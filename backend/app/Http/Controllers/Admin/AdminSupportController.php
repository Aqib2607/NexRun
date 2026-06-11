<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSupportController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = SupportTicket::with(['customer', 'assignee']);
        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('priority')) $query->byPriority($request->priority);
        if ($request->filled('assigned_to')) $query->assignedTo($request->assigned_to);

        return $this->paginated($query->orderByDesc('created_at')->paginate($request->get('per_page', 20)));
    }

    public function assign(SupportTicket $ticket, Request $request): JsonResponse
    {
        $data = $request->validate(['assigned_to' => 'required|exists:users,id']);
        $ticket->update([
            'assigned_to' => $data['assigned_to'],
            'status'      => 'in_progress',
        ]);
        return $this->success(null, 'Ticket assigned.');
    }
}
