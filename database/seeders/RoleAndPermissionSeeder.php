<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage-users',
            'manage-roles',
            'manage-insurance-plans',
            'view-audit-logs',
            'manage-appointments',
            'view-all-appointments',
            'view-patient-profiles',
            'view-patient-insurance',
            'view-own-appointments',
            'view-assigned-patients',
            'create-medical-records',
            'view-medical-records',
            'upload-medical-files',
            'book-appointments',
            'manage-own-insurance',
            'manage-medications',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'manage-users',
            'manage-roles',
            'manage-insurance-plans',
            'view-audit-logs',
        ]);

        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->syncPermissions([
            'manage-appointments',
            'view-all-appointments',
            'view-patient-profiles',
            'view-patient-insurance',
        ]);

        $doctor = Role::firstOrCreate(['name' => 'doctor']);
        $doctor->syncPermissions([
            'view-own-appointments',
            'view-assigned-patients',
            'create-medical-records',
            'view-medical-records',
            'upload-medical-files',
        ]);

        $patient = Role::firstOrCreate(['name' => 'patient']);
        $patient->syncPermissions([
            'view-own-appointments',
            'view-medical-records',
            'book-appointments',
            'manage-own-insurance',
            'manage-medications',
        ]);
    }
}
