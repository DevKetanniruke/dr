@extends('layouts.app')

@section('title', 'Consultation Record - ' . $visit->visit_code)

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0">Consultation Record</h4>
                <span class="badge bg-light text-dark border font-monospace fs-6">{{ $visit->visit_code }}</span>
            </div>
            <p class="text-muted mb-0">Recorded on {{ $visit->visit_date->format('F j, Y \a\t h:i A') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('prescriptions.create', ['visit_id' => $visit->id]) }}" class="btn btn-outline-info btn-sm">
                <i class="bi bi-capsule me-1"></i> Write Prescription
            </a>
            <a href="{{ route('invoices.create', ['visit_id' => $visit->id]) }}" class="btn btn-outline-warning text-dark btn-sm">
                <i class="bi bi-receipt me-1"></i> Create Bill
            </a>
            <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Log
            </a>
        </div>
    </div>

    <!-- Patient Header Card -->
    <div class="card card-custom mb-4">
        <div class="card-body p-3 bg-light rounded-top">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="text-uppercase text-muted small fw-semibold">Patient Profile</span>
                    <h5 class="fw-bold mb-0">
                        <a href="{{ route('patients.show', $visit->patient) }}" class="text-dark text-decoration-none">
                            {{ $visit->patient->full_name }}
                        </a>
                        <span class="small text-muted font-monospace">({{ $visit->patient->patient_code }})</span>
                    </h5>
                    <div class="small text-muted">
                        Gender: <span class="text-capitalize">{{ $visit->patient->gender }}</span> | Age: {{ $visit->patient->age ?? 'N/A' }} yrs | Phone: {{ $visit->patient->phone }}
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-uppercase text-muted small fw-semibold">Attending Physician</span>
                    <h6 class="fw-bold mb-0 text-dark">{{ $visit->doctor->name }}</h6>
                    <div class="small text-muted">{{ $visit->doctor->specialization ?? 'General Physician' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vitals Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-4 col-md-2">
            <div class="card card-custom text-center p-2">
                <span class="text-muted small">Blood Pressure</span>
                <strong class="fs-6 text-danger">
                    {{ ($visit->systolic_bp && $visit->diastolic_bp) ? "{$visit->systolic_bp}/{$visit->diastolic_bp}" : '—' }}
                </strong>
                <small class="text-muted" style="font-size: 0.7rem;">mmHg</small>
            </div>
        </div>

        <div class="col-6 col-sm-4 col-md-2">
            <div class="card card-custom text-center p-2">
                <span class="text-muted small">Pulse Rate</span>
                <strong class="fs-6 text-primary">{{ $visit->pulse_rate ?? '—' }}</strong>
                <small class="text-muted" style="font-size: 0.7rem;">bpm</small>
            </div>
        </div>

        <div class="col-6 col-sm-4 col-md-2">
            <div class="card card-custom text-center p-2">
                <span class="text-muted small">Temperature</span>
                <strong class="fs-6 text-warning">{{ $visit->temperature ? "{$visit->temperature}°F" : '—' }}</strong>
                <small class="text-muted" style="font-size: 0.7rem;">Fahrenheit</small>
            </div>
        </div>

        <div class="col-6 col-sm-4 col-md-2">
            <div class="card card-custom text-center p-2">
                <span class="text-muted small">Weight</span>
                <strong class="fs-6 text-info">{{ $visit->weight_kg ? "{$visit->weight_kg} kg" : '—' }}</strong>
                <small class="text-muted" style="font-size: 0.7rem;">Kilograms</small>
            </div>
        </div>

        <div class="col-6 col-sm-4 col-md-2">
            <div class="card card-custom text-center p-2">
                <span class="text-muted small">Height</span>
                <strong class="fs-6 text-secondary">{{ $visit->height_cm ? "{$visit->height_cm} cm" : '—' }}</strong>
                <small class="text-muted" style="font-size: 0.7rem;">Centimeters</small>
            </div>
        </div>

        <div class="col-6 col-sm-4 col-md-2">
            <div class="card card-custom text-center p-2">
                <span class="text-muted small">BMI Index</span>
                <strong class="fs-6 text-dark">{{ $visit->bmi ?? '—' }}</strong>
                <small class="text-muted" style="font-size: 0.7rem;">kg/m²</small>
            </div>
        </div>
    </div>

    <!-- Clinical Details Grid -->
    <div class="row g-4">
        <div class="col-12 col-md-6">
            <div class="card card-custom h-100">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="bi bi-chat-left-text me-2 text-teal"></i> Chief Complaint & Symptoms
                </div>
                <div class="card-body">
                    <p class="mb-0 text-dark">{{ $visit->chief_complaint ?? 'No specific complaints recorded.' }}</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card card-custom h-100">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="bi bi-file-earmark-medical me-2 text-primary"></i> Clinical Diagnosis
                </div>
                <div class="card-body">
                    <p class="mb-0 text-dark fw-medium">{{ $visit->diagnosis ?? 'No diagnosis specified.' }}</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card card-custom h-100">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="bi bi-journal-text me-2 text-secondary"></i> Examination Notes
                </div>
                <div class="card-body">
                    <p class="mb-0 text-dark">{{ $visit->clinical_notes ?? 'No additional examination notes.' }}</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card card-custom h-100">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="bi bi-lightbulb me-2 text-warning"></i> Physician Advice & Follow-up
                </div>
                <div class="card-body">
                    <p class="mb-2 text-dark">{{ $visit->advice ?? 'Follow routine care.' }}</p>
                    @if($visit->follow_up_date)
                        <div class="mt-2 alert alert-info py-2 mb-0 small">
                            <i class="bi bi-calendar-event me-1"></i> Recommended Follow-up Date: <strong>{{ $visit->follow_up_date->format('F j, Y') }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
