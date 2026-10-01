<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $query = Visit::with(['patient', 'doctor']);

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->input('patient_id'));
        }

        if (Auth::user()->isDoctor()) {
            $query->where('doctor_id', Auth::id());
        }

        $visits = $query->latest('visit_date')->paginate(15)->withQueryString();

        return view('visits.index', compact('visits'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('first_name')->get();
        $doctors = User::where('role', 'doctor')->where('is_active', true)->get();
        
        $patientId = $request->input('patient_id');
        $appointmentId = $request->input('appointment_id');
        $appointment = null;

        if ($appointmentId) {
            $appointment = Appointment::find($appointmentId);
            if ($appointment) {
                $patientId = $appointment->patient_id;
            }
        }

        return view('visits.create', compact('patients', 'doctors', 'patientId', 'appointmentId', 'appointment'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'visit_date' => 'required|date',
            'systolic_bp' => 'nullable|integer|min:40|max:300',
            'diastolic_bp' => 'nullable|integer|min:20|max:200',
            'pulse_rate' => 'nullable|integer|min:30|max:250',
            'temperature' => 'nullable|numeric|min:90|max:110',
            'weight_kg' => 'nullable|numeric|min:1|max:400',
            'height_cm' => 'nullable|numeric|min:20|max:300',
            'chief_complaint' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'clinical_notes' => 'nullable|string',
            'advice' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after:today',
        ]);

        $validated['visit_code'] = Visit::generateVisitCode();
        $validated['status'] = 'completed';

        $visit = Visit::create($validated);

        if (!empty($validated['appointment_id'])) {
            $appointment = Appointment::find($validated['appointment_id']);
            if ($appointment) {
                $appointment->update(['status' => 'completed']);
            }
        }

        return redirect()->route('visits.show', $visit)
            ->with('success', "Visit recorded successfully for patient.");
    }

    public function show(Visit $visit)
    {
        $visit->load(['patient', 'doctor', 'prescription.items', 'invoice.items', 'invoice.payments']);
        return view('visits.show', compact('visit'));
    }
}
