<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BeneficiaryList;
use App\Models\BeneficiaryListItem;
use App\Models\Grant;
use App\Models\VillagerRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function availableReports(): JsonResponse
    {
        return response()->json(['reports' => [
            ['id' => 'beneficiaries_by_grant', 'name' => 'Beneficiaries by Grant', 'description' => 'All approved beneficiaries for a specific grant'],
            ['id' => 'villagers_without_bank', 'name' => 'Villagers Without Bank Accounts', 'description' => 'Active villagers with no bank account registered'],
            ['id' => 'households_no_grants', 'name' => 'Households Without Grants', 'description' => 'Households that have not received any grant'],
            ['id' => 'demographic_summary', 'name' => 'Demographic Summary', 'description' => 'Population breakdown by gender, age group, village'],
            ['id' => 'grant_disbursement', 'name' => 'Grant Disbursement Summary', 'description' => 'Total amounts disbursed per grant'],
            ['id' => 'villagers_by_village', 'name' => 'Villagers by Village/Ward', 'description' => 'Population distribution by location'],
        ]]);
    }

    public function generate(Request $request): JsonResponse
    {
        $request->validate(['report_id' => 'required|string', 'params' => 'nullable|array']);
        $params = $request->params ?? [];

        return match($request->report_id) {
            'beneficiaries_by_grant' => $this->beneficiariesByGrant($params),
            'villagers_without_bank' => $this->villagersWithoutBank(),
            'households_no_grants' => $this->householdsNoGrants(),
            'demographic_summary' => $this->demographicSummary(),
            'grant_disbursement' => $this->grantDisbursement(),
            'villagers_by_village' => $this->villagersByVillage(),
            default => response()->json(['message' => 'Unknown report'], 404),
        };
    }

    private function beneficiariesByGrant(array $params): JsonResponse
    {
        $grantId = $params['grant_identifier'] ?? null;
        if (!$grantId) return response()->json(['message' => 'grant_identifier required'], 422);

        $grant = Grant::where('grant_identifier', $grantId)->first();
        if (!$grant) return response()->json(['message' => 'Grant not found'], 404);

        $beneficiaries = BeneficiaryListItem::whereHas('beneficiaryList', fn($q) => $q->where('grant_id', $grant->id)->where('status', 'approved'))
            ->with('villagerRecord:id,unique_id,full_name,phone_number,bank_account_number,household_id,village')
            ->get();

        return response()->json(['title' => "Beneficiaries for: {$grant->name}", 'data' => $beneficiaries, 'total' => $beneficiaries->count()]);
    }

    private function villagersWithoutBank(): JsonResponse
    {
        $villagers = VillagerRecord::where('status', 'active')
            ->whereNull('bank_account_number')
            ->get(['id', 'unique_id', 'full_name', 'household_id', 'village', 'phone_number']);
        return response()->json(['title' => 'Villagers Without Bank Accounts', 'data' => $villagers, 'total' => $villagers->count()]);
    }

    private function householdsNoGrants(): JsonResponse
    {
        $grantedHouseholds = BeneficiaryListItem::whereHas('beneficiaryList', fn($q) => $q->where('status', 'approved'))
            ->join('villager_records', 'beneficiary_list_items.villager_record_id', '=', 'villager_records.id')
            ->pluck('villager_records.household_id')->unique();

        $allHouseholds = VillagerRecord::where('status', 'active')->select('household_id')->distinct()->pluck('household_id');
        $noGrants = $allHouseholds->diff($grantedHouseholds);

        $data = VillagerRecord::whereIn('household_id', $noGrants)->where('status', 'active')
            ->select('household_id', 'village', DB::raw('COUNT(*) as members'))
            ->groupBy('household_id', 'village')->get();

        return response()->json(['title' => 'Households Without Grants', 'data' => $data, 'total' => $data->count()]);
    }

    private function demographicSummary(): JsonResponse
    {
        $byGender = VillagerRecord::where('status', 'active')->select('gender', DB::raw('COUNT(*) as count'))->groupBy('gender')->get();
        $byVillage = VillagerRecord::where('status', 'active')->whereNotNull('village')
            ->select('village', DB::raw('COUNT(*) as count'))->groupBy('village')->get();
        $total = VillagerRecord::where('status', 'active')->count();

        return response()->json(['title' => 'Demographic Summary', 'data' => [
            'total_active' => $total, 'by_gender' => $byGender, 'by_village' => $byVillage,
        ]]);
    }

    private function grantDisbursement(): JsonResponse
    {
        $grants = Grant::withCount(['beneficiaryLists as approved_lists' => fn($q) => $q->where('status', 'approved')])
            ->get(['id', 'grant_identifier', 'name', 'amount', 'status']);

        $data = $grants->map(function ($g) {
            $totalBeneficiaries = BeneficiaryListItem::whereHas('beneficiaryList', fn($q) => $q->where('grant_id', $g->id)->where('status', 'approved'))->count();
            $totalAmount = BeneficiaryListItem::whereHas('beneficiaryList', fn($q) => $q->where('grant_id', $g->id)->where('status', 'approved'))->sum('grant_amount');
            return [...$g->toArray(), 'total_beneficiaries' => $totalBeneficiaries, 'total_disbursed' => $totalAmount];
        });

        return response()->json(['title' => 'Grant Disbursement Summary', 'data' => $data]);
    }

    private function villagersByVillage(): JsonResponse
    {
        $data = VillagerRecord::where('status', 'active')
            ->select('village', 'ward', DB::raw('COUNT(*) as count'))
            ->groupBy('village', 'ward')->orderBy('village')->get();
        return response()->json(['title' => 'Villagers by Village/Ward', 'data' => $data]);
    }
}
