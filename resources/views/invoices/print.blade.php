<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->invoice_number }} - {{ $setting->clinic_name }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 2rem 0;
        }

        .invoice-paper {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 2.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .clinic-header {
            border-bottom: 2px solid #0d9488;
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .invoice-paper {
                box-shadow: none;
                width: 100%;
                min-height: 100vh;
                padding: 1.5rem;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="text-center mb-3 no-print">
    <button onclick="window.print()" class="btn btn-primary btn-lg shadow-sm">
        <i class="bi bi-printer-fill me-2"></i> Print Bill Receipt (A4 Format)
    </button>
    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary btn-lg ms-2">
        <i class="bi bi-arrow-left me-1"></i> Return to Invoice
    </a>
</div>

<div class="invoice-paper">
    <div>
        <!-- Clinic Branding Header -->
        <div class="clinic-header d-flex align-items-center justify-content-between">
            <div>
                <h3 class="fw-bold text-dark mb-1">{{ $setting->clinic_name }}</h3>
                <p class="text-muted small mb-1">{{ $setting->tagline }}</p>
                <div class="small text-secondary">{{ $setting->address }} | Ph: {{ $setting->phone }}</div>
            </div>
            <div class="text-end">
                <h4 class="fw-bold text-teal mb-0">BILL RECEIPT</h4>
                <div class="fw-bold font-monospace text-dark">{{ $invoice->invoice_number }}</div>
                <div class="text-muted small">Date: <strong>{{ $invoice->invoice_date->format('M d, Y') }}</strong></div>
            </div>
        </div>

        <!-- Patient Info & Invoice Status Strip -->
        <div class="row bg-light p-3 rounded mb-4 border align-items-center">
            <div class="col-6">
                <span class="text-muted small d-block">BILLED TO:</span>
                <strong class="fs-6 text-dark">{{ $invoice->patient->full_name }}</strong>
                <div class="small text-muted">MRN: {{ $invoice->patient->patient_code }} | Ph: {{ $invoice->patient->phone }}</div>
            </div>
            <div class="col-6 text-end">
                <span class="text-muted small d-block">PAYMENT STATUS:</span>
                <span class="badge {{ $invoice->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }} fs-6">
                    {{ ucfirst(str_replace('_', ' ', $invoice->payment_status)) }}
                </span>
            </div>
        </div>

        <!-- Items Table -->
        <table class="table table-bordered align-middle mb-4">
            <thead class="table-light">
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 55%;">Description / Service</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 15%;" class="text-end">Unit Price</th>
                    <th style="width: 15%;" class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $idx => $item)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $item->description }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">{{ $setting->currency }}{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end fw-bold">{{ $setting->currency }}{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Summary -->
        <div class="row justify-content-end mb-4">
            <div class="col-6">
                <div class="p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>{{ $setting->currency }}{{ number_format($invoice->subtotal, 2) }}</strong>
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
                    <div class="d-flex justify-content-between fs-5 fw-bold text-dark mb-1">
                        <span>Grand Total:</span>
                        <span>{{ $setting->currency }}{{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-success fw-medium">
                        <span>Paid Amount:</span>
                        <span>{{ $setting->currency }}{{ number_format($invoice->paid_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold {{ $invoice->due_balance > 0 ? 'text-danger' : 'text-muted' }}">
                        <span>Balance Due:</span>
                        <span>{{ $setting->currency }}{{ number_format($invoice->due_balance, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Seal -->
    <div class="pt-5 d-flex align-items-end justify-content-between">
        <div class="small text-muted">
            Thank you for choosing {{ $setting->clinic_name }}. Get well soon!
        </div>
        <div class="text-center border-top border-secondary pt-2" style="width: 200px;">
            <div class="small text-muted">Authorized Signature</div>
        </div>
    </div>
</div>

</body>
</html>
