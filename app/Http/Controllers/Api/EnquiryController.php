<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    // Public - submit enquiry
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'required|string|max:20',
            'type' => 'required|in:complaint,enquiry,suggestion,grant_status',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $enquiry = Enquiry::create($request->only(['full_name', 'email', 'phone', 'type', 'subject', 'message']));

        return response()->json([
            'message' => 'Your enquiry has been submitted successfully.',
            'ticket_id' => $enquiry->ticket_id,
        ], 201);
    }

    // Public - track ticket
    public function track(Request $request): JsonResponse
    {
        $request->validate(['ticket_id' => 'required|string']);
        $enquiry = Enquiry::where('ticket_id', $request->ticket_id)->first();

        if (!$enquiry) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        return response()->json(['data' => [
            'ticket_id' => $enquiry->ticket_id,
            'subject' => $enquiry->subject,
            'type' => $enquiry->type,
            'status' => $enquiry->status,
            'submitted_at' => $enquiry->created_at->format('d/m/Y H:i'),
            'response' => $enquiry->admin_response,
            'responded_at' => $enquiry->responded_at?->format('d/m/Y H:i'),
        ]]);
    }

    // Admin - list all
    public function index(Request $request): JsonResponse
    {
        $query = Enquiry::query();
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('type')) $query->where('type', $request->type);
        return response()->json($query->orderBy('created_at', 'desc')->paginate(25));
    }

    // Admin - respond
    public function respond(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'admin_response' => 'required|string',
            'status' => 'required|in:in_progress,resolved,closed',
        ]);

        $enquiry = Enquiry::findOrFail($id);
        $enquiry->update([
            'admin_response' => $request->admin_response,
            'status' => $request->status,
            'responded_by' => $request->user()->id,
            'responded_at' => now(),
        ]);

        return response()->json(['message' => 'Response saved', 'data' => $enquiry]);
    }
}
