<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $this->authorize('audit.view');

        return view('audit.index', [
            'logs' => AuditLog::with('user')->latest()->paginate(30),
        ]);
    }

    /** JSON feed for the AJAX audit-trail table (DataTables). */
    public function datatable()
    {
        $this->authorize('audit.view');

        $rows = AuditLog::with('user')->latest()->limit(2000)->get()
            ->map(fn (AuditLog $log) => [
                'when' => $log->created_at->format('d M Y H:i:s'),
                'user' => e($log->user->name ?? 'System / API'),
                'action' => '<code>'.e(str_replace('_', ' ', str_replace('.', ' — ', $log->action))).'</code>',
                'subject' => $log->auditable_type
                    ? '<span class="small">'.e(class_basename($log->auditable_type).' #'.$log->auditable_id).'</span>'
                    : '<span class="text-muted">—</span>',
                'details' => '<span class="small text-muted">'.e(\Illuminate\Support\Str::limit(json_encode($log->new_values ?? $log->old_values), 90)).'</span>',
            ]);

        return response()->json(['data' => $rows]);
    }

}
