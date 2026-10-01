@extends('layouts.app')

@section('title', 'Billing & Invoices')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-receipt text-teal me-2"></i> Billing & Payments</h4>
            <p class="text-muted mb-0">Manage clinic financial invoices, receipts, and payment records.</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="btn btn-brand shadow-sm d-inline-flex align-items-center gap-2">
            <i class="bi bi-journal-plus fs-5"></i> Create New Invoice
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="card card-custom mb-4">
        <div class="card-body py-3">
            <form action="{{ route('invoices.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Search by Invoice # or Patient Name/MRN..." value="{{ request('search') }}">
                </div>

                <div class="col-12 col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Payment Statuses</option>
                        <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Fully Paid</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>

                @if(request('search') || request('status'))
                    <div class="col-12 col-md-2">
                        <a href="{{ route('invoices.index') }}" class="btn btn-light border w-100">Reset</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card card-custom">
        <div class="card-body p-0">
            @if($invoices->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-receipt-cutoff fs-1 opacity-50 d-block mb-2"></i>
                    <p class="mb-0">No invoices found matching criteria.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Patient Name</th>
                                <th>Total Bill</th>
                                <th>Amount Paid</th>
                                <th>Balance Due</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $inv)
                                <tr>
                                    <td>
                                        <a href="{{ route('invoices.show', $inv) }}" class="fw-bold font-monospace text-dark text-decoration-none">
                                            {{ $inv->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="small">{{ $inv->invoice_date->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('patients.show', $inv->patient) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $inv->patient->full_name }}
                                        </a>
                                        <div class="text-muted small">MRN: {{ $inv->patient->patient_code }}</div>
                                    </td>
                                    <td class="fw-bold">{{ $setting->currency }}{{ number_format($inv->total_amount, 2) }}</td>
                                    <td class="text-success">{{ $setting->currency }}{{ number_format($inv->paid_amount, 2) }}</td>
                                    <td class="fw-semibold {{ $inv->due_balance > 0 ? 'text-danger' : 'text-muted' }}">
                                        {{ $setting->currency }}{{ number_format($inv->due_balance, 2) }}
                                    </td>
                                    <td>
                                        @php
                                            $stClass = [
                                                'paid' => 'bg-success text-white',
                                                'partially_paid' => 'bg-warning text-dark',
                                                'unpaid' => 'bg-danger text-white',
                                            ];
                                        @endphp
                                        <span class="badge {{ $stClass[$inv->payment_status] ?? 'bg-secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $inv->payment_status)) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('invoices.show', $inv) }}" class="btn btn-sm btn-outline-primary me-1">
                                            Manage / Pay
                                        </a>
                                        <a href="{{ route('invoices.print', $inv) }}" target="_blank" class="btn btn-sm btn-light border">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
