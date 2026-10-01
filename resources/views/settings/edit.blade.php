@extends('layouts.app')

@section('title', 'Clinic Settings')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-sliders text-teal me-2"></i> Clinic Branding & Operational Settings</h4>
            <p class="text-muted mb-0">Configure clinic profile, currency, invoice prefixes, and appointment slots.</p>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-body p-4">
            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <h6 class="fw-bold text-uppercase text-teal mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    <i class="bi bi-hospital me-1"></i> Clinic Branding Profile
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label for="clinic_name" class="form-label fw-medium">Clinic Name <span class="text-danger">*</span></label>
                        <input type="text" name="clinic_name" id="clinic_name" class="form-control" value="{{ old('clinic_name', $setting->clinic_name) }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="tagline" class="form-label fw-medium">Tagline / Motto</label>
                        <input type="text" name="tagline" id="tagline" class="form-control" value="{{ old('tagline', $setting->tagline) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="phone" class="form-label fw-medium">Primary Contact Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $setting->phone) }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label fw-medium">Clinic Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $setting->email) }}">
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label fw-medium">Full Physical Address <span class="text-danger">*</span></label>
                        <textarea name="address" id="address" rows="2" class="form-control" required>{{ old('address', $setting->address) }}</textarea>
                    </div>
                </div>

                <h6 class="fw-bold text-uppercase text-teal mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    <i class="bi bi-gear me-1"></i> System Prefixes & Operations
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-3">
                        <label for="currency" class="form-label fw-medium">Currency Symbol <span class="text-danger">*</span></label>
                        <input type="text" name="currency" id="currency" class="form-control font-monospace" value="{{ old('currency', $setting->currency) }}" required placeholder="$ or ₹ or €">
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="patient_prefix" class="form-label fw-medium">Patient Code Prefix <span class="text-danger">*</span></label>
                        <input type="text" name="patient_prefix" id="patient_prefix" class="form-control font-monospace" value="{{ old('patient_prefix', $setting->patient_prefix) }}" required placeholder="PAT-">
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="invoice_prefix" class="form-label fw-medium">Invoice Number Prefix <span class="text-danger">*</span></label>
                        <input type="text" name="invoice_prefix" id="invoice_prefix" class="form-control font-monospace" value="{{ old('invoice_prefix', $setting->invoice_prefix) }}" required placeholder="INV-">
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="slot_duration_minutes" class="form-label fw-medium">Default Slot Duration (Mins)</label>
                        <input type="number" name="slot_duration_minutes" id="slot_duration_minutes" class="form-control" value="{{ old('slot_duration_minutes', $setting->slot_duration_minutes) }}" min="5" max="120" required>
                    </div>
                </div>

                @if(Auth::user()->isAdmin())
                    <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-brand px-4">
                            <i class="bi bi-check-lg me-1"></i> Save Clinic Settings
                        </button>
                    </div>
                @else
                    <div class="alert alert-info small mb-0">
                        <i class="bi bi-info-circle me-1"></i> Only clinic administrators can save settings updates.
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
