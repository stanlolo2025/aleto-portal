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

class PublicController extends Controller
{
    // Public search by Unique ID, NIN, or Phone
    public function search(Request $request): JsonResponse
    {
        $request->validate(['unique_id' => 'required|string']);

        $searchValue = $request->unique_id;

        // Try unique_id first
        $villager = VillagerRecord::where('unique_id', $searchValue)->first();

        // If not found, try matching by phone number (encrypted field - need to scan)
        if (!$villager) {
            $allRecords = VillagerRecord::all();
            foreach ($allRecords as $record) {
                if ($record->phone_number === $searchValue || $record->nin === $searchValue) {
                    $villager = $record;
                    break;
                }
            }
        }

        if (!$villager) {
            return response()->json(['message' => 'No record found with this ID'], 404);
        }

        return response()->json(['data' => [
            'unique_id' => $villager->unique_id,
            'full_name' => $villager->full_name,
            'household_id' => $villager->household_id,
            'status' => $villager->status,
            'gender' => $villager->gender,
            'village' => $villager->village,
            'ward' => $villager->ward,
            'created_at' => $villager->created_at,
        ]]);
    }

    // Transparency dashboard stats
    public function stats(): JsonResponse
    {
        $total = VillagerRecord::count();
        $active = VillagerRecord::where('status', 'active')->count();
        $deceased = VillagerRecord::where('status', 'deceased')->count();
        $archived = VillagerRecord::where('status', 'archived')->count();

        $grantCycles = Grant::select('grant_identifier', 'name')
            ->withCount(['beneficiaryLists as beneficiaries_count' => function ($q) {
                $q->where('status', 'approved')
                    ->join('beneficiary_list_items', 'beneficiary_lists.id', '=', 'beneficiary_list_items.beneficiary_list_id')
                    ->select(DB::raw('count(distinct beneficiary_list_items.villager_record_id)'));
            }])
            ->get();

        // Simplified grant beneficiary count
        $grantStats = Grant::all()->map(function ($grant) {
            $count = BeneficiaryListItem::whereHas('beneficiaryList', function ($q) use ($grant) {
                $q->where('grant_id', $grant->id)->where('status', 'approved');
            })->count();
            return ['name' => $grant->name, 'beneficiaries' => $count];
        })->filter(fn($g) => $g['beneficiaries'] > 0)->values();

        return response()->json([
            'total_members' => $total,
            'active' => $active,
            'deceased' => $deceased,
            'archived' => $archived,
            'grant_stats' => $grantStats,
        ]);
    }

    // Public summary reports
    public function reports(): JsonResponse
    {
        $recentGrants = Grant::where('status', 'active')
            ->orWhere('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['grant_identifier', 'name', 'status', 'created_at']);

        return response()->json(['recent_grants' => $recentGrants]);
    }

    // Public members grid (photo, name, ID only)
    public function members(Request $request): JsonResponse
    {
        $query = VillagerRecord::where('status', 'active')
            ->select('unique_id', 'full_name', 'passport_photo', 'village', 'created_at');

        if ($request->filled('search')) {
            $query->where('full_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('village')) {
            $query->where('village', $request->village);
        }

        $members = $query->orderBy('full_name')->paginate($request->get('per_page', 24));

        return response()->json($members);
    }
}
