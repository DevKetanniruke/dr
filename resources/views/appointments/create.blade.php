@extends('layouts.app')

@section('title', 'Book Appointment')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-calendar-plus text-teal me-2"></i> Book New Appointment</h4>
            <p class="text-muted mb-0">Schedule a patient consultation slot with a clinic physician.</p>
        </div>
        <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Schedule
        </a>
    </div>

    <div class="card card-custom">
        <div class="card-body p-4">
            <form action="{{ route('appointments.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-4">
                    <!-- Select Patient -->
                    <div class="col-12 col-md-6">
                        <label for="patient_id" class="form-label fw-medium">Select Patient <span class="text-danger">*</span></label>
                        <select name="patient_id" id="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                            <option value="">-- Choose Patient --</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ (old('patient_id', $selectedPatientId) == $patient->id) ? 'selected' : '' }}>
                                    {{ $patient->full_name }} ({{ $patient->patient_code }}) - {{ $patient->phone }}
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text small">Patient not listed? <a href="{{ route('patients.create') }}" target="_blank">Register new patient here</a>.</div>
                    </div>

                    <!-- Select Doctor -->
                    <div class="col-12 col-md-6">
                        <label for="doctor_id" class="form-label fw-medium">Select Consulting Doctor <span class="text-danger">*</span></label>
                        <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                            <option value="">-- Choose Doctor --</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ (old('doctor_id') == $doctor->id || (count($doctors) === 1)) ? 'selected' : '' }}>
                                    {{ $doctor->name }} ({{ $doctor->specialization ?? 'General Physician' }})
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Date & Time -->
                    <div class="col-12 col-md-4">
                        <label for="appointment_date" class="form-label fw-medium">Appointment Date <span class="text-danger">*</span></label>
                        <input type="date" name="appointment_date" id="appointment_date" class="form-control @error('appointment_date') is-invalid @enderror" value="{{ old('appointment_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                        @error('appointment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="appointment_time" class="form-label fw-medium">Time Slot <span class="text-danger">*</span></label>
                        <input type="time" name="appointment_time" id="appointment_time" class="form-control @error('appointment_time') is-invalid @enderror" value="{{ old('appointment_time', '09:00') }}" required>
                        @error('appointment_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="duration_minutes" class="form-label fw-medium">Slot Duration (Minutes)</label>
                        <input type="number" name="duration_minutes" id="duration_minutes" class="form-control" value="{{ old('duration_minutes', $setting->slot_duration_minutes ?? 15) }}" min="5" max="120">
                    </div>

                    <!-- Reason & Notes -->
                    <div class="col-12">
                        <label for="reason" class="form-label fw-medium">Reason for Visit / Chief Symptoms</label>
                        <input type="text" name="reason" id="reason" class="form-control" value="{{ old('reason') }}" placeholder="e.g. Annual Checkup, High Fever, Follow-up consultation">
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label fw-medium">Internal Notes for Doctor / Staff</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="Optional receptionist notes or patient requests">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('appointments.index') }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-brand px-4">
                        <i class="bi bi-calendar-check me-1"></i> Confirm & Book Slot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
