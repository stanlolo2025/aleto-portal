<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function inbox(Request $request): JsonResponse
    {
        $messages = Message::where('receiver_id', $request->user()->id)
            ->with('sender:id,name,role')
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        return response()->json($messages);
    }

    public function sent(Request $request): JsonResponse
    {
        $messages = Message::where('sender_id', $request->user()->id)
            ->with('receiver:id,name,role')
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        return response()->json($messages);
    }

    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'body' => $request->body,
        ]);

        return response()->json(['message' => 'Message sent', 'data' => $message], 201);
    }

    public function read(Request $request, int $id): JsonResponse
    {
        $message = Message::where('receiver_id', $request->user()->id)->findOrFail($id);
        $message->update(['is_read' => true, 'read_at' => now()]);
        $message->load('sender:id,name,role');
        return response()->json(['data' => $message]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = Message::where('receiver_id', $request->user()->id)->where('is_read', false)->count();
        return response()->json(['count' => $count]);
    }
}
