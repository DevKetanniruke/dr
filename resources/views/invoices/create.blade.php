@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-receipt text-teal me-2"></i> Create Billing Invoice</h4>
            <p class="text-muted mb-0">Generate itemized bill for consultation, procedures, or medicines.</p>
        </div>
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Invoices
        </a>
    </div>

    <div class="card card-custom">
        <div class="card-body p-4">
            <form action="{{ route('invoices.store') }}" method="POST">
                @csrf
                @if($visitId)
                    <input type="hidden" name="visit_id" value="{{ $visitId }}">
                @endif

                <!-- Patient & Date Selection -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label for="patient_id" class="form-label fw-medium">Patient <span class="text-danger">*</span></label>
                        <select name="patient_id" id="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                            <option value="">-- Select Patient --</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ (old('patient_id', $patientId) == $patient->id) ? 'selected' : '' }}>
                                    {{ $patient->full_name }} ({{ $patient->patient_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="invoice_date" class="form-label fw-medium">Invoice Date <span class="text-danger">*</span></label>
                        <input type="date" name="invoice_date" id="invoice_date" class="form-control" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="due_date" class="form-label fw-medium">Payment Due Date</label>
                        <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date', date('Y-m-d')) }}">
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="card border mb-4">
                    <div class="card-header bg-light d-flex align-items-center justify-content-between py-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-list-check text-teal me-1 fs-5"></i> Itemized Charges & Services
                        </h6>
                        <button type="button" class="btn btn-sm btn-teal" onclick="addInvoiceRow()">
                            <i class="bi bi-plus-lg me-1"></i> Add Service / Item
                        </button>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="invoiceTable">
                                <thead class="table-light text-muted small text-uppercase">
                                    <tr>
                                        <th style="width: 50%;">Description / Service <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Qty <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Unit Price ({{ $setting->currency }}) <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Total ({{ $setting->currency }})</th>
                                        <th style="width: 5%;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="invoiceTableBody">
                                    <!-- Default Consultation Row -->
                                    <tr>
                                        <td>
                                            <input type="text" name="items[0][description]" class="form-control form-control-sm" value="Doctor Consultation Fee" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][quantity]" class="form-control form-control-sm qty-input" value="1" min="1" oninput="calculateInvoiceTotals()" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="items[0][unit_price]" class="form-control form-control-sm price-input" value="50.00" min="0" oninput="calculateInvoiceTotals()" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm bg-light line-total" value="50.00" readonly>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeInvoiceRow(this)" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Totals Calculation Panel -->
                <div class="row justify-content-end mb-4">
                    <div class="col-12 col-md-5">
                        <div class="bg-light p-3 rounded border">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <strong id="subtotalDisplay">{{ $setting->currency }}50.00</strong>
                            </div>

                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-6">
                                    <label for="discount_amount" class="form-label small text-muted mb-0">Discount Amount</label>
                                </div>
                                <div class="col-6">
                                    <input type="number" step="0.01" name="discount_amount" id="discount_amount" class="form-control form-control-sm text-end" value="0.00" min="0" oninput="calculateInvoiceTotals()">
                                </div>
                            </div>

                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-6">
                                    <label for="tax_amount" class="form-label small text-muted mb-0">Tax Amount</label>
                                </div>
                                <div class="col-6">
                                    <input type="number" step="0.01" name="tax_amount" id="tax_amount" class="form-control form-control-sm text-end" value="0.00" min="0" oninput="calculateInvoiceTotals()">
                                </div>
                            </div>

                            <hr class="my-2">

                            <div class="d-flex justify-content-between fs-5 fw-bold text-dark">
                                <span>Grand Total:</span>
                                <span id="grandTotalDisplay">{{ $setting->currency }}50.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label fw-medium">Billing Notes / Terms</label>
                    <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="Optional notes for the invoice receipt">{{ old('notes') }}</textarea>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('invoices.index') }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-brand px-4">
                        <i class="bi bi-check-lg me-1"></i> Generate Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let itemIndex = 1;
    const currency = "{{ $setting->currency }}";

    function addInvoiceRow() {
        const tbody = document.getElementById('invoiceTableBody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="text" name="items[${itemIndex}][description]" class="form-control form-control-sm" placeholder="e.g. Lab Procedure / Medicines" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm qty-input" value="1" min="1" oninput="calculateInvoiceTotals()" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemIndex}][unit_price]" class="form-control form-control-sm price-input" value="0.00" min="0" oninput="calculateInvoiceTotals()" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm bg-light line-total" value="0.00" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeInvoiceRow(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        itemIndex++;
        updateInvoiceTrashButtons();
        calculateInvoiceTotals();
    }

    function removeInvoiceRow(btn) {
        const tbody = document.getElementById('invoiceTableBody');
        if (tbody.children.length > 1) {
            btn.closest('tr').remove();
            updateInvoiceTrashButtons();
            calculateInvoiceTotals();
        }
    }

    function updateInvoiceTrashButtons() {
        const tbody = document.getElementById('invoiceTableBody');
        const buttons = tbody.querySelectorAll('button');
        if (tbody.children.length === 1) {
            buttons[0].disabled = true;
        } else {
            buttons.forEach(btn => btn.disabled = false);
        }
    }

    function calculateInvoiceTotals() {
        const rows = document.querySelectorAll('#invoiceTableBody tr');
        let subtotal = 0;

        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = qty * price;
            row.querySelector('.line-total').value = total.toFixed(2);
            subtotal += total;
        });

        const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
        const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
        const grandTotal = Math.max(0, subtotal - discount + tax);

        document.getElementById('subtotalDisplay').innerText = currency + subtotal.toFixed(2);
        document.getElementById('grandTotalDisplay').innerText = currency + grandTotal.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', calculateInvoiceTotals);
</script>
@endpush
@endsection
