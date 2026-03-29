<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientInsurance extends Model
{
    protected $table = 'patient_insurance';

    protected $fillable = [
        'patient_id',
        'insurance_plan_id',
        'policy_number',
        'holder_name',
        'tessera_sanitaria_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'policy_number' => 'encrypted',
            'holder_name' => 'encrypted',
            'tessera_sanitaria_number' => 'encrypted',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function plan()
    {
        return $this->belongsTo(InsurancePlan::class, 'insurance_plan_id');
    }
}
