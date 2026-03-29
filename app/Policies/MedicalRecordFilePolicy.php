<?php

namespace App\Policies;

use App\Models\MedicalRecordFile;
use App\Models\User;

class MedicalRecordFilePolicy
{
    /**
     * Doctor who created the record or the patient who owns it can download files.
     */
    public function download(User $user, MedicalRecordFile $file): bool
    {
        $record = $file->medicalRecord;

        if ($user->hasRole('doctor')) {
            return $user->doctor?->id === $record->doctor_id;
        }

        if ($user->hasRole('patient')) {
            return $user->patient?->id === $record->patient_id;
        }

        return false;
    }
}
