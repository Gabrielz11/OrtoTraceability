<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->action) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(30);
        return view('audit.index', compact('logs'));
    }
}
