<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display list of audit logs
     */
    public function index(Request $request)
    {
        $query = \App\Models\AuditLog::with('user')->latest();

        // Filter by action
        if ($request->action) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->paginate(50);
        
        // Get unique actions for filter dropdown
        $actions = \App\Models\AuditLog::select('action')->distinct()->pluck('action');
        
        // Get users who have logs
        $users = \App\Models\User::whereHas('auditLogs')->get(['id', 'name']);

        return view('settings.audit-logs', compact('logs', 'actions', 'users'));
    }
}
