@extends('layouts.app')

@section('title', 'Visits Report')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-bar-chart-steps text-teal me-2"></i> Clinical Visit Volume Report</h4>
            <p class="text-muted mb-0">Overview of patient consultations and doctor visit volumes.</p>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card card-custom mb-4">
        <div class="card-body py-3">
            <form action="{{ route('reports.visits') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small text-muted">From Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label small text-muted">To Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-12 col-md-4">
                    <button type="submit" class="btn btn-brand w-100">
                        <i class="bi bi-funnel me-1"></i> Filter Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="card card-custom mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-muted small fw-semibold">Total Completed Consultations</span>
                    <h2 class="fw-bold text-teal mt-1 mb-0">{{ $visits->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card card-custom">
        <div class="card-body p-0">
            @if($visits->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-journal-x fs-1 opacity-50 d-block mb-2"></i>
                    <p class="mb-0">No visits logged for this date range.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th>Visit Code</th>
                                <th>Date</th>
                                <th>Patient Name</th>
                                <th>Physician</th>
                                <th>Diagnosis</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visits as $v)
                                <tr>
                                    <td class="font-monospace fw-bold">{{ $v->visit_code }}</td>
                                    <td class="small">{{ $v->visit_date->format('M d, Y - h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('patients.show', $v->patient) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $v->patient->full_name }}
                                        </a>
                                    </td>
                                    <td>{{ $v->doctor->name }}</td>
                                    <td class="small text-muted">{{ Str::limit($v->diagnosis ?? 'General Consultation', 40) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('visits.show', $v) }}" class="btn btn-sm btn-light border">View Record</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
