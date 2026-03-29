<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{
    /**
     * Doctor can view records they created; Patient can view own records.
     */
    public function view(User $user, MedicalRecord $record): bool
    {
        if ($user->hasRole('doctor')) {
            return $user->doctor?->id === $record->doctor_id;
        }

        if ($user->hasRole('patient')) {
            return $user->patient?->id === $record->patient_id;
        }

        return false;
    }

    /**
     * Only doctors can create medical records.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('doctor');
    }
}
