@extends('layouts.app')

@section('title', 'Patient Profile - ' . $patient->full_name)

@section('content')
<div class="container-fluid px-0">

    <!-- Header Card -->
    <div class="card card-custom mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-teal text-white d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 64px; height: 64px; background-color: #0d9488;">
                        {{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="fw-bold mb-0 text-dark">{{ $patient->full_name }}</h4>
                            <span class="badge bg-light text-dark border font-monospace">{{ $patient->patient_code }}</span>
                            @if($patient->blood_group)
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle">{{ $patient->blood_group }}</span>
                            @endif
                        </div>
                        <div class="text-muted small mt-1">
                            <span class="text-capitalize me-2"><i class="bi bi-gender-ambiguous"></i> {{ $patient->gender }}</span>
                            @if($patient->age)<span class="me-2">• {{ $patient->age }} years old</span>@endif
                            @if($patient->dob)<span class="me-2">• DOB: {{ $patient->dob->format('M d, Y') }}</span>@endif
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-calendar-plus me-1"></i> Book Appointment
                    </a>
                    <a href="{{ route('visits.create', ['patient_id' => $patient->id]) }}" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-stethoscope me-1"></i> Start Visit
                    </a>
                    <a href="{{ route('prescriptions.create', ['patient_id' => $patient->id]) }}" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-capsule me-1"></i> Prescription
                    </a>
                    <a href="{{ route('invoices.create', ['patient_id' => $patient->id]) }}" class="btn btn-outline-warning text-dark btn-sm">
                        <i class="bi bi-receipt me-1"></i> Issue Bill
                    </a>
                    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-light border btn-sm">
                        <i class="bi bi-pencil me-1"></i> Edit Demographics
                    </a>
                </div>
            </div>

            <hr class="my-3">

            <!-- Contact & Health Alerts Strip -->
            <div class="row g-3 text-sm">
                <div class="col-12 col-sm-6 col-md-3">
                    <span class="text-muted d-block small">Phone Number</span>
                    <strong class="text-dark"><i class="bi bi-telephone text-muted me-1"></i> {{ $patient->phone }}</strong>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <span class="text-muted d-block small">Email Address</span>
                    <strong class="text-dark"><i class="bi bi-envelope text-muted me-1"></i> {{ $patient->email ?? 'Not provided' }}</strong>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <span class="text-muted d-block small">Known Allergies</span>
                    @if($patient->allergies)
                        <span class="badge bg-warning bg-opacity-20 text-dark border border-warning">{{ $patient->allergies }}</span>
                    @else
                        <span class="text-muted">None reported</span>
                    @endif
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <span class="text-muted d-block small">Emergency Contact</span>
                    <strong class="text-dark">{{ $patient->emergency_contact_name ?? 'N/A' }}</strong> 
                    @if($patient->emergency_contact_phone)
                        <small class="text-muted">({{ $patient->emergency_contact_phone }})</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Patient History Nav Tabs -->
    <ul class="nav nav-tabs border-bottom-0 mb-3" id="patientTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-medium px-4" id="visits-tab" data-bs-toggle="tab" data-bs-target="#visits-tab-pane">
                <i class="bi bi-clipboard2-pulse me-1 text-teal"></i> Consultations ({{ $patient->visits->count() }})
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-medium px-4" id="rx-tab" data-bs-toggle="tab" data-bs-target="#rx-tab-pane">
                <i class="bi bi-capsule me-1 text-info"></i> Prescriptions ({{ $patient->prescriptions->count() }})
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-medium px-4" id="bills-tab" data-bs-toggle="tab" data-bs-target="#bills-tab-pane">
                <i class="bi bi-receipt me-1 text-warning"></i> Billing & Payments ({{ $patient->invoices->count() }})
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-medium px-4" id="apts-tab" data-bs-toggle="tab" data-bs-target="#apts-tab-pane">
                <i class="bi bi-calendar-event me-1 text-primary"></i> Appointments ({{ $patient->appointments->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="patientTabsContent">

        <!-- 1. Consultations / Visits Tab -->
        <div class="tab-pane fade show active" id="visits-tab-pane" role="tabpanel">
            <div class="card card-custom">
                <div class="card-body p-0">
                    @if($patient->visits->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-1 opacity-50 d-block mb-2"></i>
                            <p class="mb-0">No consultation records recorded for this patient.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($patient->visits->sortByDesc('visit_date') as $visit)
                                <div class="list-group-item p-4">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div>
                                            <span class="badge bg-light text-dark border font-monospace">{{ $visit->visit_code }}</span>
                                            <strong class="ms-2 text-dark">{{ $visit->visit_date->format('F j, Y - h:i A') }}</strong>
                                            <span class="text-muted ms-2">with {{ $visit->doctor->name }}</span>
                                        </div>
                                        <a href="{{ route('visits.show', $visit) }}" class="btn btn-sm btn-outline-secondary">
                                            View Details &rarr;
                                        </a>
                                    </div>

                                    <!-- Vitals Pill Bar -->
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @if($visit->systolic_bp && $visit->diastolic_bp)
                                            <span class="badge bg-light text-dark border"><i class="bi bi-heart-pulse text-danger me-1"></i> BP: {{ $visit->systolic_bp }}/{{ $visit->diastolic_bp }} mmHg</span>
                                        @endif
                                        @if($visit->pulse_rate)
                                            <span class="badge bg-light text-dark border"><i class="bi bi-activity text-primary me-1"></i> Pulse: {{ $visit->pulse_rate }} bpm</span>
                                        @endif
                                        @if($visit->temperature)
                                            <span class="badge bg-light text-dark border"><i class="bi bi-thermometer-half text-warning me-1"></i> Temp: {{ $visit->temperature }}°F</span>
                                        @endif
                                        @if($visit->weight_kg)
                                            <span class="badge bg-light text-dark border"><i class="bi bi-speedometer text-info me-1"></i> Weight: {{ $visit->weight_kg }} kg</span>
                                        @endif
                                        @if($visit->bmi)
                                            <span class="badge bg-light text-dark border">BMI: {{ $visit->bmi }}</span>
                                        @endif
                                    </div>

                                    @if($visit->chief_complaint)
                                        <p class="mb-1 text-dark"><strong>Chief Complaint:</strong> {{ $visit->chief_complaint }}</p>
                                    @endif

                                    @if($visit->diagnosis)
                                        <p class="mb-0 text-muted small"><strong>Diagnosis:</strong> {{ $visit->diagnosis }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Prescriptions Tab -->
        <div class="tab-pane fade" id="rx-tab-pane" role="tabpanel">
            <div class="card card-custom">
                <div class="card-body p-0">
                    @if($patient->prescriptions->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-capsule fs-1 opacity-50 d-block mb-2"></i>
                            <p class="mb-0">No active or past prescriptions written for this patient.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-muted small text-uppercase">
                                    <tr>
                                        <th>Date</th>
                                        <th>Prescribing Doctor</th>
                                        <th>Medicines Count</th>
                                        <th>Instructions Summary</th>
                                        <th class="text-end">Print / View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patient->prescriptions->sortByDesc('prescription_date') as $rx)
                                        <tr>
                                            <td class="fw-semibold">{{ $rx->prescription_date->format('M d, Y') }}</td>
                                            <td>{{ $rx->doctor->name }}</td>
                                            <td><span class="badge bg-info bg-opacity-10 text-info border">{{ $rx->items->count() }} Medicines</span></td>
                                            <td class="small text-muted">{{ Str::limit($rx->general_instructions ?? '—', 40) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('prescriptions.print', $rx) }}" target="_blank" class="btn btn-sm btn-light border">
                                                    <i class="bi bi-printer me-1"></i> Print RX
                                                </a>
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

        <!-- 3. Billing & Payments Tab -->
        <div class="tab-pane fade" id="bills-tab-pane" role="tabpanel">
            <div class="card card-custom">
                <div class="card-body p-0">
                    @if($patient->invoices->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-receipt fs-1 opacity-50 d-block mb-2"></i>
                            <p class="mb-0">No billing invoices found for this patient.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-muted small text-uppercase">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Date</th>
                                        <th>Total Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Status</th>
                                        <th class="text-end">View / Collect</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patient->invoices->sortByDesc('invoice_date') as $inv)
                                        <tr>
                                            <td class="fw-bold font-monospace">{{ $inv->invoice_number }}</td>
                                            <td>{{ $inv->invoice_date->format('M d, Y') }}</td>
                                            <td class="fw-bold">${{ number_format($inv->total_amount, 2) }}</td>
                                            <td class="text-success">${{ number_format($inv->paid_amount, 2) }}</td>
                                            <td>
                                                <span class="badge {{ $inv->payment_status === 'paid' ? 'bg-success' : ($inv->payment_status === 'partially_paid' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                    {{ ucfirst($inv->payment_status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('invoices.show', $inv) }}" class="btn btn-sm btn-light border">
                                                    Manage Bill
                                                </a>
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

        <!-- 4. Appointments Tab -->
        <div class="tab-pane fade" id="apts-tab-pane" role="tabpanel">
            <div class="card card-custom">
                <div class="card-body p-0">
                    @if($patient->appointments->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 opacity-50 d-block mb-2"></i>
                            <p class="mb-0">No appointment history found.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-muted small text-uppercase">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Doctor</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patient->appointments->sortByDesc('appointment_date') as $apt)
                                        <tr>
                                            <td class="fw-semibold">
                                                {{ $apt->appointment_date->format('M d, Y') }} at {{ date('h:i A', strtotime($apt->appointment_time)) }}
                                            </td>
                                            <td>{{ $apt->doctor->name }}</td>
                                            <td class="small text-muted">{{ $apt->reason ?? '—' }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ ucfirst(str_replace('_', ' ', $apt->status)) }}
                                                </span>
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

    </div>
</div>
@endsection
