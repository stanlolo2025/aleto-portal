<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClinicVisit;
use App\Models\HealthGrant;
use App\Models\MedicalRecord;
use App\Models\PreventiveCareAlert;
use App\Models\VillagerRecord;
use App\Services\Contracts\AuditLoggerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthcareController extends Controller
{
    public function __construct(private AuditLoggerInterface $auditLogger) {}

    // === Medical Records ===
    public function getMedicalRecords(Request $request): JsonResponse
    {
        $query = MedicalRecord::with(['villagerRecord:id,unique_id,full_name', 'recordedByUser:id,name']);
        if ($request->filled('villager_id')) {
            $villager = VillagerRecord::where('unique_id', $request->villager_id)->first();
            if ($villager) $query->where('villager_record_id', $villager->id);
        }
        return response()->json($query->orderBy('created_at', 'desc')->paginate(25));
    }

    public function storeMedicalRecord(Request $request): JsonResponse
    {
        $request->validate([
            'villager_record_id' => 'required|exists:villager_records,id',
            'vaccination_type' => 'nullable|string|max:255',
            'vaccination_date' => 'nullable|date',
            'vaccination_status' => 'nullable|in:completed,partial,pending',
            'chronic_conditions' => 'nullable|string',
            'disability_status' => 'nullable|boolean',
            'allergies' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $record = MedicalRecord::create([...$request->only([
            'villager_record_id', 'vaccination_type', 'vaccination_date',
            'vaccination_status', 'chronic_conditions', 'disability_status', 'allergies', 'notes',
        ]), 'recorded_by' => $request->user()->id]);

        $villager = VillagerRecord::find($request->villager_record_id);
        $this->auditLogger->log('medical_record_created', $request->user()->id, [
            'villager_id' => $villager?->unique_id,
            'description' => "Medical record added for {$villager?->full_name}",
        ]);

        return response()->json(['message' => 'Medical record created', 'data' => $record], 201);
    }

    // === Clinic Visits ===
    public function getClinicVisits(Request $request): JsonResponse
    {
        $query = ClinicVisit::with(['villagerRecord:id,unique_id,full_name', 'recordedByUser:id,name']);
        if ($request->filled('villager_id')) {
            $villager = VillagerRecord::where('unique_id', $request->villager_id)->first();
            if ($villager) $query->where('villager_record_id', $villager->id);
        }
        return response()->json($query->orderBy('visit_date', 'desc')->paginate(25));
    }

    public function storeClinicVisit(Request $request): JsonResponse
    {
        $request->validate([
            'villager_record_id' => 'required|exists:villager_records,id',
            'clinic_name' => 'required|string|max:255',
            'clinic_location' => 'nullable|string|max:255',
            'visit_date' => 'required|date',
            'reason' => 'required|in:diagnosis,check_up,emergency,follow_up,vaccination,other',
            'treatment' => 'nullable|string',
            'health_worker' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $visit = ClinicVisit::create([...$request->only([
            'villager_record_id', 'clinic_name', 'clinic_location', 'visit_date',
            'reason', 'treatment', 'health_worker', 'notes',
        ]), 'recorded_by' => $request->user()->id]);

        $villager = VillagerRecord::find($request->villager_record_id);
        $this->auditLogger->log('clinic_visit_recorded', $request->user()->id, [
            'villager_id' => $villager?->unique_id,
            'description' => "Clinic visit recorded for {$villager?->full_name} at {$request->clinic_name}",
        ]);

        return response()->json(['message' => 'Clinic visit recorded', 'data' => $visit], 201);
    }

    // === Preventive Care Alerts ===
    public function getAlerts(Request $request): JsonResponse
    {
        $query = PreventiveCareAlert::with(['villagerRecord:id,unique_id,full_name', 'createdByUser:id,name']);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('alert_type')) $query->where('alert_type', $request->alert_type);
        if ($request->filled('villager_id')) {
            $villager = VillagerRecord::where('unique_id', $request->villager_id)->first();
            if ($villager) $query->where('villager_record_id', $villager->id);
        }
        return response()->json($query->orderBy('due_date', 'asc')->paginate(25));
    }

    public function storeAlert(Request $request): JsonResponse
    {
        $request->validate([
            'villager_record_id' => 'required|exists:villager_records,id',
            'alert_type' => 'required|in:immunization,maternal,chronic,follow_up,other',
            'description' => 'required|string|max:500',
            'due_date' => 'required|date',
        ]);

        $alert = PreventiveCareAlert::create([...$request->only([
            'villager_record_id', 'alert_type', 'description', 'due_date',
        ]), 'created_by' => $request->user()->id]);

        return response()->json(['message' => 'Alert created', 'data' => $alert], 201);
    }

    public function completeAlert(Request $request, int $id): JsonResponse
    {
        $alert = PreventiveCareAlert::findOrFail($id);
        $alert->update(['status' => 'completed', 'completed_date' => now()]);
        return response()->json(['message' => 'Alert completed', 'data' => $alert]);
    }

    // === Health Grants ===
    public function getHealthGrants(Request $request): JsonResponse
    {
        $query = HealthGrant::with(['villagerRecord:id,unique_id,full_name', 'approvedByUser:id,name', 'createdByUser:id,name']);
        if ($request->filled('approval_status')) $query->where('approval_status', $request->approval_status);
        if ($request->filled('grant_type')) $query->where('grant_type', $request->grant_type);
        return response()->json($query->orderBy('created_at', 'desc')->paginate(25));
    }

    public function storeHealthGrant(Request $request): JsonResponse
    {
        $request->validate([
            'villager_record_id' => 'required|exists:villager_records,id',
            'grant_type' => 'required|in:medical_subsidy,elderly_health_stipend,disability_support,maternal_care,other',
            'amount' => 'required|numeric|min:0.01|max:999999999.99',
            'payment_method' => 'required|in:bank_transfer,proxy_account,mobile_wallet',
            'remarks' => 'nullable|string',
        ]);

        $grant = HealthGrant::create([...$request->only([
            'villager_record_id', 'grant_type', 'amount', 'payment_method', 'remarks',
        ]), 'created_by' => $request->user()->id]);

        $villager = VillagerRecord::find($request->villager_record_id);
        $this->auditLogger->log('health_grant_created', $request->user()->id, [
            'villager_id' => $villager?->unique_id,
            'description' => "Health grant ({$request->grant_type}) created for {$villager?->full_name}: ₦{$request->amount}",
        ]);

        return response()->json(['message' => 'Health grant created', 'data' => $grant], 201);
    }

    public function approveHealthGrant(Request $request, int $id): JsonResponse
    {
        $grant = HealthGrant::findOrFail($id);
        $grant->update([
            'approval_status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        $this->auditLogger->log('health_grant_approved', $request->user()->id, [
            'villager_id' => $grant->villagerRecord?->unique_id,
            'description' => "Health grant #{$id} approved",
        ]);

        return response()->json(['message' => 'Health grant approved', 'data' => $grant]);
    }

    public function markHealthGrantPaid(Request $request, int $id): JsonResponse
    {
        $grant = HealthGrant::findOrFail($id);
        $grant->update(['approval_status' => 'paid']);
        return response()->json(['message' => 'Health grant marked as paid', 'data' => $grant]);
    }
}
