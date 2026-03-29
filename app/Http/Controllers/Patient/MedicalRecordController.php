<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $patient = $request->user()->patient;

        $records = MedicalRecord::where('patient_id', $patient->id)
            ->with(['doctor.user', 'files'])
            ->orderBy('visit_date', 'desc')
            ->get();

        return Inertia::render('Patient/MedicalRecords/Index', [
            'records' => $records,
            'visitTypes' => Appointment::$visitTypes,
            'symptomOptions' => MedicalRecord::$symptomOptions,
        ]);
    }

    public function show(Request $request, MedicalRecord $record)
    {
        $this->authorize('view', $record);

        $record->load(['doctor.user', 'files']);

        AuditService::log('medical_record_viewed', MedicalRecord::class, $record->id);

        return Inertia::render('Patient/MedicalRecords/Show', [
            'record' => $record,
            'visitTypes' => Appointment::$visitTypes,
            'symptomOptions' => MedicalRecord::$symptomOptions,
        ]);
    }
}
