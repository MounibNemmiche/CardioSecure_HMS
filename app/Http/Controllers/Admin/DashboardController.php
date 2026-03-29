<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalUsers = User::count();
        $totalDoctors = User::role('doctor')->count();
        $totalPatients = User::role('patient')->count();
        $totalStaff = User::role('staff')->count();
        $recentLogs = AuditLog::latest()->limit(5)->with('user')->get()->map(fn($log) => [
            'id' => $log->id,
            'action' => $log->action,
            'user' => $log->user?->name ?? 'Unknown',
            'ip' => $log->ip_address,
            'created_at' => $log->created_at,
        ]);

        return Inertia::render('Admin/Dashboard', [
            'totalUsers' => $totalUsers,
            'totalDoctors' => $totalDoctors,
            'totalPatients' => $totalPatients,
            'totalStaff' => $totalStaff,
            'recentLogs' => $recentLogs,
        ]);
    }
}
