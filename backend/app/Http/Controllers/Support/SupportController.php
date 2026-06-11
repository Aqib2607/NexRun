<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\ApiController;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $tickets = SupportTicket::forCustomer($request->user()->id)
            ->with('messages')
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        $data = $tickets->through(fn($t) => [
            'id'             => $t->id,
            'ticket_number'  => $t->ticket_number,
            'subject'        => $t->subject,
            'priority'       => $t->priority,
            'status'         => $t->status,
            'messages_count' => $t->messages->count(),
            'created_at'     => $t->created_at?->toIso8601String(),
        ]);

        return $this->paginated($data);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject'     => 'required|string|max:200',
            'description' => 'required|string|max:5000',
            'priority'    => 'sometimes|in:low,medium,high,critical',
            'order_id'    => 'nullable|exists:orders,id',
        ]);

        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'customer_id'   => $request->user()->id,
            'subject'       => $data['subject'],
            'description'   => $data['description'],
            'priority'      => $data['priority'] ?? 'medium',
            'order_id'      => $data['order_id'] ?? null,
        ]);

        // Add initial message
        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $request->user()->id,
            'message'   => $data['description'],
        ]);

        return $this->created([
            'ticket_number' => $ticket->ticket_number,
            'id'            => $ticket->id,
        ], 'Support ticket created.');
    }

    public function show(SupportTicket $ticket, Request $request): JsonResponse
    {
        if ($ticket->customer_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403, 'Access denied.');
        }

        $ticket->load(['messages.sender', 'assignee', 'order']);

        return $this->success([
            'id'             => $ticket->id,
            'ticket_number'  => $ticket->ticket_number,
            'subject'        => $ticket->subject,
            'description'    => $ticket->description,
            'priority'       => $ticket->priority,
            'status'         => $ticket->status,
            'assigned_to'    => $ticket->assignee?->full_name,
            'order'          => $ticket->order ? ['id' => $ticket->order->id, 'order_number' => $ticket->order->order_number] : null,
            'messages'       => $ticket->messages->map(fn($m) => [
                'id'         => $m->id,
                'sender'     => $m->sender->full_name,
                'message'    => $m->message,
                'attachments' => $m->attachments,
                'created_at' => $m->created_at?->toIso8601String(),
            ]),
            'created_at'     => $ticket->created_at?->toIso8601String(),
        ]);
    }

    public function addMessage(SupportTicket $ticket, Request $request): JsonResponse
    {
        if ($ticket->customer_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403, 'Access denied.');
        }

        $data = $request->validate([
            'message'     => 'required|string|max:5000',
            'attachments' => 'nullable|array',
        ]);

        $message = SupportMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $request->user()->id,
            'message'   => $data['message'],
            'attachments' => $data['attachments'] ?? null,
        ]);

        // Reopen ticket if it was resolved
        if ($ticket->status === 'resolved') {
            $ticket->update(['status' => 'open']);
        }

        return $this->created([
            'id'         => $message->id,
            'message'    => $message->message,
            'created_at' => $message->created_at?->toIso8601String(),
        ]);
    }

    public function close(SupportTicket $ticket, Request $request): JsonResponse
    {
        if ($ticket->customer_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403, 'Access denied.');
        }

        $ticket->update(['status' => 'closed']);
        return $this->success(null, 'Ticket closed.');
    }
}
