<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor']);

        // Date filter (defaults to today if not provided or 'all')
        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->input('date'));
        } else {
            $query->whereDate('appointment_date', date('Y-m-d'));
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->input('doctor_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $doctors = User::where('role', 'doctor')->where('is_active', true)->get();
        $appointments = $query->orderBy('appointment_time', 'asc')->paginate(15)->withQueryString();

        return view('appointments.index', compact('appointments', 'doctors'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('first_name')->get();
        $doctors = User::where('role', 'doctor')->where('is_active', true)->get();
        $selectedPatientId = $request->input('patient_id');
        $setting = ClinicSetting::getSettings();

        return view('appointments.create', compact('patients', 'doctors', 'selectedPatientId', 'setting'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'duration_minutes' => 'required|integer|min:5|max:120',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Validate schedule conflict
        $existing = Appointment::where('doctor_id', $validated['doctor_id'])
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->whereIn('status', ['scheduled', 'checked_in', 'in_consultation'])
            ->first();

        if ($existing) {
            return back()->withErrors([
                'appointment_time' => 'Doctor already has an active appointment booked for this time slot.',
            ])->withInput();
        }

        $validated['status'] = 'scheduled';
        $validated['created_by'] = Auth::id();

        $appointment = Appointment::create($validated);

        return redirect()->route('appointments.index', ['date' => $appointment->appointment_date->format('Y-m-d')])
            ->with('success', 'Appointment booked successfully.');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,checked_in,in_consultation,completed,cancelled,no_show',
        ]);

        $appointment->update(['status' => $validated['status']]);

        return back()->with('success', 'Appointment status updated to ' . ucfirst(str_replace('_', ' ', $appointment->status)));
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);
        $appointment->delete();

        return back()->with('success', 'Appointment has been cancelled and archived.');
    }
}
