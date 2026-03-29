<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InsurancePlanController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\MedicalRecordController as DoctorMedicalRecordController;
use App\Http\Controllers\Doctor\PatientController as DoctorPatientController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\InsuranceController;
use App\Http\Controllers\Patient\MedicalRecordController as PatientMedicalRecordController;
use App\Http\Controllers\Patient\MedicationReminderController;
use App\Http\Controllers\Shared\FileDownloadController;
use App\Http\Controllers\Shared\ProfileController;
use App\Http\Controllers\Shared\PushSubscriptionController;
use App\Http\Controllers\Staff\AppointmentController as StaffAppointmentController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\PatientController as StaffPatientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Landing page
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Role-based dashboard redirect (Fortify posts here after login)
Route::middleware(['auth', 'verified', 'mfa'])->get('/dashboard', function (Request $request) {
    $user = $request->user();
    if ($user->hasRole('admin')) return redirect()->route('admin.dashboard');
    if ($user->hasRole('doctor')) return redirect()->route('doctor.dashboard');
    if ($user->hasRole('staff')) return redirect()->route('staff.dashboard');
    return redirect()->route('patient.dashboard');
})->name('dashboard');

// MFA Setup (authenticated but not yet MFA-confirmed)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mfa/setup', function () {
        return Inertia::render('Auth/MfaSetup');
    })->name('mfa.setup');
});

// Protected routes — auth + email verified + MFA confirmed
Route::middleware(['auth', 'verified', 'mfa'])->group(function () {

    // Shared file download
    Route::get('/files/{file}/download', FileDownloadController::class)->name('files.download');

    // Push subscription
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');

    // Patient routes
    Route::middleware('role:patient')->prefix('patient')->name('patient.')->group(function () {
        Route::get('/dashboard', PatientDashboardController::class)->name('dashboard');
        Route::get('/appointments', [PatientAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/create', [PatientAppointmentController::class, 'create'])->name('appointments.create');
        Route::get('/appointments/available-slots', [PatientAppointmentController::class, 'availableSlots'])->name('appointments.slots');
        Route::post('/appointments', [PatientAppointmentController::class, 'store'])->name('appointments.store');
        Route::patch('/appointments/{appointment}/cancel', [PatientAppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::get('/records', [PatientMedicalRecordController::class, 'index'])->name('records.index');
        Route::get('/records/{record}', [PatientMedicalRecordController::class, 'show'])->name('records.show');
        Route::get('/medications', [MedicationReminderController::class, 'index'])->name('medications.index');
        Route::post('/medications', [MedicationReminderController::class, 'store'])->name('medications.store');
        Route::put('/medications/{reminder}', [MedicationReminderController::class, 'update'])->name('medications.update');
        Route::patch('/medications/{reminder}/toggle', [MedicationReminderController::class, 'toggleActive'])->name('medications.toggle');
        Route::delete('/medications/{reminder}', [MedicationReminderController::class, 'destroy'])->name('medications.destroy');
        Route::get('/insurance', [InsuranceController::class, 'show'])->name('insurance');
        Route::put('/insurance', [InsuranceController::class, 'update'])->name('insurance.update');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });

    // Doctor routes
    Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', DoctorDashboardController::class)->name('dashboard');
        Route::get('/appointments', [DoctorAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/patients', [DoctorPatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/{patient}/records', [DoctorMedicalRecordController::class, 'index'])->name('patients.records.index');
        Route::get('/patients/{patient}/records/create', [DoctorMedicalRecordController::class, 'create'])->name('patients.records.create');
        Route::post('/patients/{patient}/records', [DoctorMedicalRecordController::class, 'store'])->name('patients.records.store');
        Route::get('/patients/{patient}/records/{record}', [DoctorMedicalRecordController::class, 'show'])->name('patients.records.show');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });

    // Staff routes
    Route::middleware('role:staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', StaffDashboardController::class)->name('dashboard');
        Route::get('/appointments', [StaffAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/available-slots', [StaffAppointmentController::class, 'availableSlots'])->name('appointments.slots');
        Route::patch('/appointments/{appointment}/reschedule', [StaffAppointmentController::class, 'reschedule'])->name('appointments.reschedule');
        Route::patch('/appointments/{appointment}/cancel', [StaffAppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::patch('/appointments/{appointment}/status', [StaffAppointmentController::class, 'updateStatus'])->name('appointments.status');
        Route::get('/patients', [StaffPatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/{patient}', [StaffPatientController::class, 'show'])->name('patients.show');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::resource('users', UserManagementController::class)->except(['show', 'destroy']);
        Route::resource('insurance-plans', InsurancePlanController::class)->except(['show']);
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });
});
