<?php

namespace App\Http\Controllers;

use App\Modules\Audit\Models\AuditEvent;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditEvent::with(['user', 'entity' => function ($query) {
            $query->withTrashed();
        }])->latest();

        if ($request->action) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(15)->withQueryString();
        return view('audit.index', compact('logs'));
    }
}
