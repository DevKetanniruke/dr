@extends('layouts.app')

@section('title', 'Patient Directory')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-people-fill text-teal me-2"></i> Patient Directory</h4>
            <p class="text-muted mb-0">Manage clinic patient records, demographics, and clinical history.</p>
        </div>
        <a href="{{ route('patients.create') }}" class="btn btn-brand shadow-sm d-inline-flex align-items-center gap-2">
            <i class="bi bi-person-plus-fill fs-5"></i> Register New Patient
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="card card-custom mb-4">
        <div class="card-body py-3">
            <form action="{{ route('patients.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by Patient Code (MRN), Name, Phone, or Email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter me-1"></i> Search
                    </button>
                </div>
                @if(request('search'))
                    <div class="col-12 col-md-auto">
                        <a href="{{ route('patients.index') }}" class="btn btn-light border text-muted">Clear Filter</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Patients List Table -->
    <div class="card card-custom">
        <div class="card-body p-0">
            @if($patients->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-person-exclamation fs-1 opacity-50 d-block mb-2"></i>
                    <p class="mb-1 fw-medium">No patient records found.</p>
                    <small class="text-muted">Try adjusting your search criteria or register a new patient.</small>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th>MRN / Code</th>
                                <th>Patient Name</th>
                                <th>Gender / Age</th>
                                <th>Phone Number</th>
                                <th>Blood Group</th>
                                <th>Registered</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $patient)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">{{ $patient->patient_code }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('patients.show', $patient) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $patient->full_name }}
                                        </a>
                                        @if($patient->email)
                                            <div class="text-muted small">{{ $patient->email }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-capitalize">{{ $patient->gender }}</span>
                                        @if($patient->age)
                                            <span class="text-muted small">({{ $patient->age }} yrs)</span>
                                        @endif
                                    </td>
                                    <td>
                                        <i class="bi bi-telephone me-1 text-muted"></i> {{ $patient->phone }}
                                    </td>
                                    <td>
                                        @if($patient->blood_group)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle">{{ $patient->blood_group }}</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">
                                        {{ $patient->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                <li>
                                                    <a href="{{ route('patients.show', $patient) }}" class="dropdown-item">
                                                        <i class="bi bi-eye me-2 text-primary"></i> View Timeline Profile
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="dropdown-item">
                                                        <i class="bi bi-calendar-plus me-2 text-success"></i> Book Appointment
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('visits.create', ['patient_id' => $patient->id]) }}" class="dropdown-item">
                                                        <i class="bi bi-stethoscope me-2 text-info"></i> Start Consultation
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('invoices.create', ['patient_id' => $patient->id]) }}" class="dropdown-item">
                                                        <i class="bi bi-receipt me-2 text-warning"></i> Issue Bill
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a href="{{ route('patients.edit', $patient) }}" class="dropdown-item">
                                                        <i class="bi bi-pencil me-2 text-secondary"></i> Edit Demographics
                                                    </a>
                                                </li>
                                                @if(Auth::user()->isAdmin())
                                                    <li>
                                                        <form action="{{ route('patients.destroy', $patient) }}" method="POST" onsubmit="return confirm('Archive patient record? (Can be restored by DB admin)');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="bi bi-archive me-2"></i> Archive Patient
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Server-side Pagination -->
                <div class="p-3 border-top">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
