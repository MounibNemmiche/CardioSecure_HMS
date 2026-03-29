<?php

namespace App\Http\Controllers\Staff;

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
        $query = Appointment::with(['patient.user', 'doctor.user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by doctor
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('appointment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('appointment_date', '<=', $request->date_to);
        }

        $appointments = $query
            ->orderByRaw("CASE WHEN status = 'scheduled' THEN 0 ELSE 1 END")
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(15)
            ->withQueryString();

        $doctors = Doctor::with('user')->get()->map(fn ($d) => [
            'id' => $d->id,
            'name' => $d->user->name,
        ]);

        $allAppointments = Appointment::with(['patient.user', 'doctor.user'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return Inertia::render('Staff/Appointments/Index', [
            'appointments' => $appointments,
            'allAppointments' => $allAppointments,
            'doctors' => $doctors,
            'visitTypes' => Appointment::$visitTypes,
            'filters' => $request->only(['status', 'doctor_id', 'date_from', 'date_to']),
        ]);
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $this->authorize('reschedule', $appointment);

        $validated = $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        // Verify not a weekend
        if (Carbon::parse($validated['appointment_date'])->isWeekend()) {
            return back()->withErrors(['appointment_date' => 'Appointments are only available Monday to Friday.']);
        }

        // Verify slot is available
        $exists = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['appointment_time' => 'This time slot is no longer available.']);
        }

        $appointment->update([
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
        ]);

        AuditService::log('appointment_updated', Appointment::class, $appointment->id, true, ['action_detail' => 'rescheduled']);

        return redirect()->route('staff.appointments.index')
            ->with('flash', ['success' => 'Appointment rescheduled successfully.']);
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

        return redirect()->route('staff.appointments.index')
            ->with('flash', ['success' => 'Appointment cancelled successfully.']);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $this->authorize('updateStatus', $appointment);

        $validated = $request->validate([
            'status' => 'required|in:completed,no_show',
        ]);

        $appointment->update([
            'status' => $validated['status'],
        ]);

        AuditService::log('appointment_updated', Appointment::class, $appointment->id, true, ['status' => $validated['status']]);

        return redirect()->route('staff.appointments.index')
            ->with('flash', ['success' => 'Appointment status updated.']);
    }

    /**
     * Get available time slots for rescheduling.
     */
    public function availableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'exclude_id' => 'nullable|exists:appointments,id',
        ]);

        $date = Carbon::parse($request->date);

        if ($date->isWeekend()) {
            return response()->json(['slots' => []]);
        }

        $allSlots = [];
        $start = Carbon::parse($request->date . ' 09:00');
        $end = Carbon::parse($request->date . ' 17:00');

        while ($start < $end) {
            $allSlots[] = $start->format('H:i');
            $start->addMinutes(30);
        }

        $query = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->date)
            ->where('status', '!=', 'cancelled');

        if ($request->filled('exclude_id')) {
            $query->where('id', '!=', $request->exclude_id);
        }

        $booked = $query->pluck('appointment_time')
            ->map(fn ($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        $available = array_values(array_diff($allSlots, $booked));

        return response()->json(['slots' => $available]);
    }
}
