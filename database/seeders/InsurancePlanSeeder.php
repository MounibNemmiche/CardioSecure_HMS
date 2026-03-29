<?php

namespace Database\Seeders;

use App\Models\InsurancePlan;
use Illuminate\Database\Seeder;

class InsurancePlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'SSN / Tessera Sanitaria',
                'code' => 'ssn',
                'description' => 'Servizio Sanitario Nazionale — copertura pubblica italiana',
                'is_active' => true,
            ],
            [
                'name' => 'AXA Assicurazioni',
                'code' => 'axa',
                'description' => 'Polizza sanitaria privata AXA',
                'is_active' => true,
            ],
            [
                'name' => 'Allianz Salute',
                'code' => 'allianz',
                'description' => 'Polizza sanitaria privata Allianz',
                'is_active' => true,
            ],
            [
                'name' => 'WAI / Waitaly',
                'code' => 'wai',
                'description' => 'Polizza sanitaria WAI per residenti stranieri in Italia',
                'is_active' => true,
            ],
            [
                'name' => 'Altro',
                'code' => 'other',
                'description' => 'Altra assicurazione sanitaria non elencata',
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            InsurancePlan::firstOrCreate(['code' => $plan['code']], $plan);
        }
    }
}
