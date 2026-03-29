<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'visit_type',
        'status',
        'reason',
        'notes',
        'cancelled_at',
        'cancelled_by',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'cancelled_at' => 'datetime',
        ];
    }

    public static array $visitTypes = [
        'prima_visita' => 'First Cardiology Visit',
        'controllo' => 'Check-up',
        'ecg' => 'ECG',
        'follow_up' => 'Follow-up',
        'ecocardiogramma' => 'Echocardiogram',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}
