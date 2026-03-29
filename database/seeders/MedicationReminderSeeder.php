<?php

namespace Database\Seeders;

use App\Models\MedicationReminder;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class MedicationReminderSeeder extends Seeder
{
    public function run(): void
    {
        $rossi = Patient::whereHas('user', fn ($q) => $q->where('name', 'Maria Rossi'))->first();
        $verdi = Patient::whereHas('user', fn ($q) => $q->where('name', 'Giuseppe Verdi'))->first();
        $ferrari = Patient::whereHas('user', fn ($q) => $q->where('name', 'Anna Ferrari'))->first();
        $romano = Patient::whereHas('user', fn ($q) => $q->where('name', 'Luca Romano'))->first();

        $reminders = [
            // Maria Rossi
            ['patient_id' => $rossi->id, 'medication_name' => 'Ramipril', 'dosage' => '5mg', 'reminder_time' => '08:00', 'notes' => 'Take in the morning before breakfast', 'is_active' => true],
            ['patient_id' => $rossi->id, 'medication_name' => 'Atorvastatina', 'dosage' => '20mg', 'reminder_time' => '21:00', 'notes' => 'Take in the evening with dinner', 'is_active' => true],

            // Giuseppe Verdi
            ['patient_id' => $verdi->id, 'medication_name' => 'Bisoprololo', 'dosage' => '2.5mg', 'reminder_time' => '07:30', 'notes' => null, 'is_active' => true],

            // Anna Ferrari
            ['patient_id' => $ferrari->id, 'medication_name' => 'Ramipril', 'dosage' => '5mg', 'reminder_time' => '08:00', 'notes' => null, 'is_active' => true],
            ['patient_id' => $ferrari->id, 'medication_name' => 'Amlodipina', 'dosage' => '5mg', 'reminder_time' => '08:00', 'notes' => 'Take with Ramipril', 'is_active' => true],
            ['patient_id' => $ferrari->id, 'medication_name' => 'Furosemide', 'dosage' => '20mg', 'reminder_time' => '07:00', 'notes' => 'Take as needed for edema', 'is_active' => false],

            // Luca Romano
            ['patient_id' => $romano->id, 'medication_name' => 'Cardioaspirina', 'dosage' => '100mg', 'reminder_time' => '13:00', 'notes' => 'Take after lunch', 'is_active' => true],
        ];

        foreach ($reminders as $reminder) {
            MedicationReminder::firstOrCreate(
                [
                    'patient_id' => $reminder['patient_id'],
                    'medication_name' => $reminder['medication_name'],
                ],
                $reminder
            );
        }
    }
}
