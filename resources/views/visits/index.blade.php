@extends('layouts.app')

@section('title', 'Consultations History')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-clipboard2-pulse text-teal me-2"></i> Consultations Log</h4>
            <p class="text-muted mb-0">Clinical visits, vitals records, and diagnostic history.</p>
        </div>
        <a href="{{ route('visits.create') }}" class="btn btn-brand shadow-sm d-inline-flex align-items-center gap-2">
            <i class="bi bi-stethoscope fs-5"></i> Start New Consultation
        </a>
    </div>

    <!-- Visits Table -->
    <div class="card card-custom">
        <div class="card-body p-0">
            @if($visits->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-journal-x fs-1 opacity-50 d-block mb-2"></i>
                    <p class="mb-1 fw-medium">No consultation records recorded yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th>Visit Code</th>
                                <th>Date & Time</th>
                                <th>Patient Name</th>
                                <th>Attending Doctor</th>
                                <th>Chief Complaint / Diagnosis</th>
                                <th>Vitals Summary</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visits as $v)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">{{ $v->visit_code }}</span>
                                    </td>
                                    <td class="fw-semibold">
                                        {{ $v->visit_date->format('M d, Y - h:i A') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('patients.show', $v->patient) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $v->patient->full_name }}
                                        </a>
                                        <div class="text-muted small">MRN: {{ $v->patient->patient_code }}</div>
                                    </td>
                                    <td>{{ $v->doctor->name }}</td>
                                    <td class="small">
                                        <div class="fw-medium text-dark">{{ Str::limit($v->chief_complaint ?? 'General Consultation', 30) }}</div>
                                        @if($v->diagnosis)
                                            <div class="text-muted" style="font-size: 0.75rem;">Diag: {{ Str::limit($v->diagnosis, 35) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($v->systolic_bp && $v->diastolic_bp)
                                            <span class="badge bg-light text-dark border me-1">BP {{ $v->systolic_bp }}/{{ $v->diastolic_bp }}</span>
                                        @endif
                                        @if($v->temperature)
                                            <span class="badge bg-light text-dark border">{{ $v->temperature }}°F</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('visits.show', $v) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye me-1"></i> View Record
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $visits->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
