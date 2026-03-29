<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\InsurancePlan;
use App\Models\PatientInsurance;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InsuranceController extends Controller
{
    public function show(Request $request)
    {
        $patient = $request->user()->patient;
        $insurance = $patient?->insurance()->with('plan')->first();
        $plans = InsurancePlan::where('is_active', true)->get(['id', 'name', 'code']);

        AuditService::log('insurance_viewed', PatientInsurance::class, $insurance?->id);

        return Inertia::render('Patient/Insurance', [
            'insurance' => $insurance ? [
                'insurance_plan_id' => $insurance->insurance_plan_id,
                'policy_number' => $insurance->policy_number,
                'holder_name' => $insurance->holder_name,
                'tessera_sanitaria_number' => $insurance->tessera_sanitaria_number,
                'notes' => $insurance->notes,
                'plan' => $insurance->plan?->name,
            ] : null,
            'plans' => $plans,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'insurance_plan_id' => 'required|exists:insurance_plans,id',
            'policy_number' => 'nullable|string|max:255',
            'holder_name' => 'nullable|string|max:255',
            'tessera_sanitaria_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $patient = $request->user()->patient;

        $patient->insurance()->updateOrCreate(
            ['patient_id' => $patient->id],
            $validated
        );

        return back()->with('flash', ['success' => 'Insurance information saved.']);
    }
}
