<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordFile;
use App\Models\Patient;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MedicalRecordController extends Controller
{
    /**
     * List records for a specific patient (doctor must have appointments with them).
     */
    public function index(Request $request, Patient $patient)
    {
        $doctor = $request->user()->doctor;

        // Verify doctor has appointments with this patient
        $hasAppointment = Appointment::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->exists();

        abort_unless($hasAppointment, 403);

        $records = MedicalRecord::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->with('files')
            ->orderBy('visit_date', 'desc')
            ->get();

        return Inertia::render('Doctor/MedicalRecords/Index', [
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->user->name,
                'blood_type' => $patient->blood_type,
                'allergies' => $patient->allergies,
            ],
            'records' => $records,
            'visitTypes' => Appointment::$visitTypes,
            'symptomOptions' => MedicalRecord::$symptomOptions,
        ]);
    }

    /**
     * Show form to create a new medical record.
     */
    public function create(Request $request, Patient $patient)
    {
        $doctor = $request->user()->doctor;

        $hasAppointment = Appointment::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->exists();

        abort_unless($hasAppointment, 403);

        return Inertia::render('Doctor/MedicalRecords/Create', [
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->user->name,
            ],
            'visitTypes' => Appointment::$visitTypes,
            'symptomOptions' => MedicalRecord::$symptomOptions,
        ]);
    }

    /**
     * Store a new medical record (immutable — no edits after creation).
     */
    public function store(Request $request, Patient $patient)
    {
        $doctor = $request->user()->doctor;

        $hasAppointment = Appointment::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->exists();

        abort_unless($hasAppointment, 403);

        $validated = $request->validate([
            'visit_date' => 'required|date|before_or_equal:today',
            'visit_type' => 'required|string|in:' . implode(',', array_keys(Appointment::$visitTypes)),
            'symptoms' => 'nullable|array',
            'symptoms.*' => 'string|in:' . implode(',', array_keys(MedicalRecord::$symptomOptions)),
            'bp_systolic' => 'nullable|integer|min:50|max:300',
            'bp_diastolic' => 'nullable|integer|min:30|max:200',
            'heart_rate' => 'nullable|integer|min:20|max:300',
            'oxygen_saturation' => 'nullable|integer|min:50|max:100',
            'diagnosis' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:5000',
            'recommendations' => 'nullable|string|max:2000',
            'follow_up_date' => 'nullable|date|after:today',
            'files' => 'nullable|array|max:5',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'file_descriptions' => 'nullable|array',
            'file_descriptions.*' => 'nullable|string|max:255',
        ]);

        $record = MedicalRecord::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'visit_date' => $validated['visit_date'],
            'visit_type' => $validated['visit_type'],
            'symptoms' => $validated['symptoms'] ?? null,
            'bp_systolic' => $validated['bp_systolic'] ?? null,
            'bp_diastolic' => $validated['bp_diastolic'] ?? null,
            'heart_rate' => $validated['heart_rate'] ?? null,
            'oxygen_saturation' => $validated['oxygen_saturation'] ?? null,
            'diagnosis' => $validated['diagnosis'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'recommendations' => $validated['recommendations'] ?? null,
            'follow_up_date' => $validated['follow_up_date'] ?? null,
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $originalName = Str::ascii(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $originalName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $originalName);
                $extension = $file->getClientOriginalExtension();
                $fileName = $originalName . '_' . Str::random(8) . '.' . $extension;

                $path = $file->storeAs("medical-files/{$record->id}", $fileName);

                MedicalRecordFile::create([
                    'medical_record_id' => $record->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => $request->user()->id,
                    'description' => $validated['file_descriptions'][$index] ?? null,
                ]);

                AuditService::log('file_uploaded', MedicalRecordFile::class, $record->id);
            }
        }

        AuditService::log('medical_record_created', MedicalRecord::class, $record->id);

        return redirect()->route('doctor.patients.records.index', $patient)
            ->with('flash', ['success' => 'Medical record created successfully.']);
    }

    /**
     * View a specific medical record.
     */
    public function show(Request $request, Patient $patient, MedicalRecord $record)
    {
        $this->authorize('view', $record);

        $record->load('files', 'doctor.user');

        AuditService::log('medical_record_viewed', MedicalRecord::class, $record->id);

        return Inertia::render('Doctor/MedicalRecords/Show', [
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->user->name,
            ],
            'record' => $record,
            'visitTypes' => Appointment::$visitTypes,
            'symptomOptions' => MedicalRecord::$symptomOptions,
        ]);
    }
}
