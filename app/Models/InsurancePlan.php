<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsurancePlan extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function patientInsurances()
    {
        return $this->hasMany(PatientInsurance::class);
    }
}
