<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClinicSettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Clinic CRM Application Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Patients
    Route::resource('patients', PatientController::class);

    // Appointments
    Route::resource('appointments', AppointmentController::class)->except(['show', 'edit', 'update']);
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');

    // Visits / Consultations
    Route::resource('visits', VisitController::class)->except(['edit', 'update', 'destroy']);

    // Prescriptions
    Route::resource('prescriptions', PrescriptionController::class)->except(['edit', 'update', 'destroy']);
    Route::get('/prescriptions/{prescription}/print', [PrescriptionController::class, 'print'])->name('prescriptions.print');

    // Invoices & Payments
    Route::resource('invoices', InvoiceController::class)->except(['edit', 'update']);
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('invoices.payments.store');

    // Reports (Admin & Doctor focus)
    Route::middleware('role:admin')->group(function () {
        Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    });
    Route::middleware('role:admin,doctor')->group(function () {
        Route::get('/reports/visits', [ReportController::class, 'visits'])->name('reports.visits');
    });

    // User Staff Management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    // Clinic Settings
    Route::get('/settings', [ClinicSettingController::class, 'edit'])->name('settings.edit');
    Route::middleware('role:admin')->group(function () {
        Route::put('/settings', [ClinicSettingController::class, 'update'])->name('settings.update');
    });
});
