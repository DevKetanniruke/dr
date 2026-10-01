<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = date('Y-m-d');
        $setting = ClinicSetting::getSettings();

        // High-level KPI metrics
        $todayAppointmentsCount = Appointment::whereDate('appointment_date', $today)->count();
        $totalPatientsCount = Patient::count();
        
        $todayRevenue = Payment::whereDate('payment_date', $today)->sum('amount');
        $pendingBillsCount = Invoice::where('payment_status', '!=', 'paid')->count();
        $todayVisitsCount = Visit::whereDate('visit_date', $today)->count();

        // Queue / Today's Appointments
        $appointmentQuery = Appointment::with(['patient', 'doctor'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time', 'asc');

        if ($user->isDoctor()) {
            $appointmentQuery->where('doctor_id', $user->id);
        }

        $todayAppointments = $appointmentQuery->get();

        // Recent Patients
        $recentPatients = Patient::latest()->take(5)->get();

        // Recent Invoices
        $recentInvoices = Invoice::with('patient')->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'setting',
            'todayAppointmentsCount',
            'totalPatientsCount',
            'todayRevenue',
            'pendingBillsCount',
            'todayVisitsCount',
            'todayAppointments',
            'recentPatients',
            'recentInvoices'
        ));
    }
}
