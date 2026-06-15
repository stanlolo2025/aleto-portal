<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdultLiteracy;
use App\Models\ExamResult;
use App\Models\Scholarship;
use App\Models\StudentRegistry;
use App\Models\VillagerRecord;
use App\Services\Contracts\AuditLoggerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function __construct(private AuditLoggerInterface $auditLogger) {}

    // === Student Registry ===
    public function getStudents(Request $request): JsonResponse
    {
        $query = StudentRegistry::with(['villagerRecord:id,unique_id,full_name', 'registeredByUser:id,name']);
        if ($request->filled('enrollment_status')) $query->where('enrollment_status', $request->enrollment_status);
        if ($request->filled('school_name')) $query->where('school_name', 'like', '%' . $request->school_name . '%');
        return response()->json($query->orderBy('created_at', 'desc')->paginate(25));
    }

    public function storeStudent(Request $request): JsonResponse
    {
        $request->validate([
            'villager_record_id' => 'required|exists:villager_records,id',
            'school_name' => 'required|string|max:255',
            'class_level' => 'required|string|max:100',
            'enrollment_status' => 'required|in:enrolled,not_enrolled,dropped_out,graduated',
            'days_present' => 'nullable|integer|min:0',
            'days_absent' => 'nullable|integer|min:0',
        ]);

        $student = StudentRegistry::create([...$request->only([
            'villager_record_id', 'school_name', 'class_level', 'enrollment_status', 'days_present', 'days_absent',
        ]), 'registered_by' => $request->user()->id]);

        $villager = VillagerRecord::find($request->villager_record_id);
        $this->auditLogger->log('student_registered', $request->user()->id, [
            'villager_id' => $villager?->unique_id,
            'description' => "Student registered: {$villager?->full_name} at {$request->school_name}",
        ]);

        return response()->json(['message' => 'Student registered', 'data' => $student], 201);
    }

    public function updateStudent(Request $request, int $id): JsonResponse
    {
        $student = StudentRegistry::findOrFail($id);
        $request->validate([
            'enrollment_status' => 'sometimes|in:enrolled,not_enrolled,dropped_out,graduated',
            'days_present' => 'sometimes|integer|min:0',
            'days_absent' => 'sometimes|integer|min:0',
            'class_level' => 'sometimes|string|max:100',
        ]);
        $student->update($request->only(['enrollment_status', 'days_present', 'days_absent', 'class_level', 'school_name']));
        return response()->json(['message' => 'Student updated', 'data' => $student]);
    }

    // === Scholarships ===
    public function getScholarships(Request $request): JsonResponse
    {
        $query = Scholarship::with(['student.villagerRecord:id,unique_id,full_name', 'approvedByUser:id,name', 'createdByUser:id,name']);
        if ($request->filled('approval_status')) $query->where('approval_status', $request->approval_status);
        return response()->json($query->orderBy('created_at', 'desc')->paginate(25));
    }

    public function storeScholarship(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:student_registry,id',
            'scholarship_type' => 'required|in:government_bursary,community_grant,ngo_sponsorship,other',
            'amount' => 'required|numeric|min:0.01|max:999999999.99',
            'payment_method' => 'required|in:bank_transfer,proxy_account,mobile_wallet',
            'academic_year' => 'nullable|string|max:20',
            'remarks' => 'nullable|string',
        ]);

        $scholarship = Scholarship::create([...$request->only([
            'student_id', 'scholarship_type', 'amount', 'payment_method', 'academic_year', 'remarks',
        ]), 'created_by' => $request->user()->id]);

        return response()->json(['message' => 'Scholarship created', 'data' => $scholarship], 201);
    }

    public function approveScholarship(Request $request, int $id): JsonResponse
    {
        $scholarship = Scholarship::findOrFail($id);
        $scholarship->update(['approval_status' => 'approved', 'approved_by' => $request->user()->id, 'approved_at' => now()]);
        return response()->json(['message' => 'Scholarship approved', 'data' => $scholarship]);
    }

    public function markScholarshipPaid(Request $request, int $id): JsonResponse
    {
        $scholarship = Scholarship::findOrFail($id);
        $scholarship->update(['approval_status' => 'paid']);
        return response()->json(['message' => 'Scholarship marked as paid', 'data' => $scholarship]);
    }

    // === Exam Results ===
    public function getExamResults(Request $request): JsonResponse
    {
        $query = ExamResult::with(['student.villagerRecord:id,unique_id,full_name', 'recordedByUser:id,name']);
        if ($request->filled('student_id')) $query->where('student_id', $request->student_id);
        return response()->json($query->orderBy('exam_date', 'desc')->paginate(25));
    }

    public function storeExamResult(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:student_registry,id',
            'subject' => 'required|string|max:255',
            'score' => 'required|string|max:20',
            'grade' => 'nullable|string|max:5',
            'exam_date' => 'required|date',
            'exam_type' => 'nullable|string|max:50',
            'remarks' => 'nullable|string',
        ]);

        $result = ExamResult::create([...$request->only([
            'student_id', 'subject', 'score', 'grade', 'exam_date', 'exam_type', 'remarks',
        ]), 'recorded_by' => $request->user()->id]);

        return response()->json(['message' => 'Exam result recorded', 'data' => $result], 201);
    }

    // === Adult Literacy ===
    public function getLiteracyPrograms(Request $request): JsonResponse
    {
        $query = AdultLiteracy::with(['villagerRecord:id,unique_id,full_name', 'registeredByUser:id,name']);
        if ($request->filled('completion_status')) $query->where('completion_status', $request->completion_status);
        return response()->json($query->orderBy('created_at', 'desc')->paginate(25));
    }

    public function storeLiteracyProgram(Request $request): JsonResponse
    {
        $request->validate([
            'villager_record_id' => 'required|exists:villager_records,id',
            'program_name' => 'required|string|max:255',
            'enrollment_status' => 'required|in:enrolled,completed,dropped_out',
            'total_sessions' => 'required|integer|min:1',
            'start_date' => 'required|date',
        ]);

        $program = AdultLiteracy::create([...$request->only([
            'villager_record_id', 'program_name', 'enrollment_status', 'total_sessions', 'start_date',
        ]), 'registered_by' => $request->user()->id]);

        return response()->json(['message' => 'Literacy program enrollment created', 'data' => $program], 201);
    }

    public function updateLiteracyProgram(Request $request, int $id): JsonResponse
    {
        $program = AdultLiteracy::findOrFail($id);
        $request->validate([
            'sessions_attended' => 'sometimes|integer|min:0',
            'enrollment_status' => 'sometimes|in:enrolled,completed,dropped_out',
            'completion_status' => 'sometimes|in:in_progress,completed,incomplete',
            'end_date' => 'sometimes|date',
        ]);
        $program->update($request->only(['sessions_attended', 'enrollment_status', 'completion_status', 'end_date']));
        return response()->json(['message' => 'Program updated', 'data' => $program]);
    }
}
