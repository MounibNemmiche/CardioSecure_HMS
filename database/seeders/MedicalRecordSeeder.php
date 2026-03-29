<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MedicalRecordSeeder extends Seeder
{
    public function run(): void
    {
        $conti = Doctor::whereHas('user', fn ($q) => $q->where('email', 'roberto.conti@cardiosecure.it'))->first();
        $russo = Doctor::whereHas('user', fn ($q) => $q->where('email', 'elena.russo@cardiosecure.it'))->first();
        $moretti = Doctor::whereHas('user', fn ($q) => $q->where('email', 'francesco.moretti@cardiosecure.it'))->first();

        $rossi = Patient::whereHas('user', fn ($q) => $q->where('name', 'Maria Rossi'))->first();
        $verdi = Patient::whereHas('user', fn ($q) => $q->where('name', 'Giuseppe Verdi'))->first();
        $ferrari = Patient::whereHas('user', fn ($q) => $q->where('name', 'Anna Ferrari'))->first();
        $romano = Patient::whereHas('user', fn ($q) => $q->where('name', 'Luca Romano'))->first();
        $colombo = Patient::whereHas('user', fn ($q) => $q->where('name', 'Sofia Colombo'))->first();

        $records = [
            // Maria Rossi — Dr. Conti
            [
                'patient_id' => $rossi->id,
                'doctor_id' => $conti->id,
                'visit_date' => Carbon::now()->subWeeks(4)->format('Y-m-d'),
                'visit_type' => 'prima_visita',
                'symptoms' => ['chest_pain', 'shortness_of_breath', 'fatigue'],
                'bp_systolic' => 145,
                'bp_diastolic' => 92,
                'heart_rate' => 88,
                'oxygen_saturation' => 96,
                'diagnosis' => 'Grade 1 arterial hypertension. Mild left ventricular hypertrophy on echocardiogram.',
                'notes' => 'Patient reports intermittent chest tightness over the past 3 months, worsening with physical activity. Family history of cardiovascular disease (father had MI at age 58). Non-smoker. BMI 27.3.',
                'recommendations' => "Start Ramipril 5mg daily. Low-sodium diet recommended. Regular aerobic exercise 30 min/day. Repeat ECG in 3 months.",
                'follow_up_date' => Carbon::now()->addWeeks(6)->format('Y-m-d'),
            ],
            // Maria Rossi — follow-up with Dr. Conti
            [
                'patient_id' => $rossi->id,
                'doctor_id' => $conti->id,
                'visit_date' => Carbon::now()->subWeeks(1)->format('Y-m-d'),
                'visit_type' => 'controllo',
                'symptoms' => ['fatigue'],
                'bp_systolic' => 130,
                'bp_diastolic' => 85,
                'heart_rate' => 72,
                'oxygen_saturation' => 98,
                'diagnosis' => 'Arterial hypertension — improved with treatment. Blood pressure within target range.',
                'notes' => 'Patient tolerating Ramipril well. Reports reduced chest tightness. Continuing exercise program. Weight stable.',
                'recommendations' => 'Continue Ramipril 5mg daily. Add Atorvastatin 20mg for mild hypercholesterolemia. Follow up in 2 months.',
                'follow_up_date' => Carbon::now()->addMonths(2)->format('Y-m-d'),
            ],

            // Giuseppe Verdi — Dr. Conti
            [
                'patient_id' => $verdi->id,
                'doctor_id' => $conti->id,
                'visit_date' => Carbon::now()->subWeeks(3)->format('Y-m-d'),
                'visit_type' => 'ecg',
                'symptoms' => ['palpitations', 'dizziness'],
                'bp_systolic' => 118,
                'bp_diastolic' => 75,
                'heart_rate' => 95,
                'oxygen_saturation' => 99,
                'diagnosis' => 'Sinus tachycardia. ECG shows normal sinus rhythm with rate of 95 bpm. No ST-T changes. Normal QTc interval.',
                'notes' => 'Patient reports episodic palpitations lasting 10-15 minutes, occurring 2-3 times weekly. No syncope. Caffeine intake: 4-5 espressos daily.',
                'recommendations' => 'Reduce caffeine intake. 24-hour Holter monitor recommended to capture episodic arrhythmias. Consider Bisoprolol 2.5mg if symptoms persist.',
                'follow_up_date' => Carbon::now()->addWeeks(3)->format('Y-m-d'),
            ],

            // Anna Ferrari — Dr. Russo
            [
                'patient_id' => $ferrari->id,
                'doctor_id' => $russo->id,
                'visit_date' => Carbon::now()->subWeeks(2)->format('Y-m-d'),
                'visit_type' => 'ecocardiogramma',
                'symptoms' => ['shortness_of_breath', 'edema'],
                'bp_systolic' => 155,
                'bp_diastolic' => 98,
                'heart_rate' => 78,
                'oxygen_saturation' => 95,
                'diagnosis' => 'Grade 2 arterial hypertension. Echocardiogram reveals mild mitral regurgitation. LVEF 55% (preserved).',
                'notes' => 'Patient presents with bilateral ankle edema (grade 1) and exertional dyspnea (NYHA class II). BMI 31.2. History of gestational diabetes.',
                'recommendations' => 'Start combination therapy: Ramipril 5mg + Amlodipine 5mg daily. Furosemide 20mg as needed for edema. Weight reduction program. Cardiology follow-up in 6 weeks.',
                'follow_up_date' => Carbon::now()->addWeeks(6)->format('Y-m-d'),
            ],

            // Luca Romano — Dr. Moretti
            [
                'patient_id' => $romano->id,
                'doctor_id' => $moretti->id,
                'visit_date' => Carbon::now()->subWeeks(5)->format('Y-m-d'),
                'visit_type' => 'prima_visita',
                'symptoms' => ['syncope', 'dizziness', 'palpitations'],
                'bp_systolic' => 105,
                'bp_diastolic' => 68,
                'heart_rate' => 52,
                'oxygen_saturation' => 98,
                'diagnosis' => 'Sinus bradycardia (52 bpm). Neurocardiogenic syncope suspected. Orthostatic hypotension confirmed on tilt test.',
                'notes' => 'Young patient (28 years old) with two syncopal episodes in the past month, both preceded by prodromal symptoms (lightheadedness, visual dimming). Amateur cyclist — high vagal tone likely contributory.',
                'recommendations' => 'Increase fluid and salt intake. Counter-pressure maneuvers education. Avoid prolonged standing. Event monitor for 2 weeks. No driving restriction at this time.',
                'follow_up_date' => Carbon::now()->addWeeks(4)->format('Y-m-d'),
            ],

            // Sofia Colombo — Dr. Russo
            [
                'patient_id' => $colombo->id,
                'doctor_id' => $russo->id,
                'visit_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'visit_type' => 'follow_up',
                'symptoms' => ['chest_pain'],
                'bp_systolic' => 122,
                'bp_diastolic' => 78,
                'heart_rate' => 68,
                'oxygen_saturation' => 99,
                'diagnosis' => 'Atypical chest pain — likely musculoskeletal origin. Cardiac workup negative. Stress test normal.',
                'notes' => 'Follow-up after cardiac stress test performed last week. Maximal heart rate achieved (152 bpm, 92% predicted). No ischemic changes on ECG during exercise. Patient reports pain is reproducible with palpation of costochondral junction.',
                'recommendations' => 'Reassurance provided. NSAIDs as needed for costochondritis. No cardiac medication required. Annual cardiology check-up recommended.',
                'follow_up_date' => null,
            ],
        ];

        foreach ($records as $record) {
            MedicalRecord::firstOrCreate(
                [
                    'patient_id' => $record['patient_id'],
                    'doctor_id' => $record['doctor_id'],
                    'visit_date' => $record['visit_date'],
                    'visit_type' => $record['visit_type'],
                ],
                $record
            );
        }
    }
}
