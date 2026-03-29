<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('CardioSecure2026!');

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@cardiosecure.it'],
            [
                'name' => 'Alessandro Bianchi',
                'password' => $password,
                'email_verified_at' => now(),
                'two_factor_confirmed_at' => now(),
            ]
        );
        $admin->assignRole('admin');
        Profile::firstOrCreate(['user_id' => $admin->id], [
            'phone' => '+39 049 123 4567',
            'city' => 'Padova',
            'province' => 'PD',
        ]);

        // Staff
        $staff1 = User::firstOrCreate(
            ['email' => 'giulia.marino@cardiosecure.it'],
            [
                'name' => 'Giulia Marino',
                'password' => $password,
                'email_verified_at' => now(),
                'two_factor_confirmed_at' => now(),
            ]
        );
        $staff1->assignRole('staff');
        Profile::firstOrCreate(['user_id' => $staff1->id], [
            'phone' => '+39 049 234 5678',
            'city' => 'Padova',
            'province' => 'PD',
        ]);

        $staff2 = User::firstOrCreate(
            ['email' => 'marco.esposito@cardiosecure.it'],
            [
                'name' => 'Marco Esposito',
                'password' => $password,
                'email_verified_at' => now(),
                'two_factor_confirmed_at' => now(),
            ]
        );
        $staff2->assignRole('staff');
        Profile::firstOrCreate(['user_id' => $staff2->id], [
            'phone' => '+39 049 345 6789',
            'city' => 'Padova',
            'province' => 'PD',
        ]);

        // Doctors
        $doctorData = [
            [
                'email' => 'roberto.conti@cardiosecure.it',
                'name' => 'Roberto Conti',
                'license_number' => 'PD-2019-00451',
                'bio' => 'Cardiologo con 15 anni di esperienza in cardiologia interventistica.',
            ],
            [
                'email' => 'elena.russo@cardiosecure.it',
                'name' => 'Elena Russo',
                'license_number' => 'PD-2021-00892',
                'bio' => 'Specialista in ecocardiografia e cardiologia dello sport.',
            ],
            [
                'email' => 'francesco.moretti@cardiosecure.it',
                'name' => 'Francesco Moretti',
                'license_number' => 'PD-2018-00327',
                'bio' => 'Cardiologo con specializzazione in aritmologia e elettrofisiologia.',
            ],
        ];

        foreach ($doctorData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $password,
                    'email_verified_at' => now(),
                    'two_factor_confirmed_at' => now(),
                ]
            );
            $user->assignRole('doctor');
            Profile::firstOrCreate(['user_id' => $user->id], [
                'phone' => '+39 049 456 7890',
                'city' => 'Padova',
                'province' => 'PD',
            ]);
            Doctor::firstOrCreate(['user_id' => $user->id], [
                'specialization' => 'Cardiologia',
                'license_number' => $data['license_number'],
                'bio' => $data['bio'],
            ]);
        }

        // Patients
        $patientData = [
            [
                'email' => 'maria.rossi@example.it',
                'name' => 'Maria Rossi',
                'phone' => '+39 333 111 2222',
                'date_of_birth' => '1985-03-15',
                'codice_fiscale' => 'RSSMRA85C55H501Z',
                'blood_type' => 'A+',
                'allergies' => 'Penicillina',
            ],
            [
                'email' => 'giuseppe.verdi@example.it',
                'name' => 'Giuseppe Verdi',
                'phone' => '+39 333 222 3333',
                'date_of_birth' => '1960-07-22',
                'codice_fiscale' => 'VRDGPP60L22H501X',
                'blood_type' => 'B+',
                'allergies' => null,
            ],
            [
                'email' => 'anna.ferrari@example.it',
                'name' => 'Anna Ferrari',
                'phone' => '+39 333 333 4444',
                'date_of_birth' => '1978-11-08',
                'codice_fiscale' => 'FRRNNA78S48H501K',
                'blood_type' => 'O+',
                'allergies' => 'Aspirina, FANS',
            ],
            [
                'email' => 'luca.romano@example.it',
                'name' => 'Luca Romano',
                'phone' => '+39 333 444 5555',
                'date_of_birth' => '1992-04-30',
                'codice_fiscale' => 'RMNLCU92D30H501W',
                'blood_type' => 'AB-',
                'allergies' => null,
            ],
            [
                'email' => 'sofia.colombo@example.it',
                'name' => 'Sofia Colombo',
                'phone' => '+39 333 555 6666',
                'date_of_birth' => '2000-09-12',
                'codice_fiscale' => 'CLMSFO00P52H501J',
                'blood_type' => 'A-',
                'allergies' => 'Lattice',
            ],
        ];

        foreach ($patientData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $password,
                    'email_verified_at' => now(),
                    'two_factor_confirmed_at' => now(),
                ]
            );
            $user->assignRole('patient');
            Profile::firstOrCreate(['user_id' => $user->id], [
                'phone' => $data['phone'],
                'date_of_birth' => $data['date_of_birth'],
                'codice_fiscale' => $data['codice_fiscale'],
                'city' => 'Padova',
                'province' => 'PD',
            ]);
            Patient::firstOrCreate(['user_id' => $user->id], [
                'blood_type' => $data['blood_type'],
                'allergies' => $data['allergies'],
            ]);
        }
    }
}
