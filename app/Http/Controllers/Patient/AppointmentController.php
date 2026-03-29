<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $patient = $request->user()->patient;

        $appointments = Appointment::where('patient_id', $patient->id)
            ->with(['doctor.user'])
            ->orderByRaw("CASE WHEN status = 'scheduled' THEN 0 ELSE 1 END")
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(10);

        $allAppointments = Appointment::where('patient_id', $patient->id)
            ->with(['doctor.user'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return Inertia::render('Patient/Appointments/Index', [
            'appointments' => $appointments,
            'allAppointments' => $allAppointments,
            'visitTypes' => Appointment::$visitTypes,
        ]);
    }

    public function create()
    {
        $doctors = Doctor::with('user')->get()->map(fn ($d) => [
            'id' => $d->id,
            'name' => $d->user->name,
            'specialization' => $d->specialization,
        ]);

        return Inertia::render('Patient/Appointments/Create', [
            'doctors' => $doctors,
            'visitTypes' => Appointment::$visitTypes,
        ]);
    }

    /**
     * Get available time slots for a given doctor and date.
     */
    public function availableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $date = Carbon::parse($request->date);

        // No weekends
        if ($date->isWeekend()) {
            return response()->json(['slots' => []]);
        }

        // Generate all 30-min slots from 09:00 to 17:00 (last slot starts at 16:30)
        $allSlots = [];
        $start = Carbon::parse($request->date . ' 09:00');
        $end = Carbon::parse($request->date . ' 17:00');

        while ($start < $end) {
            $allSlots[] = $start->format('H:i');
            $start->addMinutes(30);
        }

        // Get booked slots for this doctor on this date
        $booked = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->pluck('appointment_time')
            ->map(fn ($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        $available = array_values(array_diff($allSlots, $booked));

        return response()->json(['slots' => $available]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'visit_type' => 'required|in:' . implode(',', array_keys(Appointment::$visitTypes)),
            'reason' => 'nullable|string|max:500',
        ]);

        $patient = $request->user()->patient;

        // Verify slot is still available
        $exists = Appointment::where('doctor_id', $validated['doctor_id'])
            ->where('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($exists) {
            return back()->withErrors(['appointment_time' => 'This time slot is no longer available.']);
        }

        // Verify not a weekend
        if (Carbon::parse($validated['appointment_date'])->isWeekend()) {
            return back()->withErrors(['appointment_date' => 'Appointments are only available Monday to Friday.']);
        }

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $validated['doctor_id'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'visit_type' => $validated['visit_type'],
            'status' => 'scheduled',
            'reason' => $validated['reason'],
        ]);

        AuditService::log('appointment_created', Appointment::class, $appointment->id);

        return redirect()->route('patient.appointments.index')
            ->with('flash', ['success' => 'Appointment booked successfully.']);
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        $this->authorize('cancel', $appointment);

        $appointment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => $request->user()->id,
        ]);

        AuditService::log('appointment_cancelled', Appointment::class, $appointment->id);

        return redirect()->route('patient.appointments.index')
            ->with('flash', ['success' => 'Appointment cancelled successfully.']);
    }
}
