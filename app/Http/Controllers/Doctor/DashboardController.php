<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $doctor = $user->doctor;

        $appointmentsToday = 0;
        $totalPatients = 0;
        $totalRecords = 0;
        $upcomingAppointments = [];

        if ($doctor) {
            $appointmentsToday = $doctor->appointments()
                ->where('appointment_date', today())
                ->where('status', 'scheduled')
                ->count();

            $totalPatients = $doctor->appointments()
                ->distinct('patient_id')
                ->count('patient_id');

            $totalRecords = $doctor->medicalRecords()->count();

            $upcomingAppointments = $doctor->appointments()
                ->where('appointment_date', '>=', today())
                ->where('status', 'scheduled')
                ->orderBy('appointment_date')
                ->orderBy('appointment_time')
                ->with('patient.user')
                ->limit(5)
                ->get()
                ->map(fn($a) => [
                    'id' => $a->id,
                    'date' => $a->appointment_date,
                    'time' => $a->appointment_time,
                    'patient' => $a->patient->user->name,
                    'type' => $a->visit_type,
                ]);
        }

        return Inertia::render('Doctor/Dashboard', [
            'appointmentsToday' => $appointmentsToday,
            'totalPatients' => $totalPatients,
            'totalRecords' => $totalRecords,
            'upcomingAppointments' => $upcomingAppointments,
        ]);
    }
}
