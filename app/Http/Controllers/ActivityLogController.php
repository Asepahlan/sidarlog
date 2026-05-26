<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('pengguna')->latest()->paginate(20);
        return view('pages.sistem.activity_log', compact('logs'));
    }
}
