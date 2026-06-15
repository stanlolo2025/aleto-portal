<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BeneficiaryList;
use App\Models\PaymentRun;
use App\Models\PaymentRunItem;
use App\Services\Contracts\AuditLoggerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentRunController extends Controller
{
    public function __construct(private AuditLoggerInterface $auditLogger) {}

    public function index(): JsonResponse
    {
        $runs = PaymentRun::with(['beneficiaryList.grant:id,grant_identifier,name', 'initiatedByUser:id,name'])
            ->orderBy('created_at', 'desc')->paginate(25);
        return response()->json($runs);
    }

    public function create(Request $request): JsonResponse
    {
        $request->validate(['beneficiary_list_id' => 'required|exists:beneficiary_lists,id']);
        $list = BeneficiaryList::with('items.villagerRecord.proxyAccount')->findOrFail($request->beneficiary_list_id);

        if ($list->status !== 'approved') {
            return response()->json(['message' => 'Only approved lists can be used for payment runs'], 422);
        }

        $run = PaymentRun::create([
            'beneficiary_list_id' => $list->id,
            'total_beneficiaries' => $list->items->count(),
            'total_amount' => $list->items->sum('grant_amount'),
            'initiated_by' => $request->user()->id,
        ]);

        foreach ($list->items as $item) {
            $villager = $item->villagerRecord;
            $bankName = $villager->proxyAccount ? $villager->proxyAccount->proxy_bank_name : ($villager->bank_name ?? '');
            $accountNumber = $villager->getEffectiveBankAccount();

            PaymentRunItem::create([
                'payment_run_id' => $run->id,
                'villager_record_id' => $villager->id,
                'amount' => $item->grant_amount,
                'bank_name' => $bankName,
                'account_number' => $accountNumber,
            ]);
        }

        $this->auditLogger->log('payment_run_created', $request->user()->id, [
            'description' => "Payment run created with {$run->total_beneficiaries} beneficiaries, total ₦{$run->total_amount}",
        ]);

        return response()->json(['message' => 'Payment run created', 'data' => $run], 201);
    }

    public function show(string $runId): JsonResponse
    {
        $run = PaymentRun::where('run_id', $runId)
            ->with(['items.villagerRecord:id,unique_id,full_name', 'beneficiaryList.grant', 'initiatedByUser:id,name'])
            ->firstOrFail();
        return response()->json(['data' => $run]);
    }

    public function markPaid(Request $request, int $itemId): JsonResponse
    {
        $request->validate(['transaction_reference' => 'required|string|max:255']);
        $item = PaymentRunItem::findOrFail($itemId);
        $item->update([
            'status' => 'paid', 'transaction_reference' => $request->transaction_reference,
            'paid_at' => now(), 'confirmed_by' => $request->user()->id,
        ]);
        $this->updateRunTotals($item->payment_run_id);
        return response()->json(['message' => 'Marked as paid', 'data' => $item]);
    }

    public function markFailed(Request $request, int $itemId): JsonResponse
    {
        $request->validate(['failure_reason' => 'required|string']);
        $item = PaymentRunItem::findOrFail($itemId);
        $item->update(['status' => 'failed', 'failure_reason' => $request->failure_reason, 'confirmed_by' => $request->user()->id]);
        $this->updateRunTotals($item->payment_run_id);
        return response()->json(['message' => 'Marked as failed', 'data' => $item]);
    }

    private function updateRunTotals(int $runId): void
    {
        $run = PaymentRun::find($runId);
        $run->paid_count = $run->items()->where('status', 'paid')->count();
        $run->failed_count = $run->items()->where('status', 'failed')->count();
        $run->paid_amount = $run->items()->where('status', 'paid')->sum('amount');
        if ($run->paid_count + $run->failed_count >= $run->total_beneficiaries) {
            $run->status = 'completed';
            $run->completed_at = now();
        } else {
            $run->status = 'in_progress';
        }
        $run->save();
    }
}
