<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ImpactAnalytic;
use App\Models\Project;
use App\Models\ProjectBeneficiary;
use App\Models\ProjectFeedback;
use App\Services\Contracts\AuditLoggerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    public function __construct(private AuditLoggerInterface $auditLogger) {}

    // === Projects ===
    public function index(Request $request): JsonResponse
    {
        $query = Project::withCount(['beneficiaries', 'feedback', 'impactAnalytics']);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('project_type')) $query->where('project_type', $request->project_type);
        return response()->json($query->orderBy('created_at', 'desc')->paginate(25));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_type' => 'required|in:water,road,electrification,health_facility,school,market,other',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planned,ongoing,completed,delayed,cancelled',
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
        ]);
        $project = Project::create([...$request->only(['name', 'project_type', 'location', 'start_date', 'end_date', 'status', 'description', 'budget']), 'created_by' => $request->user()->id]);
        $this->auditLogger->log('project_created', $request->user()->id, ['description' => "Project created: {$project->name}"]);
        return response()->json(['message' => 'Project created', 'data' => $project], 201);
    }

    public function show(string $projectId): JsonResponse
    {
        $project = Project::where('project_id', $projectId)->withCount(['beneficiaries', 'feedback', 'impactAnalytics'])->firstOrFail();
        return response()->json(['data' => $project]);
    }

    public function update(Request $request, string $projectId): JsonResponse
    {
        $project = Project::where('project_id', $projectId)->firstOrFail();
        $project->update($request->only(['name', 'status', 'end_date', 'description', 'budget']));
        return response()->json(['message' => 'Project updated', 'data' => $project]);
    }

    // === Beneficiaries ===
    public function getBeneficiaries(string $projectId): JsonResponse
    {
        $project = Project::where('project_id', $projectId)->firstOrFail();
        $beneficiaries = ProjectBeneficiary::where('project_id', $project->id)
            ->with(['villagerRecord:id,unique_id,full_name,household_id'])->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $beneficiaries]);
    }

    public function addBeneficiary(Request $request, string $projectId): JsonResponse
    {
        $project = Project::where('project_id', $projectId)->firstOrFail();
        $request->validate([
            'villager_record_id' => 'required|exists:villager_records,id',
            'benefit_type' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);
        $ben = ProjectBeneficiary::create([
            'project_id' => $project->id, 'villager_record_id' => $request->villager_record_id,
            'benefit_type' => $request->benefit_type, 'remarks' => $request->remarks, 'recorded_by' => $request->user()->id,
        ]);
        return response()->json(['message' => 'Beneficiary added', 'data' => $ben], 201);
    }

    // === Feedback ===
    public function getFeedback(string $projectId): JsonResponse
    {
        $project = Project::where('project_id', $projectId)->firstOrFail();
        $feedback = ProjectFeedback::where('project_id', $project->id)
            ->with(['villagerRecord:id,unique_id,full_name'])->orderBy('date_submitted', 'desc')->get();
        return response()->json(['data' => $feedback]);
    }

    public function addFeedback(Request $request, string $projectId): JsonResponse
    {
        $project = Project::where('project_id', $projectId)->firstOrFail();
        $request->validate([
            'villager_record_id' => 'required|exists:villager_records,id',
            'feedback_text' => 'required|string',
            'date_submitted' => 'required|date',
        ]);
        $fb = ProjectFeedback::create([
            'project_id' => $project->id, 'villager_record_id' => $request->villager_record_id,
            'feedback_text' => $request->feedback_text, 'date_submitted' => $request->date_submitted, 'recorded_by' => $request->user()->id,
        ]);
        return response()->json(['message' => 'Feedback recorded', 'data' => $fb], 201);
    }

    // === Impact Analytics ===
    public function getImpact(string $projectId): JsonResponse
    {
        $project = Project::where('project_id', $projectId)->firstOrFail();
        $impact = ImpactAnalytic::where('project_id', $project->id)->orderBy('date_recorded', 'desc')->get();
        return response()->json(['data' => $impact]);
    }

    public function addImpact(Request $request, string $projectId): JsonResponse
    {
        $project = Project::where('project_id', $projectId)->firstOrFail();
        $request->validate([
            'metric' => 'required|string|max:255',
            'value' => 'required|numeric',
            'date_recorded' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        $impact = ImpactAnalytic::create([
            'project_id' => $project->id, 'metric' => $request->metric,
            'value' => $request->value, 'date_recorded' => $request->date_recorded, 'notes' => $request->notes, 'recorded_by' => $request->user()->id,
        ]);
        return response()->json(['message' => 'Impact recorded', 'data' => $impact], 201);
    }
}
