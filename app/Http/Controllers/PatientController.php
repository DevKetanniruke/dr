<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('patient_code', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'dob' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'blood_group' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $validated['patient_code'] = Patient::generatePatientCode();
        $validated['created_by'] = Auth::id();

        $patient = Patient::create($validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', "Patient {$patient->full_name} ({$patient->patient_code}) registered successfully.");
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'appointments.doctor',
            'visits.doctor',
            'prescriptions.doctor',
            'prescriptions.items',
            'invoices.payments',
        ]);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'dob' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'blood_group' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $patient->update($validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', "Patient {$patient->full_name} updated successfully.");
    }

    public function destroy(Patient $patient)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only clinic administrators can soft-delete patient records.');
        }

        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', "Patient record for {$patient->full_name} has been archived.");
    }
}
