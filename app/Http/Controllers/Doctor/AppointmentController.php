<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $doctor = $request->user()->doctor;

        $query = Appointment::where('doctor_id', $doctor->id)
            ->with(['patient.user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->where('appointment_date', $request->date);
        }

        $appointments = $query
            ->orderByRaw("CASE WHEN status = 'scheduled' THEN 0 ELSE 1 END")
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(15)
            ->withQueryString();

        $allAppointments = Appointment::where('doctor_id', $doctor->id)
            ->with(['patient.user'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return Inertia::render('Doctor/Appointments/Index', [
            'appointments' => $appointments,
            'allAppointments' => $allAppointments,
            'visitTypes' => Appointment::$visitTypes,
            'filters' => $request->only(['status', 'date']),
        ]);
    }
}
