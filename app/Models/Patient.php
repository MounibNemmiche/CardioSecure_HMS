<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'emergency_contact_name',
        'emergency_contact_phone',
        'blood_type',
        'allergies',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function insurance()
    {
        return $this->hasOne(PatientInsurance::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function medicationReminders()
    {
        return $this->hasMany(MedicationReminder::class);
    }
}
