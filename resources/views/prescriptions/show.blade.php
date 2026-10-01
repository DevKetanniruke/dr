@extends('layouts.app')

@section('title', 'Prescription Details')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-capsule text-teal me-2"></i> Prescription Overview</h4>
            <p class="text-muted mb-0">Written on {{ $prescription->prescription_date->format('F j, Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('prescriptions.print', $prescription) }}" target="_blank" class="btn btn-brand btn-sm">
                <i class="bi bi-printer me-1"></i> Print RX Format
            </a>
            <a href="{{ route('prescriptions.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Log
            </a>
        </div>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-md-6">
                    <span class="text-uppercase text-muted small fw-semibold">Patient Name</span>
                    <h5 class="fw-bold">
                        <a href="{{ route('patients.show', $prescription->patient) }}" class="text-dark text-decoration-none">
                            {{ $prescription->patient->full_name }}
                        </a>
                        <span class="small text-muted font-monospace">({{ $prescription->patient->patient_code }})</span>
                    </h5>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-uppercase text-muted small fw-semibold">Prescribing Physician</span>
                    <h5 class="fw-bold text-dark">{{ $prescription->doctor->name }}</h5>
                </div>
            </div>

            <h6 class="fw-bold text-uppercase text-teal mb-3" style="font-size: 0.8rem;">Prescribed Medicines:</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Medicine</th>
                            <th>Dosage</th>
                            <th>Frequency</th>
                            <th>Duration</th>
                            <th>Instructions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prescription->items as $item)
                            <tr>
                                <td class="fw-bold">{{ $item->medicine_name }}</td>
                                <td>{{ $item->dosage }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $item->frequency }}</span></td>
                                <td>{{ $item->duration }}</td>
                                <td class="small text-muted">{{ $item->instructions ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($prescription->general_instructions)
                <div class="p-3 bg-light rounded border">
                    <strong class="text-dark d-block mb-1">General Advice:</strong>
                    <p class="mb-0 text-muted small">{{ $prescription->general_instructions }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
