<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsurancePlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InsurancePlanController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/InsurancePlans/Index', [
            'plans' => InsurancePlan::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/InsurancePlans/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:insurance_plans,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        InsurancePlan::create($validated);

        return redirect()->route('admin.insurance-plans.index')
            ->with('flash', ['success' => 'Insurance plan created.']);
    }

    public function edit(InsurancePlan $insurancePlan)
    {
        return Inertia::render('Admin/InsurancePlans/Edit', [
            'plan' => $insurancePlan,
        ]);
    }

    public function update(Request $request, InsurancePlan $insurancePlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:insurance_plans,code,' . $insurancePlan->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $insurancePlan->update($validated);

        return redirect()->route('admin.insurance-plans.index')
            ->with('flash', ['success' => 'Insurance plan updated.']);
    }

    public function destroy(InsurancePlan $insurancePlan)
    {
        $insurancePlan->delete();

        return redirect()->route('admin.insurance-plans.index')
            ->with('flash', ['success' => 'Insurance plan deleted.']);
    }
}
