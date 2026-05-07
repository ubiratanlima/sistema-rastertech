<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Filtro por busca simples
        if ($request->search) {
            $query->where('event', 'like', "%{$request->search}%")
                  ->orWhere('auditable_type', 'like', "%{$request->search}%")
                  ->orWhere('ip_address', 'like', "%{$request->search}%");
        }

        $logs = $query->paginate(20);

        return view('audit.index', compact('logs'));
    }
}
