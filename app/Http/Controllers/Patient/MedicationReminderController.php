<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\MedicationReminder;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MedicationReminderController extends Controller
{
    public function index(Request $request)
    {
        $patient = $request->user()->patient;

        $reminders = $patient->medicationReminders()
            ->orderByDesc('is_active')
            ->orderBy('reminder_time')
            ->get();

        return Inertia::render('Patient/Medications/Index', [
            'reminders' => $reminders,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medication_name' => 'required|string|max:255',
            'dosage' => 'required|string|max:100',
            'reminder_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'notes' => 'nullable|string|max:500',
        ]);

        // Normalize to HH:MM regardless of whether browser sent HH:MM:SS
        $validated['reminder_time'] = substr($validated['reminder_time'], 0, 5);

        $patient = $request->user()->patient;

        $reminder = $patient->medicationReminders()->create($validated + ['is_active' => true]);

        AuditService::log('medication_reminder_created', MedicationReminder::class, $reminder->id);

        return back()->with('flash', ['success' => 'Medication reminder added.']);
    }

    public function update(Request $request, MedicationReminder $reminder)
    {
        $patient = $request->user()->patient;
        abort_unless($reminder->patient_id === $patient->id, 403);

        $validated = $request->validate([
            'medication_name' => 'required|string|max:255',
            'dosage' => 'required|string|max:100',
            'reminder_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['reminder_time'] = substr($validated['reminder_time'], 0, 5);

        $reminder->update($validated);

        return back()->with('flash', ['success' => 'Medication reminder updated.']);
    }

    public function toggleActive(Request $request, MedicationReminder $reminder)
    {
        $patient = $request->user()->patient;
        abort_unless($reminder->patient_id === $patient->id, 403);

        $reminder->update(['is_active' => !$reminder->is_active]);

        return back()->with('flash', [
            'success' => $reminder->is_active ? 'Reminder activated.' : 'Reminder paused.',
        ]);
    }

    public function destroy(Request $request, MedicationReminder $reminder)
    {
        $patient = $request->user()->patient;
        abort_unless($reminder->patient_id === $patient->id, 403);

        $reminder->delete();

        return back()->with('flash', ['success' => 'Medication reminder deleted.']);
    }
}
