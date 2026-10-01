@extends('layouts.app')

@section('title', 'Write Prescription')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-capsule text-teal me-2"></i> Write Digital Prescription</h4>
            <p class="text-muted mb-0">Construct multi-item medicine prescription with dosage, frequency, and duration.</p>
        </div>
        <a href="{{ route('prescriptions.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Prescriptions
        </a>
    </div>

    <div class="card card-custom">
        <div class="card-body p-4">
            <form action="{{ route('prescriptions.store') }}" method="POST">
                @csrf
                @if($visitId)
                    <input type="hidden" name="visit_id" value="{{ $visitId }}">
                @endif

                <!-- Doctor & Patient Selection -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-5">
                        <label for="patient_id" class="form-label fw-medium">Patient <span class="text-danger">*</span></label>
                        <select name="patient_id" id="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                            <option value="">-- Choose Patient --</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ (old('patient_id', $patientId) == $patient->id) ? 'selected' : '' }}>
                                    {{ $patient->full_name }} ({{ $patient->patient_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="doctor_id" class="form-label fw-medium">Prescribing Doctor <span class="text-danger">*</span></label>
                        <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ (old('doctor_id', Auth::id()) == $doctor->id) ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="prescription_date" class="form-label fw-medium">Prescription Date <span class="text-danger">*</span></label>
                        <input type="date" name="prescription_date" id="prescription_date" class="form-control" value="{{ old('prescription_date', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <!-- Dynamic Medicine Items Table -->
                <div class="card border mb-4">
                    <div class="card-header bg-light d-flex align-items-center justify-content-between py-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-prescription text-teal me-1 fs-5"></i> Prescribed Medicines
                        </h6>
                        <button type="button" class="btn btn-sm btn-teal" onclick="addMedicineRow()">
                            <i class="bi bi-plus-lg me-1"></i> Add Medicine Row
                        </button>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="medicineTable">
                                <thead class="table-light text-muted small text-uppercase">
                                    <tr>
                                        <th style="width: 30%;">Medicine Name & Strength <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Dosage <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Frequency <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Duration <span class="text-danger">*</span></th>
                                        <th style="width: 20%;">Instructions</th>
                                        <th style="width: 5%;" class="text-center">Remove</th>
                                    </tr>
                                </thead>
                                <tbody id="medicineTableBody">
                                    <!-- Row 0 -->
                                    <tr>
                                        <td>
                                            <input type="text" name="items[0][medicine_name]" class="form-control form-control-sm" placeholder="e.g. Amoxicillin 500mg" required>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][dosage]" class="form-control form-control-sm" placeholder="e.g. 1 Tablet" required>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][frequency]" class="form-control form-control-sm" placeholder="e.g. 1-0-1 or Twice daily" required>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][duration]" class="form-control form-control-sm" placeholder="e.g. 5 days" required>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][instructions]" class="form-control form-control-sm" placeholder="e.g. After meals">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMedicineRow(this)" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- General Instructions -->
                <div class="mb-4">
                    <label for="general_instructions" class="form-label fw-medium">General Doctor Advice & Dietary Precautions</label>
                    <textarea name="general_instructions" id="general_instructions" rows="3" class="form-control" placeholder="e.g. Drink plenty of fluids, complete antibiotic course, avoid cold beverages.">{{ old('general_instructions') }}</textarea>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('prescriptions.index') }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-brand px-4">
                        <i class="bi bi-printer me-1"></i> Save & Generate Printable RX
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let rowIndex = 1;

    function addMedicineRow() {
        const tbody = document.getElementById('medicineTableBody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="text" name="items[${rowIndex}][medicine_name]" class="form-control form-control-sm" placeholder="e.g. Paracetamol 650mg" required>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][dosage]" class="form-control form-control-sm" placeholder="e.g. 1 Tablet" required>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][frequency]" class="form-control form-control-sm" placeholder="e.g. 1-1-1 or As needed" required>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][duration]" class="form-control form-control-sm" placeholder="e.g. 3 days" required>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][instructions]" class="form-control form-control-sm" placeholder="e.g. Before food">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMedicineRow(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
        updateTrashButtons();
    }

    function removeMedicineRow(btn) {
        const tbody = document.getElementById('medicineTableBody');
        if (tbody.children.length > 1) {
            btn.closest('tr').remove();
            updateTrashButtons();
        }
    }

    function updateTrashButtons() {
        const tbody = document.getElementById('medicineTableBody');
        const buttons = tbody.querySelectorAll('button');
        if (tbody.children.length === 1) {
            buttons[0].disabled = true;
        } else {
            buttons.forEach(btn => btn.disabled = false);
        }
    }
</script>
@endpush
@endsection
