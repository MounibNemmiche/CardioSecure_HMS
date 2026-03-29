<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Patient can view their own appointments.
     * Doctor can view appointments assigned to them.
     * Staff can view all appointments.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('staff')) {
            return true;
        }

        if ($user->hasRole('patient') && $user->patient) {
            return $appointment->patient_id === $user->patient->id;
        }

        if ($user->hasRole('doctor') && $user->doctor) {
            return $appointment->doctor_id === $user->doctor->id;
        }

        return false;
    }

    /**
     * Only patients can book appointments.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('patient');
    }

    /**
     * Patient can cancel own scheduled appointments.
     * Staff can cancel any scheduled appointment.
     */
    public function cancel(User $user, Appointment $appointment): bool
    {
        if ($appointment->status !== 'scheduled') {
            return false;
        }

        if ($user->hasRole('staff')) {
            return true;
        }

        if ($user->hasRole('patient') && $user->patient) {
            return $appointment->patient_id === $user->patient->id;
        }

        return false;
    }

    /**
     * Only staff can reschedule appointments.
     */
    public function reschedule(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('staff') && $appointment->status === 'scheduled';
    }

    /**
     * Only staff can mark appointments as completed/no_show.
     */
    public function updateStatus(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('staff');
    }
}
