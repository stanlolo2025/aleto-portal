<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VillagerRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HouseholdController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = VillagerRecord::select('household_id', 'village', 'ward',
            DB::raw('COUNT(*) as member_count'),
            DB::raw('SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_count')
        )->groupBy('household_id', 'village', 'ward');

        if ($request->filled('village')) $query->where('village', $request->village);
        if ($request->filled('ward')) $query->where('ward', $request->ward);
        if ($request->filled('search')) $query->where('household_id', 'like', '%' . $request->search . '%');

        $households = $query->orderBy('household_id')->paginate(25);
        return response()->json($households);
    }

    public function show(string $householdId): JsonResponse
    {
        $members = VillagerRecord::where('household_id', $householdId)
            ->with('registeredByUser:id,name')
            ->orderBy('full_name')
            ->get();

        $stats = [
            'total_members' => $members->count(),
            'active' => $members->where('status', 'active')->count(),
            'deceased' => $members->where('status', 'deceased')->count(),
            'archived' => $members->where('status', 'archived')->count(),
        ];

        return response()->json(['members' => $members, 'stats' => $stats, 'household_id' => $householdId]);
    }

    public function villages(): JsonResponse
    {
        $villages = VillagerRecord::select('village')->distinct()->whereNotNull('village')->orderBy('village')->pluck('village');
        $wards = VillagerRecord::select('ward')->distinct()->whereNotNull('ward')->orderBy('ward')->pluck('ward');
        return response()->json(['villages' => $villages, 'wards' => $wards]);
    }
}
