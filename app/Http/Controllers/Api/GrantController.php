<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BeneficiaryList;
use App\Models\BeneficiaryListItem;
use App\Models\Grant;
use App\Models\GrantHistory;
use App\Models\VillagerRecord;
use App\Services\Contracts\AuditLoggerInterface;
use App\Services\Contracts\FraudDetectorInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrantController extends Controller
{
    public function __construct(
        private AuditLoggerInterface $auditLogger,
        private FraudDetectorInterface $fraudDetector,
    ) {}

    public function index(): JsonResponse
    {
        $grants = Grant::withCount('beneficiaryLists')->orderBy('created_at', 'desc')->paginate(25);
        return response()->json($grants);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'grant_identifier' => 'required|string|unique:grants,grant_identifier',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01|max:999999999.99',
        ]);

        $grant = Grant::create($request->only(['grant_identifier', 'name', 'description', 'amount', 'status']));

        return response()->json(['message' => 'Grant created', 'data' => $grant], 201);
    }

    public function getBeneficiaryLists(string $grantId): JsonResponse
    {
        $grant = Grant::where('grant_identifier', $grantId)->firstOrFail();

        $lists = BeneficiaryList::where('grant_id', $grant->id)
            ->with(['items.villagerRecord:id,unique_id,full_name,status'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $lists]);
    }

    public function getEligible(Request $request, string $grantId): JsonResponse
    {
        $grant = Grant::where('grant_identifier', $grantId)->firstOrFail();

        $approvedVillagerIds = BeneficiaryListItem::whereHas('beneficiaryList', function ($q) use ($grant) {
            $q->where('grant_id', $grant->id)->where('status', 'approved');
        })->pluck('villager_record_id');

        $query = VillagerRecord::active()->whereNotIn('id', $approvedVillagerIds);

        if ($request->filled('age_min')) {
            $query->where('date_of_birth', '<=', now()->subYears((int)$request->age_min)->format('Y-m-d'));
        }
        if ($request->filled('age_max')) {
            $query->where('date_of_birth', '>=', now()->subYears((int)$request->age_max)->format('Y-m-d'));
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('village')) {
            $query->where('village', $request->village);
        }
        if ($request->filled('ward')) {
            $query->where('ward', $request->ward);
        }
        if ($request->filled('education_level')) {
            $query->where('education_level', $request->education_level);
        }
        if ($request->filled('marital_status')) {
            $query->where('marital_status', $request->marital_status);
        }

        $eligible = $query->orderBy('full_name')
            ->get(['id', 'unique_id', 'full_name', 'date_of_birth', 'gender', 'household_id', 'village', 'ward', 'bank_name', 'bank_account_number', 'status']);

        return response()->json(['data' => $eligible]);
    }

    public function createBeneficiaryList(Request $request, string $grantId): JsonResponse
    {
        $request->validate([
            'villager_ids' => 'required|array|min:1',
            'villager_ids.*' => 'exists:villager_records,id',
            'grant_amount' => 'required|numeric|min:0.01|max:999999999.99',
        ]);

        $grant = Grant::where('grant_identifier', $grantId)->firstOrFail();

        return DB::transaction(function () use ($request, $grant) {
            $list = BeneficiaryList::create([
                'grant_id' => $grant->id,
                'status' => 'pending_review',
                'created_by' => $request->user()->id,
            ]);

            foreach ($request->villager_ids as $villagerId) {
                BeneficiaryListItem::create([
                    'beneficiary_list_id' => $list->id,
                    'villager_record_id' => $villagerId,
                    'grant_amount' => $request->grant_amount,
                    'created_at' => now(),
                ]);
            }

            // Run duplicate check on selected beneficiaries
            // (Check if any two share phone/bank/NIN)
            $this->runBeneficiaryDuplicateCheck($list);

            $list->load('items.villagerRecord');

            return response()->json([
                'message' => 'Beneficiary list created',
                'data' => $list,
            ], 201);
        });
    }

    public function approveBeneficiaryList(Request $request, string $grantId): JsonResponse
    {
        $request->validate(['list_id' => 'required|exists:beneficiary_lists,id']);

        $list = BeneficiaryList::findOrFail($request->list_id);

        if ($list->hasUnresolvedFlags()) {
            return response()->json([
                'message' => 'Cannot approve: unresolved duplicate flags exist. Please review flagged items.',
            ], 422);
        }

        $list->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        $grant = $list->grant;

        // Record approval in grant history for each beneficiary
        foreach ($list->items as $item) {
            GrantHistory::create([
                'grant_id' => $grant->id,
                'villager_record_id' => $item->villager_record_id,
                'action_type' => 'approval',
                'action_date' => now(),
                'amount' => $item->grant_amount,
                'performed_by' => $request->user()->id,
                'remarks' => "Beneficiary approved in list #{$list->id}",
            ]);
        }

        $this->auditLogger->log('grant_approved', $request->user()->id, [
            'description' => "Beneficiary list approved for grant: {$grant->grant_identifier}",
            'metadata' => [
                'grant_identifier' => $grant->grant_identifier,
                'beneficiary_count' => $list->items()->count(),
            ],
        ]);

        return response()->json(['message' => 'Beneficiary list approved', 'data' => $list]);
    }

    public function exportList(Request $request, string $grantId, string $format): mixed
    {
        // Handle auth via query token for downloads opened in new tabs
        if ($request->has('token')) {
            $token = \Laravel\Sanctum\PersonalAccessToken::findToken($request->token);
            if (!$token) {
                return response('Unauthorized. Invalid or expired token.', 401);
            }
            // Token is valid, proceed
        } else {
            return response('Unauthorized. Token required.', 401);
        }

        $grant = Grant::where('grant_identifier', $grantId)->firstOrFail();
        $list = BeneficiaryList::where('grant_id', $grant->id)
            ->where('status', 'approved')
            ->latest()
            ->firstOrFail();

        $items = $list->items()->with('villagerRecord.proxyAccount')->get();

        if ($format === 'csv') {
            return response()->streamDownload(function () use ($items) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Unique ID', 'Full Name', 'Bank Name', 'Bank Account', 'Grant Amount']);

                foreach ($items as $item) {
                    $villager = $item->villagerRecord;
                    $bankName = $villager->proxyAccount ? $villager->proxyAccount->proxy_bank_name : ($villager->bank_name ?? 'N/A');
                    fputcsv($handle, [
                        $villager->unique_id,
                        $villager->full_name,
                        $bankName,
                        $villager->getEffectiveBankAccount() ?? 'N/A',
                        $item->grant_amount,
                    ]);
                }
                fclose($handle);
            }, "beneficiaries_{$grantId}.csv", ['Content-Type' => 'text/csv']);
        }

        // PDF format - generate printable HTML
        $grant = Grant::where('grant_identifier', $grantId)->first();
        $html = view('exports.beneficiary-list', [
            'items' => $items,
            'grant' => $grant,
            'exportDate' => now()->format('d/m/Y H:i'),
        ])->render();

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            return $pdf->download("beneficiaries_{$grantId}.pdf");
        }

        // Fallback: return printable HTML
        return response($html, 200, ['Content-Type' => 'text/html']);
    }

    private function runBeneficiaryDuplicateCheck(BeneficiaryList $list): void
    {
        $items = $list->items()->with('villagerRecord')->get();

        // Check for duplicates among selected beneficiaries
        $phones = [];
        $banks = [];
        $nins = [];

        foreach ($items as $item) {
            $v = $item->villagerRecord;

            if ($v->phone_number) {
                if (in_array($v->phone_number, $phones)) {
                    $item->update(['duplicate_flagged' => true]);
                }
                $phones[] = $v->phone_number;
            }

            if ($v->bank_account_number) {
                if (in_array($v->bank_account_number, $banks)) {
                    $item->update(['duplicate_flagged' => true]);
                }
                $banks[] = $v->bank_account_number;
            }

            if ($v->nin) {
                if (in_array($v->nin, $nins)) {
                    $item->update(['duplicate_flagged' => true]);
                }
                $nins[] = $v->nin;
            }
        }
    }

    public function getGrantHistory(string $grantId): JsonResponse
    {
        $grant = Grant::where('grant_identifier', $grantId)->firstOrFail();

        $history = GrantHistory::where('grant_id', $grant->id)
            ->with(['villagerRecord:id,unique_id,full_name', 'performedByUser:id,name'])
            ->orderBy('action_date', 'desc')
            ->paginate(50);

        return response()->json($history);
    }

    public function getAllGrantHistory(Request $request): JsonResponse
    {
        $query = GrantHistory::with([
            'grant:id,grant_identifier,name',
            'villagerRecord:id,unique_id,full_name',
            'performedByUser:id,name',
        ]);

        if ($request->filled('grant_id')) {
            $grant = Grant::where('grant_identifier', $request->grant_id)->first();
            if ($grant) $query->where('grant_id', $grant->id);
        }

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->where('action_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('action_date', '<=', $request->date_to . ' 23:59:59');
        }

        $total = (clone $query)->count();
        $payments = (clone $query)->where('action_type', 'payment')->count();
        $totalAmount = (clone $query)->whereNotNull('amount')->sum('amount');
        $cancellations = (clone $query)->where('action_type', 'cancellation')->count();

        $history = $query->orderBy('action_date', 'desc')->paginate(50);

        $result = $history->toArray();
        $result['summary'] = [
            'total' => $total,
            'payments' => $payments,
            'total_amount' => number_format($totalAmount, 2),
            'cancellations' => $cancellations,
        ];

        return response()->json($result);
    }

    public function exportGrantHistory(Request $request)
    {
        $query = GrantHistory::with([
            'grant:id,grant_identifier,name',
            'villagerRecord:id,unique_id,full_name',
            'performedByUser:id,name',
        ]);

        if ($request->filled('grant_id')) {
            $grant = Grant::where('grant_identifier', $request->grant_id)->first();
            if ($grant) $query->where('grant_id', $grant->id);
        }
        if ($request->filled('action_type')) $query->where('action_type', $request->action_type);
        if ($request->filled('payment_method')) $query->where('payment_method', $request->payment_method);
        if ($request->filled('date_from')) $query->where('action_date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->where('action_date', '<=', $request->date_to . ' 23:59:59');

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['History ID', 'Date', 'Grant', 'Action Type', 'Villager Name', 'Villager ID', 'Amount', 'Payment Method', 'Transaction Ref', 'Performed By', 'Remarks']);

            $query->orderBy('action_date', 'desc')->chunk(500, function ($records) use ($handle) {
                foreach ($records as $h) {
                    fputcsv($handle, [
                        $h->history_id,
                        $h->action_date?->toDateTimeString(),
                        $h->grant?->grant_identifier,
                        $h->action_type,
                        $h->villagerRecord?->full_name ?? '',
                        $h->villagerRecord?->unique_id ?? '',
                        $h->amount,
                        $h->payment_method,
                        $h->transaction_reference,
                        $h->performedByUser?->name ?? '',
                        $h->remarks,
                    ]);
                }
            });
            fclose($handle);
        }, 'grant_history_export.csv', ['Content-Type' => 'text/csv']);
    }

    public function addGrantHistory(Request $request, string $grantId): JsonResponse
    {
        $request->validate([
            'villager_record_id' => 'nullable|exists:villager_records,id',
            'action_type' => 'required|in:registration,approval,payment,update,cancellation',
            'amount' => 'nullable|numeric|min:0.01|max:999999999.99',
            'payment_method' => 'nullable|in:bank_transfer,proxy_account,mobile_wallet',
            'transaction_reference' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $grant = Grant::where('grant_identifier', $grantId)->firstOrFail();

        $history = GrantHistory::create([
            'grant_id' => $grant->id,
            'villager_record_id' => $request->villager_record_id,
            'action_type' => $request->action_type,
            'action_date' => now(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_reference' => $request->transaction_reference,
            'performed_by' => $request->user()->id,
            'remarks' => $request->remarks,
        ]);

        $this->auditLogger->log('grant_history', $request->user()->id, [
            'villager_id' => $request->villager_record_id ? VillagerRecord::find($request->villager_record_id)?->unique_id : null,
            'description' => "Grant history recorded: {$request->action_type} for grant {$grantId}",
            'metadata' => [
                'grant_identifier' => $grantId,
                'action_type' => $request->action_type,
                'amount' => $request->amount,
            ],
        ]);

        return response()->json(['message' => 'History recorded', 'data' => $history], 201);
    }
}
