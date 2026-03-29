<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Profile;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->get()->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => $u->getRoleNames()->first() ?? '—',
            'verified' => (bool) $u->email_verified_at,
            'mfa' => (bool) $u->two_factor_confirmed_at,
            'created_at' => $u->created_at->toDateString(),
        ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => ['admin', 'staff', 'doctor', 'patient'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()->symbols()],
            'role' => 'required|in:admin,staff,doctor,patient',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        Profile::create(['user_id' => $user->id]);

        if ($validated['role'] === 'doctor') {
            Doctor::create(['user_id' => $user->id, 'specialization' => 'Cardiologia']);
        } elseif ($validated['role'] === 'patient') {
            Patient::create(['user_id' => $user->id]);
        }

        AuditService::log('user_created', User::class, $user->id, true, ['role' => $validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('flash', ['success' => 'User created successfully.']);
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'editUser' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first() ?? '',
            ],
            'roles' => ['admin', 'staff', 'doctor', 'patient'],
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,staff,doctor,patient',
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $oldRole = $user->getRoleNames()->first();
        if ($oldRole !== $validated['role']) {
            $user->syncRoles([$validated['role']]);

            if ($validated['role'] === 'doctor' && !$user->doctor) {
                Doctor::create(['user_id' => $user->id, 'specialization' => 'Cardiologia']);
            } elseif ($validated['role'] === 'patient' && !$user->patient) {
                Patient::create(['user_id' => $user->id]);
            }

            AuditService::log('user_role_changed', User::class, $user->id, true, ['from' => $oldRole, 'to' => $validated['role']]);
        }

        AuditService::log('user_updated', User::class, $user->id);

        return redirect()->route('admin.users.index')
            ->with('flash', ['success' => 'User updated successfully.']);
    }
}
