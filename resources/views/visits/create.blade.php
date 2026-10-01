@extends('layouts.app')

@section('title', 'New Consultation & Vitals')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-stethoscope text-teal me-2"></i> Record Patient Consultation</h4>
            <p class="text-muted mb-0">Record patient vitals, examination notes, and clinical diagnosis.</p>
        </div>
        <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Log
        </a>
    </div>

    <div class="card card-custom">
        <div class="card-body p-4">
            <form action="{{ route('visits.store') }}" method="POST">
                @csrf
                @if($appointmentId)
                    <input type="hidden" name="appointment_id" value="{{ $appointmentId }}">
                @endif

                <!-- Basic Visit Details -->
                <h6 class="fw-bold text-uppercase text-teal mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    <i class="bi bi-person-lines-fill me-1"></i> Patient & Physician Information
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-5">
                        <label for="patient_id" class="form-label fw-medium">Patient <span class="text-danger">*</span></label>
                        <select name="patient_id" id="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                            <option value="">-- Choose Patient --</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ (old('patient_id', $patientId) == $patient->id) ? 'selected' : '' }}>
                                    {{ $patient->full_name }} ({{ $patient->patient_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="doctor_id" class="form-label fw-medium">Attending Doctor <span class="text-danger">*</span></label>
                        <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                            <option value="">-- Choose Doctor --</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ (old('doctor_id', Auth::id()) == $doctor->id) ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="visit_date" class="form-label fw-medium">Visit Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="visit_date" id="visit_date" class="form-control" value="{{ old('visit_date', date('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>

                <!-- Patient Vitals Card Grid -->
                <div class="bg-light rounded p-3 mb-4 border">
                    <h6 class="fw-bold text-uppercase text-dark mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                        <i class="bi bi-heart-pulse-fill text-danger me-1"></i> Vital Signs & Physical Measurements
                    </h6>

                    <div class="row g-3">
                        <div class="col-6 col-sm-4 col-md-2">
                            <label for="systolic_bp" class="form-label small text-muted">Systolic BP (mmHg)</label>
                            <input type="number" name="systolic_bp" id="systolic_bp" class="form-control form-control-sm" value="{{ old('systolic_bp') }}" placeholder="120">
                        </div>
                        <div class="col-6 col-sm-4 col-md-2">
                            <label for="diastolic_bp" class="form-label small text-muted">Diastolic BP (mmHg)</label>
                            <input type="number" name="diastolic_bp" id="diastolic_bp" class="form-control form-control-sm" value="{{ old('diastolic_bp') }}" placeholder="80">
                        </div>
                        <div class="col-6 col-sm-4 col-md-2">
                            <label for="pulse_rate" class="form-label small text-muted">Pulse (bpm)</label>
                            <input type="number" name="pulse_rate" id="pulse_rate" class="form-control form-control-sm" value="{{ old('pulse_rate') }}" placeholder="72">
                        </div>
                        <div class="col-6 col-sm-4 col-md-2">
                            <label for="temperature" class="form-label small text-muted">Temp (°F)</label>
                            <input type="number" step="0.1" name="temperature" id="temperature" class="form-control form-control-sm" value="{{ old('temperature') }}" placeholder="98.6">
                        </div>
                        <div class="col-6 col-sm-4 col-md-2">
                            <label for="weight_kg" class="form-label small text-muted">Weight (kg)</label>
                            <input type="number" step="0.1" name="weight_kg" id="weight_kg" class="form-control form-control-sm" value="{{ old('weight_kg') }}" placeholder="70.5" oninput="calculateBMI()">
                        </div>
                        <div class="col-6 col-sm-4 col-md-2">
                            <label for="height_cm" class="form-label small text-muted">Height (cm)</label>
                            <input type="number" step="0.1" name="height_cm" id="height_cm" class="form-control form-control-sm" value="{{ old('height_cm') }}" placeholder="175" oninput="calculateBMI()">
                        </div>
                    </div>

                    <div class="mt-2 text-muted small">
                        <strong>Calculated BMI:</strong> <span id="bmiDisplay" class="badge bg-secondary">Enter Weight & Height</span>
                    </div>
                </div>

                <!-- Clinical Notes & Diagnosis -->
                <h6 class="fw-bold text-uppercase text-teal mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    <i class="bi bi-journal-text me-1"></i> Clinical Evaluation & Advice
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label for="chief_complaint" class="form-label fw-medium">Chief Complaint / Symptoms</label>
                        <textarea name="chief_complaint" id="chief_complaint" rows="3" class="form-control" placeholder="Patient reported symptoms e.g. Sore throat, fever for 2 days">{{ old('chief_complaint', optional($appointment)->reason) }}</textarea>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="diagnosis" class="form-label fw-medium">Clinical Diagnosis / Impression</label>
                        <textarea name="diagnosis" id="diagnosis" rows="3" class="form-control" placeholder="e.g. Acute Tonsillitis (J03.90)">{{ old('diagnosis') }}</textarea>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="clinical_notes" class="form-label fw-medium">Examination Notes & Observations</label>
                        <textarea name="clinical_notes" id="clinical_notes" rows="3" class="form-control" placeholder="Physical examination findings">{{ old('clinical_notes') }}</textarea>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="advice" class="form-label fw-medium">Physician Advice & Instructions</label>
                        <textarea name="advice" id="advice" rows="3" class="form-control" placeholder="Dietary advice, rest, precautions">{{ old('advice') }}</textarea>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="follow_up_date" class="form-label fw-medium">Recommended Follow-up Date</label>
                        <input type="date" name="follow_up_date" id="follow_up_date" class="form-control" value="{{ old('follow_up_date') }}">
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('visits.index') }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-brand px-4">
                        <i class="bi bi-check-lg me-1"></i> Save Consultation Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function calculateBMI() {
        const weight = parseFloat(document.getElementById('weight_kg').value);
        const height = parseFloat(document.getElementById('height_cm').value);
        const bmiDisplay = document.getElementById('bmiDisplay');

        if (weight > 0 && height > 0) {
            const heightM = height / 100;
            const bmi = (weight / (heightM * heightM)).toFixed(1);
            let category = '';
            let badgeClass = 'bg-secondary';

            if (bmi < 18.5) { category = 'Underweight'; badgeClass = 'bg-info text-dark'; }
            else if (bmi < 24.9) { category = 'Normal Weight'; badgeClass = 'bg-success'; }
            else if (bmi < 29.9) { category = 'Overweight'; badgeClass = 'bg-warning text-dark'; }
            else { category = 'Obese'; badgeClass = 'bg-danger'; }

            bmiDisplay.className = 'badge ' + badgeClass;
            bmiDisplay.innerText = bmi + ' (' + category + ')';
        } else {
            bmiDisplay.className = 'badge bg-secondary';
            bmiDisplay.innerText = 'Enter Weight & Height';
        }
    }
</script>
@endpush
@endsection
