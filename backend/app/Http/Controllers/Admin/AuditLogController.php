<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditLogController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = AuditLog::with('user:id,first_name,last_name,email');

        if ($request->filled('module'))  $query->byModule($request->module);
        if ($request->filled('action'))  $query->byAction($request->action);
        if ($request->filled('user_id')) $query->byUser($request->user_id);
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        return $this->paginated($query->recent()->paginate($request->get('per_page', 30)));
    }
}
