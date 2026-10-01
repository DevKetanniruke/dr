@extends('layouts.app')

@section('title', 'Appointment Schedule')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-calendar-event text-teal me-2"></i> Appointments & Queue</h4>
            <p class="text-muted mb-0">Manage doctor schedules, waiting room queues, and appointment statuses.</p>
        </div>
        <a href="{{ route('appointments.create') }}" class="btn btn-brand shadow-sm d-inline-flex align-items-center gap-2">
            <i class="bi bi-calendar-plus fs-5"></i> Book Appointment
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="card card-custom mb-4">
        <div class="card-body py-3">
            <form action="{{ route('appointments.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small text-muted mb-1">Appointment Date</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}" onchange="this.form.submit()">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small text-muted mb-1">Filter Doctor</label>
                    <select name="doctor_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Doctors</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>
                                {{ $doc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small text-muted mb-1">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>Checked-In / Waiting</option>
                        <option value="in_consultation" {{ request('status') === 'in_consultation' ? 'selected' : '' }}>In Consultation</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3 d-flex align-items-end">
                    <a href="{{ route('appointments.index') }}" class="btn btn-light border w-100">Reset Filters</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="card card-custom">
        <div class="card-body p-0">
            @if($appointments->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1 opacity-50 d-block mb-2"></i>
                    <p class="mb-1 fw-medium">No appointments found matching filters.</p>
                    <small class="text-muted">Select a different date or click "Book Appointment" to schedule one.</small>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th>Time</th>
                                <th>Patient Details</th>
                                <th>Doctor</th>
                                <th>Reason / Notes</th>
                                <th>Queue Status</th>
                                <th class="text-end">Manage & Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $apt)
                                <tr>
                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-clock me-1 text-teal"></i>
                                        {{ date('h:i A', strtotime($apt->appointment_time)) }}
                                        <div class="text-muted small" style="font-size: 0.75rem;">{{ $apt->duration_minutes }} mins</div>
                                    </td>
                                    <td>
                                        <a href="{{ route('patients.show', $apt->patient) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $apt->patient->full_name }}
                                        </a>
                                        <div class="text-muted small">MRN: {{ $apt->patient->patient_code }} | Ph: {{ $apt->patient->phone }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-dark">{{ $apt->doctor->name }}</div>
                                        <div class="text-muted small">{{ $apt->doctor->specialization ?? 'Doctor' }}</div>
                                    </td>
                                    <td class="small text-muted">
                                        {{ $apt->reason ?? 'General Consultation' }}
                                    </td>
                                    <td>
                                        @php
                                            $badgeClasses = [
                                                'scheduled' => 'bg-info bg-opacity-10 text-info border border-info-subtle',
                                                'checked_in' => 'bg-warning bg-opacity-20 text-dark border border-warning',
                                                'in_consultation' => 'bg-primary text-white',
                                                'completed' => 'bg-success text-white',
                                                'cancelled' => 'bg-secondary text-white',
                                                'no_show' => 'bg-danger text-white',
                                            ];
                                        @endphp
                                        <span class="badge {{ $badgeClasses[$apt->status] ?? 'bg-light text-dark' }} px-2 py-1">
                                            {{ ucfirst(str_replace('_', ' ', $apt->status)) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Update Status
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                @if($apt->status !== 'completed')
                                                    <li>
                                                        <a href="{{ route('visits.create', ['appointment_id' => $apt->id]) }}" class="dropdown-item text-success fw-medium">
                                                            <i class="bi bi-stethoscope me-2"></i> Start Consultation
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                @endif

                                                @foreach(['scheduled', 'checked_in', 'in_consultation', 'completed', 'cancelled', 'no_show'] as $st)
                                                    @if($st !== $apt->status)
                                                        <li>
                                                            <form action="{{ route('appointments.status', $apt) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="{{ $st }}">
                                                                <button type="submit" class="dropdown-item py-1">
                                                                    Mark as {{ ucfirst(str_replace('_', ' ', $st)) }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                @endforeach

                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('appointments.destroy', $apt) }}" method="POST" onsubmit="return confirm('Cancel and archive this appointment?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-x-circle me-2"></i> Cancel Appointment
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $appointments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
