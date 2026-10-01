@extends('layouts.app')

@section('title', 'Invoice - ' . $invoice->invoice_number)

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0">Invoice {{ $invoice->invoice_number }}</h4>
                @php
                    $stClass = [
                        'paid' => 'bg-success text-white',
                        'partially_paid' => 'bg-warning text-dark',
                        'unpaid' => 'bg-danger text-white',
                    ];
                @endphp
                <span class="badge {{ $stClass[$invoice->payment_status] ?? 'bg-secondary' }} fs-6">
                    {{ ucfirst(str_replace('_', ' ', $invoice->payment_status)) }}
                </span>
            </div>
            <p class="text-muted mb-0">Issued on {{ $invoice->invoice_date->format('F j, Y') }}</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @if($invoice->due_balance > 0)
                <button class="btn btn-brand btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
                    <i class="bi bi-cash-coin me-1"></i> Record Payment
                </button>
            @endif

            <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-printer me-1"></i> Print Bill
            </a>
            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Invoices
            </a>
        </div>
    </div>

    <!-- Invoice Details Card -->
    <div class="card card-custom mb-4">
        <div class="card-body p-4">
            <!-- Patient & Clinic Header -->
            <div class="row border-bottom pb-4 mb-4">
                <div class="col-md-6">
                    <span class="text-uppercase text-muted small fw-semibold d-block mb-1">Billed To Patient:</span>
                    <h5 class="fw-bold mb-1">
                        <a href="{{ route('patients.show', $invoice->patient) }}" class="text-dark text-decoration-none">
                            {{ $invoice->patient->full_name }}
                        </a>
                    </h5>
                    <div class="text-muted small">MRN: {{ $invoice->patient->patient_code }} | Ph: {{ $invoice->patient->phone }}</div>
                    @if($invoice->patient->address)<div class="text-muted small">{{ $invoice->patient->address }}</div>@endif
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-uppercase text-muted small fw-semibold d-block mb-1">Clinic Issuer:</span>
                    <h6 class="fw-bold mb-1">{{ $setting->clinic_name }}</h6>
                    <div class="text-muted small">{{ $setting->phone }} | {{ $setting->address }}</div>
                </div>
            </div>

            <!-- Itemized Table -->
            <h6 class="fw-bold text-uppercase text-teal mb-3" style="font-size: 0.8rem;">Itemized Charges:</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td class="fw-medium text-dark">{{ $item->description }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">{{ $setting->currency }}{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end fw-semibold">{{ $setting->currency }}{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals & Payment Summary -->
            <div class="row justify-content-end mb-4">
                <div class="col-12 col-md-5">
                    <div class="bg-light p-3 rounded border">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span>{{ $setting->currency }}{{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        @if($invoice->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>Discount:</span>
                                <span>- {{ $setting->currency }}{{ number_format($invoice->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        @if($invoice->tax_amount > 0)
                            <div class="d-flex justify-content-between mb-2 text-muted">
                                <span>Tax:</span>
                                <span>+ {{ $setting->currency }}{{ number_format($invoice->tax_amount, 2) }}</span>
                            </div>
                        @endif
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fs-5 fw-bold text-dark mb-2">
                            <span>Grand Total:</span>
                            <span>{{ $setting->currency }}{{ number_format($invoice->total_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-success fw-medium mb-1">
                            <span>Amount Paid:</span>
                            <span>{{ $setting->currency }}{{ number_format($invoice->paid_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-6 {{ $invoice->due_balance > 0 ? 'text-danger' : 'text-muted' }}">
                            <span>Balance Due:</span>
                            <span>{{ $setting->currency }}{{ number_format($invoice->due_balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History List -->
            <h6 class="fw-bold text-uppercase text-teal mb-3" style="font-size: 0.8rem;">
                <i class="bi bi-clock-history me-1"></i> Recorded Payments History
            </h6>
            @if($invoice->payments->isEmpty())
                <p class="text-muted small">No payments recorded against this invoice yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Ref / Transaction #</th>
                                <th>Amount</th>
                                <th>Collected By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->payments as $pmt)
                                <tr>
                                    <td class="font-monospace fw-semibold">{{ $pmt->payment_number }}</td>
                                    <td>{{ $pmt->payment_date->format('M d, Y - h:i A') }}</td>
                                    <td><span class="badge bg-light text-dark border text-uppercase">{{ $pmt->payment_method }}</span></td>
                                    <td class="small text-muted">{{ $pmt->reference_number ?? '—' }}</td>
                                    <td class="fw-bold text-success">{{ $setting->currency }}{{ number_format($pmt->amount, 2) }}</td>
                                    <td class="small">{{ $pmt->receiver->name ?? 'Staff' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
@if($invoice->due_balance > 0)
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('invoices.payments.store', $invoice) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold"><i class="bi bi-cash-coin me-2 text-teal"></i> Record Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-info py-2 small">
                            Remaining Balance Due: <strong>{{ $setting->currency }}{{ number_format($invoice->due_balance, 2) }}</strong>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label fw-medium">Payment Amount ({{ $setting->currency }}) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ $invoice->due_balance }}" max="{{ $invoice->due_balance }}" min="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label fw-medium">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="card">Credit / Debit Card</option>
                                <option value="upi">UPI / Mobile Transfer</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="other">Other / Insurance</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="payment_date" class="form-label fw-medium">Payment Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="payment_date" id="payment_date" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="reference_number" class="form-label fw-medium">Transaction / Reference #</label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control" placeholder="Card Approval Code, UPI Ref, Cheque #">
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label fw-medium">Notes</label>
                            <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="Optional notes"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-brand">Save Payment Receipt</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
