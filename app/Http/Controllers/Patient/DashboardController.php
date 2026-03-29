<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $patient = $user->patient;

        $nextAppointment = null;
        $totalRecords = 0;
        $activeReminders = 0;
        $insurancePlan = null;

        if ($patient) {
            $nextAppointment = $patient->appointments()
                ->where('appointment_date', '>=', today())
                ->where('status', 'scheduled')
                ->orderBy('appointment_date')
                ->orderBy('appointment_time')
                ->with('doctor.user')
                ->first();

            $totalRecords = $patient->medicalRecords()->count();
            $activeReminders = $patient->medicationReminders()->where('is_active', true)->count();

            $insurance = $patient->insurance()->with('plan')->first();
            $insurancePlan = $insurance?->plan?->name;
        }

        return Inertia::render('Patient/Dashboard', [
            'nextAppointment' => $nextAppointment ? [
                'date' => $nextAppointment->appointment_date,
                'time' => $nextAppointment->appointment_time,
                'doctor' => 'Dr. ' . $nextAppointment->doctor->user->name,
                'type' => $nextAppointment->visit_type,
            ] : null,
            'totalRecords' => $totalRecords,
            'activeReminders' => $activeReminders,
            'insurancePlan' => $insurancePlan,
        ]);
    }
}
