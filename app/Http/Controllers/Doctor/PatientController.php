<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatientController extends Controller
{
    /**
     * List patients the doctor has appointments with.
     */
    public function index(Request $request)
    {
        $doctor = $request->user()->doctor;

        $patients = Patient::whereHas('appointments', fn ($q) => $q->where('doctor_id', $doctor->id))
            ->with('user')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->user->name,
                'email' => $p->user->email,
                'blood_type' => $p->blood_type,
                'allergies' => $p->allergies,
                'appointments_count' => $p->appointments()->where('doctor_id', $doctor->id)->count(),
                'last_visit' => $p->appointments()
                    ->where('doctor_id', $doctor->id)
                    ->where('status', 'completed')
                    ->latest('appointment_date')
                    ->value('appointment_date'),
            ]);

        return Inertia::render('Doctor/Patients/Index', [
            'patients' => $patients,
        ]);
    }
}
