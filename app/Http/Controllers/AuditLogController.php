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
}
