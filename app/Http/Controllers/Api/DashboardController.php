<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BeneficiaryList;
use App\Models\FlaggedRegistration;
use App\Models\Grant;
use App\Models\GrantHistory;
use App\Models\HealthGrant;
use App\Models\Project;
use App\Models\Scholarship;
use App\Models\VillagerRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $totalVillagers = VillagerRecord::count();
        $activeVillagers = VillagerRecord::where('status', 'active')->count();
        $deceasedVillagers = VillagerRecord::where('status', 'deceased')->count();
        $archivedVillagers = VillagerRecord::where('status', 'archived')->count();

        $totalGrants = Grant::count();
        $activeGrants = Grant::where('status', 'active')->count();
        $totalDisbursed = GrantHistory::where('action_type', 'payment')->sum('amount');

        $pendingFlags = FlaggedRegistration::where('resolution', 'pending')->count();
        $totalProjects = Project::count();
        $ongoingProjects = Project::where('status', 'ongoing')->count();

        $pendingScholarships = Scholarship::where('approval_status', 'pending')->count();
        $pendingHealthGrants = HealthGrant::where('approval_status', 'pending')->count();

        // Gender distribution
        $genderDistribution = VillagerRecord::select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')->pluck('count', 'gender')->toArray();

        // Registration trend (last 6 months)
        $registrationTrend = VillagerRecord::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')->toArray();

        // Grant distribution by status
        $grantsByStatus = Grant::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')->pluck('count', 'status')->toArray();

        // Recent registrations
        $recentRegistrations = VillagerRecord::with('registeredByUser:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'unique_id', 'full_name', 'status', 'created_at', 'registered_by']);

        // Recent grant activity
        $recentGrantActivity = GrantHistory::with(['grant:id,grant_identifier,name', 'performedByUser:id,name'])
            ->orderBy('action_date', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => [
                'total_villagers' => $totalVillagers,
                'active_villagers' => $activeVillagers,
                'deceased_villagers' => $deceasedVillagers,
                'archived_villagers' => $archivedVillagers,
                'total_grants' => $totalGrants,
                'active_grants' => $activeGrants,
                'total_disbursed' => $totalDisbursed,
                'pending_flags' => $pendingFlags,
                'total_projects' => $totalProjects,
                'ongoing_projects' => $ongoingProjects,
                'pending_scholarships' => $pendingScholarships,
                'pending_health_grants' => $pendingHealthGrants,
            ],
            'charts' => [
                'gender_distribution' => $genderDistribution,
                'registration_trend' => $registrationTrend,
                'grants_by_status' => $grantsByStatus,
            ],
            'recent_registrations' => $recentRegistrations,
            'recent_grant_activity' => $recentGrantActivity,
        ]);
    }
}
