<?php

namespace App\Http\Controllers;

use App\Models\PasswordChangeLog;

class PasswordChangeLogController extends Controller
{
    public function index()
    {
        $logs = PasswordChangeLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.password-change-logs', compact('logs'));
    }
}