<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Announcement::active()->with('createdByUser:id,name')->orderBy('created_at', 'desc')->get());
    }

    public function all(): JsonResponse
    {
        return response()->json(Announcement::with('createdByUser:id,name')->orderBy('created_at', 'desc')->paginate(25));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,grant,registration,event,urgent',
            'expires_at' => 'nullable|date|after:today',
        ]);
        $ann = Announcement::create([...$request->only(['title', 'content', 'type', 'expires_at']), 'created_by' => $request->user()->id]);
        return response()->json(['message' => 'Announcement created', 'data' => $ann], 201);
    }

    public function deactivate(int $id): JsonResponse
    {
        Announcement::findOrFail($id)->update(['is_active' => false]);
        return response()->json(['message' => 'Announcement deactivated']);
    }
}
