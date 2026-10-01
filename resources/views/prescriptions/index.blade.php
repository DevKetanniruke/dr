@extends('layouts.app')

@section('title', 'Prescriptions Log')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-capsule text-teal me-2"></i> Digital Prescriptions</h4>
            <p class="text-muted mb-0">Manage digital prescriptions and printable RX orders.</p>
        </div>
        <a href="{{ route('prescriptions.create') }}" class="btn btn-brand shadow-sm d-inline-flex align-items-center gap-2">
            <i class="bi bi-plus-circle fs-5"></i> Write New Prescription
        </a>
    </div>

    <div class="card card-custom">
        <div class="card-body p-0">
            @if($prescriptions->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-capsule-off fs-1 opacity-50 d-block mb-2"></i>
                    <p class="mb-0 fw-medium">No prescriptions found.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th>Date</th>
                                <th>Patient Name</th>
                                <th>Prescribing Doctor</th>
                                <th>Items</th>
                                <th>General Instructions</th>
                                <th class="text-end">Print / View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prescriptions as $rx)
                                <tr>
                                    <td class="fw-bold">{{ $rx->prescription_date->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('patients.show', $rx->patient) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $rx->patient->full_name }}
                                        </a>
                                        <div class="text-muted small">MRN: {{ $rx->patient->patient_code }}</div>
                                    </td>
                                    <td>{{ $rx->doctor->name }}</td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info border font-monospace">
                                            {{ $rx->items->count() }} Medicines
                                        </span>
                                    </td>
                                    <td class="small text-muted">{{ Str::limit($rx->general_instructions ?? '—', 35) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('prescriptions.print', $rx) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                            <i class="bi bi-printer me-1"></i> Print RX
                                        </a>
                                        <a href="{{ route('prescriptions.show', $rx) }}" class="btn btn-sm btn-light border">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $prescriptions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
