@extends('layouts.app')

@section('title', 'Register New Patient')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-person-plus-fill text-teal me-2"></i> Register New Patient</h4>
            <p class="text-muted mb-0">Fill in demographics and medical history to create a patient profile.</p>
        </div>
        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Patient Directory
        </a>
    </div>

    <div class="card card-custom">
        <div class="card-body p-4">
            <form action="{{ route('patients.store') }}" method="POST">
                @csrf

                <!-- Basic Demographics -->
                <h6 class="fw-bold text-uppercase text-teal mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    <i class="bi bi-person-badge me-1"></i> Demographics & Basic Information
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label for="first_name" class="form-label fw-medium">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required placeholder="e.g. John">
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="last_name" class="form-label fw-medium">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required placeholder="e.g. Doe">
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="dob" class="form-label fw-medium">Date of Birth</label>
                        <input type="date" name="dob" id="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob') }}">
                        @error('dob')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="gender" class="form-label fw-medium">Gender <span class="text-danger">*</span></label>
                        <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="blood_group" class="form-label fw-medium">Blood Group</label>
                        <select name="blood_group" id="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                            <option value="">Unknown / Select</option>
                            <option value="A+" {{ old('blood_group') === 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('blood_group') === 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('blood_group') === 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('blood_group') === 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="O+" {{ old('blood_group') === 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('blood_group') === 'O-' ? 'selected' : '' }}>O-</option>
                            <option value="AB+" {{ old('blood_group') === 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('blood_group') === 'AB-' ? 'selected' : '' }}>AB-</option>
                        </select>
                        @error('blood_group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Contact & Address -->
                <h6 class="fw-bold text-uppercase text-teal mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    <i class="bi bi-telephone me-1"></i> Contact & Address Information
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label for="phone" class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required placeholder="+1 (555) 000-0000">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label fw-medium">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="patient@example.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label fw-medium">Residential Address</label>
                        <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror" placeholder="Street Address, City, State, ZIP">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Medical History & Emergency Contact -->
                <h6 class="fw-bold text-uppercase text-teal mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    <i class="bi bi-file-earmark-medical me-1"></i> Medical History & Emergency Details
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label for="allergies" class="form-label fw-medium">Known Allergies</label>
                        <textarea name="allergies" id="allergies" rows="2" class="form-control" placeholder="e.g. Penicillin, Peanuts, Latex">{{ old('allergies') }}</textarea>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="medical_history" class="form-label fw-medium">Past Medical History / Conditions</label>
                        <textarea name="medical_history" id="medical_history" rows="2" class="form-control" placeholder="e.g. Diabetes Type 2, Hypertension, Asthma">{{ old('medical_history') }}</textarea>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="emergency_contact_name" class="form-label fw-medium">Emergency Contact Person</label>
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}" placeholder="Spouse, Relative, Guardian">
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="emergency_contact_phone" class="form-label fw-medium">Emergency Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone') }}" placeholder="+1 (555) 000-0000">
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('patients.index') }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-brand px-4">
                        <i class="bi bi-check-lg me-1"></i> Save & Create Patient Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
