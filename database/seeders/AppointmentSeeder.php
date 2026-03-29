<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = Doctor::all();
        $patients = Patient::all();

        if ($doctors->isEmpty() || $patients->isEmpty()) {
            return;
        }

        // Find the next Monday from today (for future scheduled appointments)
        $nextMonday = Carbon::now()->next(Carbon::MONDAY);

        $appointments = [
            // Past completed appointments
            [
                'patient_id' => $patients[0]->id, // Maria Rossi
                'doctor_id' => $doctors[0]->id,   // Roberto Conti
                'appointment_date' => Carbon::now()->subWeeks(3)->startOfWeek()->format('Y-m-d'),
                'appointment_time' => '09:00',
                'visit_type' => 'prima_visita',
                'status' => 'completed',
                'reason' => 'Initial cardiology consultation for chest discomfort.',
            ],
            [
                'patient_id' => $patients[1]->id, // Giuseppe Verdi
                'doctor_id' => $doctors[0]->id,   // Roberto Conti
                'appointment_date' => Carbon::now()->subWeeks(3)->startOfWeek()->format('Y-m-d'),
                'appointment_time' => '10:00',
                'visit_type' => 'ecg',
                'status' => 'completed',
                'reason' => 'Routine ECG screening.',
            ],
            [
                'patient_id' => $patients[2]->id, // Anna Ferrari
                'doctor_id' => $doctors[1]->id,   // Elena Russo
                'appointment_date' => Carbon::now()->subWeeks(2)->startOfWeek()->addDay()->format('Y-m-d'),
                'appointment_time' => '11:00',
                'visit_type' => 'ecocardiogramma',
                'status' => 'completed',
                'reason' => 'Echocardiogram follow-up after medication change.',
            ],
            [
                'patient_id' => $patients[3]->id, // Luca Romano
                'doctor_id' => $doctors[2]->id,   // Francesco Moretti
                'appointment_date' => Carbon::now()->subWeeks(2)->startOfWeek()->addDays(2)->format('Y-m-d'),
                'appointment_time' => '14:00',
                'visit_type' => 'prima_visita',
                'status' => 'completed',
                'reason' => 'Referred by GP for palpitations.',
            ],
            // Past no-show
            [
                'patient_id' => $patients[4]->id, // Sofia Colombo
                'doctor_id' => $doctors[1]->id,   // Elena Russo
                'appointment_date' => Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d'),
                'appointment_time' => '09:30',
                'visit_type' => 'controllo',
                'status' => 'no_show',
                'reason' => 'Blood pressure check-up.',
            ],
            // Past cancelled
            [
                'patient_id' => $patients[0]->id, // Maria Rossi
                'doctor_id' => $doctors[2]->id,   // Francesco Moretti
                'appointment_date' => Carbon::now()->subWeek()->startOfWeek()->addDay()->format('Y-m-d'),
                'appointment_time' => '15:00',
                'visit_type' => 'follow_up',
                'status' => 'cancelled',
                'reason' => 'Follow-up after initial visit.',
                'cancelled_at' => Carbon::now()->subWeek(),
            ],
            // Future scheduled appointments
            [
                'patient_id' => $patients[0]->id, // Maria Rossi
                'doctor_id' => $doctors[0]->id,   // Roberto Conti
                'appointment_date' => $nextMonday->format('Y-m-d'),
                'appointment_time' => '09:00',
                'visit_type' => 'follow_up',
                'status' => 'scheduled',
                'reason' => 'Follow-up after initial cardiology consultation.',
            ],
            [
                'patient_id' => $patients[1]->id, // Giuseppe Verdi
                'doctor_id' => $doctors[0]->id,   // Roberto Conti
                'appointment_date' => $nextMonday->format('Y-m-d'),
                'appointment_time' => '10:30',
                'visit_type' => 'controllo',
                'status' => 'scheduled',
                'reason' => 'Regular check-up for hypertension.',
            ],
            [
                'patient_id' => $patients[2]->id, // Anna Ferrari
                'doctor_id' => $doctors[1]->id,   // Elena Russo
                'appointment_date' => $nextMonday->copy()->addDay()->format('Y-m-d'),
                'appointment_time' => '11:00',
                'visit_type' => 'ecocardiogramma',
                'status' => 'scheduled',
                'reason' => 'Annual echocardiogram.',
            ],
            [
                'patient_id' => $patients[3]->id, // Luca Romano
                'doctor_id' => $doctors[2]->id,   // Francesco Moretti
                'appointment_date' => $nextMonday->copy()->addDays(2)->format('Y-m-d'),
                'appointment_time' => '14:30',
                'visit_type' => 'ecg',
                'status' => 'scheduled',
                'reason' => 'ECG to monitor arrhythmia.',
            ],
            [
                'patient_id' => $patients[4]->id, // Sofia Colombo
                'doctor_id' => $doctors[1]->id,   // Elena Russo
                'appointment_date' => $nextMonday->copy()->addDays(3)->format('Y-m-d'),
                'appointment_time' => '09:00',
                'visit_type' => 'prima_visita',
                'status' => 'scheduled',
                'reason' => 'First cardiology visit, referred for syncope episodes.',
            ],
            [
                'patient_id' => $patients[1]->id, // Giuseppe Verdi
                'doctor_id' => $doctors[2]->id,   // Francesco Moretti
                'appointment_date' => $nextMonday->copy()->addDays(4)->format('Y-m-d'),
                'appointment_time' => '16:00',
                'visit_type' => 'follow_up',
                'status' => 'scheduled',
                'reason' => 'Follow-up on medication adjustment.',
            ],
        ];

        foreach ($appointments as $data) {
            Appointment::firstOrCreate(
                [
                    'patient_id' => $data['patient_id'],
                    'doctor_id' => $data['doctor_id'],
                    'appointment_date' => $data['appointment_date'],
                    'appointment_time' => $data['appointment_time'],
                ],
                $data
            );
        }
    }
}
