<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $appointmentsToday = Appointment::where('appointment_date', today())
            ->where('status', 'scheduled')
            ->count();

        $upcomingThisWeek = Appointment::whereBetween('appointment_date', [today(), today()->addDays(7)])
            ->where('status', 'scheduled')
            ->count();

        $totalPatients = Patient::count();

        $recentAppointments = Appointment::where('appointment_date', '>=', today())
            ->where('status', 'scheduled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->with(['patient.user', 'doctor.user'])
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'date' => $a->appointment_date,
                'time' => $a->appointment_time,
                'patient' => $a->patient->user->name,
                'doctor' => 'Dr. ' . $a->doctor->user->name,
                'type' => $a->visit_type,
            ]);

        return Inertia::render('Staff/Dashboard', [
            'appointmentsToday' => $appointmentsToday,
            'upcomingThisWeek' => $upcomingThisWeek,
            'totalPatients' => $totalPatients,
            'recentAppointments' => $recentAppointments,
        ]);
    }
}
