<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;
        $role = $user->getRoleNames()->first();

        $page = match ($role) {
            'doctor' => 'Doctor/Profile',
            'staff' => 'Staff/Profile',
            'admin' => 'Admin/Profile',
            default => 'Patient/Profile',
        };

        return Inertia::render($page, [
            'userName' => $user->name,
            'profile' => $profile,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'codice_fiscale' => 'nullable|string|max:16',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:2',
        ]);

        $user = $request->user();
        $user->update(['name' => $validated['name']]);

        $profileData = collect($validated)->except('name')->toArray();
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return back()->with('flash', ['success' => 'Profile updated successfully.']);
    }
}
