@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('content')
<div class="container-fluid px-0">

    <!-- Welcome Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark">Welcome, {{ Auth::user()->name }}</h4>
                <span class="badge bg-teal bg-opacity-10 text-teal border border-teal-subtle font-monospace" style="color: #0d9488;">
                    {{ ucfirst(Auth::user()->role) }}
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1">Here is your real-time clinic queue and financial summary for <strong>{{ date('F j, Y') }}</strong>.</p>
        </div>

        <!-- Quick Actions Toolbar -->
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('patients.create') }}" class="btn btn-brand btn-sm d-flex align-items-center gap-1.5 shadow-sm">
                <i class="bi bi-person-plus-fill"></i> New Patient
            </a>
            <a href="{{ route('appointments.create') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1.5 bg-white shadow-sm fw-semibold">
                <i class="bi bi-calendar-plus-fill"></i> Book Appointment
            </a>
            <a href="{{ route('visits.create') }}" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1.5 bg-white shadow-sm fw-semibold">
                <i class="bi bi-stethoscope"></i> Start Visit
            </a>
            <a href="{{ route('invoices.create') }}" class="btn btn-outline-dark btn-sm d-flex align-items-center gap-1.5 bg-white shadow-sm fw-semibold">
                <i class="bi bi-receipt"></i> Create Bill
            </a>
        </div>
    </div>

    <!-- Stat KPI Cards Grid -->
    <div class="row g-3 mb-4">
        <!-- Today's Appointments -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card border-top border-primary border-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Today's Queue</span>
                    <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-calendar2-check-fill"></i>
                    </div>
                </div>
                <h3 class="fw-extrabold mb-1 text-dark">{{ $todayAppointmentsCount }}</h3>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small">Appointments Today</span>
                    <a href="{{ route('appointments.index') }}" class="small fw-semibold text-primary text-decoration-none">Queue &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Registered Patients -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card border-top border-info border-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Total Patients</span>
                    <div class="stat-icon-wrapper bg-info bg-opacity-10 text-info">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <h3 class="fw-extrabold mb-1 text-dark">{{ $totalPatientsCount }}</h3>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small">Active Demographics</span>
                    <a href="{{ route('patients.index') }}" class="small fw-semibold text-info text-decoration-none">Directory &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card border-top border-success border-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Today's Revenue</span>
                    <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
                <h3 class="fw-extrabold mb-1 text-dark">{{ $setting->currency }}{{ number_format($todayRevenue, 2) }}</h3>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small">Collected Receipts</span>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('reports.revenue') }}" class="small fw-semibold text-success text-decoration-none">Ledger &rarr;</a>
                    @else
                        <span class="small text-muted">Paid Today</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pending Invoices -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card border-top border-warning border-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Unpaid Bills</span>
                    <div class="stat-icon-wrapper bg-warning bg-opacity-15 text-warning">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                </div>
                <h3 class="fw-extrabold mb-1 text-dark">{{ $pendingBillsCount }}</h3>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small">Pending Payments</span>
                    <a href="{{ route('invoices.index', ['status' => 'unpaid']) }}" class="small fw-semibold text-warning text-dark text-decoration-none">Review &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area: Today's Queue & Quick Panels -->
    <div class="row g-4">
        <!-- Today's Queue / Appointments Table -->
        <div class="col-12 col-lg-8">
            <div class="card card-custom h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-teal fs-5"></i> Today's Schedule & Waiting Room
                    </h6>
                    <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-light border fw-medium">View Full Schedule &rarr;</a>
                </div>

                <div class="card-body p-0">
                    @if($todayAppointments->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <div class="rounded-circle bg-light d-inline-flex p-3 mb-3 text-secondary">
                                <i class="bi bi-calendar-x fs-2"></i>
                            </div>
                            <h6 class="fw-semibold text-dark mb-1">No appointments scheduled for today</h6>
                            <p class="small text-muted mb-3">Click "Book Appointment" to add a patient to today's schedule.</p>
                            <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-brand">Book Appointment</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-muted small text-uppercase">
                                    <tr>
                                        <th>Time</th>
                                        <th>Patient Details</th>
                                        <th>Doctor</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th class="text-end">Quick Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayAppointments as $apt)
                                        <tr>
                                            <td class="fw-bold text-dark">
                                                <i class="bi bi-clock me-1 text-muted"></i>
                                                {{ date('h:i A', strtotime($apt->appointment_time)) }}
                                            </td>
                                            <td>
                                                <a href="{{ route('patients.show', $apt->patient) }}" class="fw-bold text-dark text-decoration-none">
                                                    {{ $apt->patient->full_name }}
                                                </a>
                                                <div class="text-muted small">MRN: {{ $apt->patient->patient_code }}</div>
                                            </td>
                                            <td class="small">{{ $apt->doctor->name }}</td>
                                            <td class="small text-muted">{{ Str::limit($apt->reason ?? 'General Consultation', 25) }}</td>
                                            <td>
                                                @php
                                                    $statusClasses = [
                                                        'scheduled' => 'bg-info bg-opacity-10 text-info border border-info-subtle',
                                                        'checked_in' => 'bg-warning bg-opacity-20 text-dark border border-warning',
                                                        'in_consultation' => 'bg-primary text-white',
                                                        'completed' => 'bg-success text-white',
                                                        'cancelled' => 'bg-secondary text-white',
                                                    ];
                                                @endphp
                                                <span class="badge {{ $statusClasses[$apt->status] ?? 'bg-light text-dark' }} px-2 py-1">
                                                    {{ ucfirst(str_replace('_', ' ', $apt->status)) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light border dropdown-toggle fw-medium" type="button" data-bs-toggle="dropdown">
                                                        Manage
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                        @if($apt->status === 'checked_in' || $apt->status === 'scheduled')
                                                            <li>
                                                                <a href="{{ route('visits.create', ['appointment_id' => $apt->id]) }}" class="dropdown-item text-success fw-medium">
                                                                    <i class="bi bi-stethoscope me-2"></i> Start Consultation
                                                                </a>
                                                            </li>
                                                        @endif
                                                        
                                                        @if($apt->status !== 'checked_in')
                                                            <li>
                                                                <form action="{{ route('appointments.status', $apt) }}" method="POST">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="status" value="checked_in">
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="bi bi-person-check me-2 text-warning"></i> Mark Checked-In
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif

                                                        <li>
                                                            <a href="{{ route('invoices.create', ['patient_id' => $apt->patient_id]) }}" class="dropdown-item">
                                                                <i class="bi bi-receipt me-2 text-primary"></i> Issue Bill
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
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

        <!-- Right Quick Cards Side Panels -->
        <div class="col-12 col-lg-4">
            <!-- Recent Registrations -->
            <div class="card card-custom mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus-fill text-primary fs-5"></i> Recent Registrations
                    </h6>
                    <a href="{{ route('patients.index') }}" class="small fw-semibold text-decoration-none">View All</a>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($recentPatients as $p)
                        <li class="list-group-item d-flex align-items-center justify-content-between py-2.5 px-3">
                            <div>
                                <a href="{{ route('patients.show', $p) }}" class="fw-bold text-dark text-decoration-none small">
                                    {{ $p->full_name }}
                                </a>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    {{ $p->patient_code }} | {{ $p->gender }} | {{ $p->phone }}
                                </div>
                            </div>
                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.68rem;">{{ $p->created_at->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-4 text-muted small">No recent patients registered.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Recent Billing Invoices -->
            <div class="card card-custom">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-receipt text-warning fs-5"></i> Recent Invoices
                    </h6>
                    <a href="{{ route('invoices.index') }}" class="small fw-semibold text-decoration-none">View All</a>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($recentInvoices as $inv)
                        <li class="list-group-item d-flex align-items-center justify-content-between py-2.5 px-3">
                            <div>
                                <a href="{{ route('invoices.show', $inv) }}" class="fw-bold font-monospace text-dark text-decoration-none small">
                                    {{ $inv->invoice_number }}
                                </a>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    {{ $inv->patient->full_name }}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-dark small">{{ $setting->currency }}{{ number_format($inv->total_amount, 2) }}</div>
                                <span class="badge {{ $inv->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}" style="font-size: 0.65rem;">
                                    {{ ucfirst($inv->payment_status) }}
                                </span>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-4 text-muted small">No recent invoices found.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
