<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Services\AuditService;
use Inertia\Inertia;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::with(['user', 'insurance.plan'])
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->user->name,
                'email' => $p->user->email,
                'blood_type' => $p->blood_type,
                'allergies' => $p->allergies,
                'insurance_plan' => $p->insurance?->plan?->name,
                'appointments_count' => $p->appointments()->count(),
            ]);

        return Inertia::render('Staff/Patients/Index', [
            'patients' => $patients,
        ]);
    }

    public function show(Patient $patient)
    {
        $patient->load(['user.profile', 'insurance.plan']);

        AuditService::log('patient_profile_viewed', Patient::class, $patient->id);

        return Inertia::render('Staff/Patients/Show', [
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->user->name,
                'email' => $patient->user->email,
                'phone' => $patient->user->profile?->phone,
                'date_of_birth' => $patient->user->profile?->date_of_birth,
                'codice_fiscale' => $patient->user->profile?->codice_fiscale,
                'address' => $patient->user->profile?->address,
                'city' => $patient->user->profile?->city,
                'province' => $patient->user->profile?->province,
                'blood_type' => $patient->blood_type,
                'allergies' => $patient->allergies,
                'emergency_contact_name' => $patient->emergency_contact_name,
                'emergency_contact_phone' => $patient->emergency_contact_phone,
                'insurance' => $patient->insurance ? [
                    'plan' => $patient->insurance->plan?->name,
                    'policy_number' => $patient->insurance->policy_number,
                    'holder_name' => $patient->insurance->holder_name,
                    'tessera_sanitaria_number' => $patient->insurance->tessera_sanitaria_number,
                    'notes' => $patient->insurance->notes,
                ] : null,
            ],
        ]);
    }
}
