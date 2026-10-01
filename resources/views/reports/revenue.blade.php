@extends('layouts.app')

@section('title', 'Revenue & Collections Report')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-graph-up-arrow text-teal me-2"></i> Revenue & Collections Report</h4>
            <p class="text-muted mb-0">Financial audit of clinic payments collected across payment modes.</p>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card card-custom mb-4">
        <div class="card-body py-3">
            <form action="{{ route('reports.revenue') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small text-muted">From Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label small text-muted">To Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-12 col-md-4">
                    <button type="submit" class="btn btn-brand w-100">
                        <i class="bi bi-funnel me-1"></i> Generate Revenue Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <span class="text-uppercase text-muted fw-semibold small">Total Revenue Collected</span>
                <h2 class="fw-bold text-success mt-2 mb-0">{{ $setting->currency }}{{ number_format($totalCollected, 2) }}</h2>
                <small class="text-muted">Between {{ date('M d, Y', strtotime($startDate)) }} - {{ date('M d, Y', strtotime($endDate)) }}</small>
            </div>
        </div>

        <div class="col-12 col-md-8">
            <div class="stat-card h-100">
                <span class="text-uppercase text-muted fw-semibold small d-block mb-2">Breakdown by Payment Method</span>
                <div class="d-flex flex-wrap gap-3">
                    @foreach(['cash', 'card', 'upi', 'bank_transfer', 'other'] as $method)
                        <div class="border rounded p-2 px-3 bg-light">
                            <span class="text-uppercase text-muted small d-block" style="font-size: 0.7rem;">{{ $method }}</span>
                            <strong class="text-dark">{{ $setting->currency }}{{ number_format($byMethod[$method] ?? 0, 2) }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Receipts Audit Table -->
    <div class="card card-custom">
        <div class="card-header bg-white py-3 border-bottom fw-bold">
            <i class="bi bi-list-check me-2 text-teal"></i> Payment Transaction Ledger
        </div>
        <div class="card-body p-0">
            @if($payments->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-file-earmark-x fs-1 opacity-50 d-block mb-2"></i>
                    <p class="mb-0">No payment transactions recorded for the selected date range.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th>Receipt #</th>
                                <th>Date & Time</th>
                                <th>Patient Name</th>
                                <th>Invoice #</th>
                                <th>Method</th>
                                <th>Ref #</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $pmt)
                                <tr>
                                    <td class="font-monospace fw-bold">{{ $pmt->payment_number }}</td>
                                    <td class="small">{{ $pmt->payment_date->format('M d, Y - h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('patients.show', $pmt->invoice->patient) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $pmt->invoice->patient->full_name }}
                                        </a>
                                    </td>
                                    <td class="font-monospace small">
                                        <a href="{{ route('invoices.show', $pmt->invoice) }}" class="text-decoration-none">
                                            {{ $pmt->invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td><span class="badge bg-light text-dark border text-uppercase">{{ $pmt->payment_method }}</span></td>
                                    <td class="small text-muted">{{ $pmt->reference_number ?? '—' }}</td>
                                    <td class="text-end fw-bold text-success">{{ $setting->currency }}{{ number_format($pmt->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
