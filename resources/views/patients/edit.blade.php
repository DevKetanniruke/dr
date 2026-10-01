@extends('layouts.app')

@section('title', 'Edit Patient - ' . $patient->full_name)

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-pencil-square text-teal me-2"></i> Edit Patient Record</h4>
            <p class="text-muted mb-0">Updating profile for <strong>{{ $patient->full_name }}</strong> ({{ $patient->patient_code }}).</p>
        </div>
        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Cancel & Return to Profile
        </a>
    </div>

    <div class="card card-custom">
        <div class="card-body p-4">
            <form action="{{ route('patients.update', $patient) }}" method="POST">
                @csrf
                @method('PUT')

                <h6 class="fw-bold text-uppercase text-teal mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    <i class="bi bi-person-badge me-1"></i> Demographics
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label for="first_name" class="form-label fw-medium">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $patient->first_name) }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="last_name" class="form-label fw-medium">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $patient->last_name) }}" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="dob" class="form-label fw-medium">Date of Birth</label>
                        <input type="date" name="dob" id="dob" class="form-control" value="{{ old('dob', optional($patient->dob)->format('Y-m-d')) }}">
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="gender" class="form-label fw-medium">Gender <span class="text-danger">*</span></label>
                        <select name="gender" id="gender" class="form-select" required>
                            <option value="male" {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $patient->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="blood_group" class="form-label fw-medium">Blood Group</label>
                        <select name="blood_group" id="blood_group" class="form-select">
                            <option value="">Unknown / Select</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group', $patient->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <h6 class="fw-bold text-uppercase text-teal mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    <i class="bi bi-telephone me-1"></i> Contact & Address
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label for="phone" class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $patient->phone) }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label fw-medium">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $patient->email) }}">
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label fw-medium">Residential Address</label>
                        <textarea name="address" id="address" rows="2" class="form-control">{{ old('address', $patient->address) }}</textarea>
                    </div>
                </div>

                <h6 class="fw-bold text-uppercase text-teal mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    <i class="bi bi-file-earmark-medical me-1"></i> Medical History
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label for="allergies" class="form-label fw-medium">Known Allergies</label>
                        <textarea name="allergies" id="allergies" rows="2" class="form-control">{{ old('allergies', $patient->allergies) }}</textarea>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="medical_history" class="form-label fw-medium">Past Medical History</label>
                        <textarea name="medical_history" id="medical_history" rows="2" class="form-control">{{ old('medical_history', $patient->medical_history) }}</textarea>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="emergency_contact_name" class="form-label fw-medium">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="emergency_contact_phone" class="form-label fw-medium">Emergency Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}">
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('patients.show', $patient) }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-brand px-4">
                        <i class="bi bi-check-lg me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
