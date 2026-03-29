<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicationReminder extends Model
{
    protected $fillable = [
        'patient_id',
        'medication_name',
        'dosage',
        'reminder_time',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
