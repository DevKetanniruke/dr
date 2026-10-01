<?php

namespace App\Http\Controllers;

use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::with(['patient', 'doctor', 'visit']);

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->input('patient_id'));
        }

        if (Auth::user()->isDoctor()) {
            $query->where('doctor_id', Auth::id());
        }

        $prescriptions = $query->latest('prescription_date')->paginate(15)->withQueryString();

        return view('prescriptions.index', compact('prescriptions'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('first_name')->get();
        $doctors = User::where('role', 'doctor')->where('is_active', true)->get();

        $patientId = $request->input('patient_id');
        $visitId = $request->input('visit_id');
        $visit = null;

        if ($visitId) {
            $visit = Visit::find($visitId);
            if ($visit) {
                $patientId = $visit->patient_id;
            }
        }

        return view('prescriptions.create', compact('patients', 'doctors', 'patientId', 'visitId', 'visit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'visit_id' => 'nullable|exists:visits,id',
            'prescription_date' => 'required|date',
            'general_instructions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medicine_name' => 'required|string|max:150',
            'items.*.dosage' => 'required|string|max:100',
            'items.*.frequency' => 'required|string|max:100',
            'items.*.duration' => 'required|string|max:100',
            'items.*.instructions' => 'nullable|string|max:255',
        ]);

        $prescription = DB::transaction(function () use ($validated) {
            $rx = Prescription::create([
                'patient_id' => $validated['patient_id'],
                'doctor_id' => $validated['doctor_id'],
                'visit_id' => $validated['visit_id'] ?? null,
                'prescription_date' => $validated['prescription_date'],
                'general_instructions' => $validated['general_instructions'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $rx->items()->create([
                    'medicine_name' => $item['medicine_name'],
                    'dosage' => $item['dosage'],
                    'frequency' => $item['frequency'],
                    'duration' => $item['duration'],
                    'instructions' => $item['instructions'] ?? null,
                ]);
            }

            return $rx;
        });

        return redirect()->route('prescriptions.print', $prescription)
            ->with('success', 'Prescription created successfully.');
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor', 'items', 'visit']);
        $setting = ClinicSetting::getSettings();
        return view('prescriptions.show', compact('prescription', 'setting'));
    }

    public function print(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor', 'items', 'visit']);
        $setting = ClinicSetting::getSettings();
        return view('prescriptions.print', compact('prescription', 'setting'));
    }
}
