<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Report;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    use ApiResponse;

    public function submitReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'target_type' => ['required', 'string', 'in:listing,user,message'],
            'target_id' => ['required', 'integer'],
            'reason_code' => ['required', 'string', 'in:spam,fraud,prohibited_item,offensive,duplicate'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        $report = Report::create([
            'reporter_id' => $request->user()->id,
            'target_type' => $validated['target_type'],
            'target_id' => $validated['target_id'],
            'reason_code' => $validated['reason_code'],
            'details' => $validated['details'] ?? null,
            'status' => 'open',
        ]);

        return $this->successResponse($report, 'Report submitted to moderation queue', 201);
    }

    public function tickets(Request $request): JsonResponse
    {
        $tickets = SupportTicket::where('user_id', $request->user()->id)
            ->latest('id')
            ->get();

        return $this->successResponse($tickets);
    }

    public function createTicket(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:5000'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high,urgent'],
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $request->user()->id,
            'subject' => $validated['subject'],
            'priority' => $validated['priority'] ?? 'medium',
            'status' => 'open',
        ]);

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'author_id' => $request->user()->id,
            'body' => $validated['message'],
        ]);

        return $this->successResponse($ticket->load('messages'), 'Support ticket created', 201);
    }

    public function showTicket(Request $request, SupportTicket $ticket): JsonResponse
    {
        if ($ticket->user_id !== $request->user()->id && !$request->user()->hasRole('support') && !$request->user()->hasRole('admin')) {
            abort(403);
        }

        return $this->successResponse($ticket->load('messages.author:id,name'));
    }

    public function sendMessage(Request $request, SupportTicket $ticket): JsonResponse
    {
        if ($ticket->user_id !== $request->user()->id && !$request->user()->hasRole('support') && !$request->user()->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message = SupportMessage::create([
            'ticket_id' => $ticket->id,
            'author_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return $this->successResponse($message->load('author:id,name'), 'Message added to ticket', 201);
    }
}
