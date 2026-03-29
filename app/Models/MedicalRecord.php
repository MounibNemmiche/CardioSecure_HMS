<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'visit_date',
        'visit_type',
        'symptoms',
        'bp_systolic',
        'bp_diastolic',
        'heart_rate',
        'oxygen_saturation',
        'diagnosis',
        'notes',
        'recommendations',
        'follow_up_date',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'follow_up_date' => 'date',
            'symptoms' => 'array',
        ];
    }

    public static array $symptomOptions = [
        'chest_pain' => 'Chest Pain',
        'palpitations' => 'Palpitations',
        'shortness_of_breath' => 'Shortness of Breath',
        'dizziness' => 'Dizziness',
        'fatigue' => 'Fatigue',
        'syncope' => 'Syncope',
        'edema' => 'Edema',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function files()
    {
        return $this->hasMany(MedicalRecordFile::class);
    }
}
