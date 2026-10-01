<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - {{ $prescription->patient->full_name }} - {{ $prescription->prescription_date->format('Y-m-d') }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 2rem 0;
        }

        .prescription-paper {
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

        .rx-symbol {
            font-size: 2.5rem;
            font-weight: 700;
            color: #0d9488;
            line-height: 1;
        }

        .table-rx th {
            background-color: #f8fafc;
            border-bottom: 2px solid #cbd5e1;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .signature-box {
            border-top: 1px dashed #94a3b8;
            width: 220px;
            text-align: center;
            padding-top: 0.5rem;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .prescription-paper {
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
        <i class="bi bi-printer-fill me-2"></i> Print Prescription (A4 Format)
    </button>
    <a href="{{ route('patients.show', $prescription->patient) }}" class="btn btn-outline-secondary btn-lg ms-2">
        <i class="bi bi-arrow-left me-1"></i> Return to Patient
    </a>
</div>

<div class="prescription-paper">
    <div>
        <!-- Clinic Branding Header -->
        <div class="clinic-header d-flex align-items-center justify-content-between">
            <div>
                <h3 class="fw-bold text-dark mb-1">{{ $setting->clinic_name }}</h3>
                <p class="text-muted small mb-1">{{ $setting->tagline }}</p>
                <div class="small text-secondary">{{ $setting->address }} | Ph: {{ $setting->phone }}</div>
            </div>
            <div class="text-end">
                <h5 class="fw-bold text-teal mb-0">{{ $prescription->doctor->name }}</h5>
                <div class="text-muted small">{{ $prescription->doctor->specialization ?? 'General Physician' }}</div>
                <div class="text-muted small">Date: <strong>{{ $prescription->prescription_date->format('M d, Y') }}</strong></div>
            </div>
        </div>

        <!-- Patient Demographics Summary Strip -->
        <div class="bg-light p-3 rounded mb-4 border d-flex flex-wrap justify-content-between text-sm">
            <div>
                <span class="text-muted small d-block">PATIENT NAME</span>
                <strong class="fs-6 text-dark">{{ $prescription->patient->full_name }}</strong>
            </div>
            <div>
                <span class="text-muted small d-block">PATIENT ID (MRN)</span>
                <span class="font-monospace text-dark">{{ $prescription->patient->patient_code }}</span>
            </div>
            <div>
                <span class="text-muted small d-block">AGE / GENDER</span>
                <span class="text-capitalize">{{ $prescription->patient->age ? $prescription->patient->age . ' yrs' : 'N/A' }} / {{ $prescription->patient->gender }}</span>
            </div>
            <div>
                <span class="text-muted small d-block">BLOOD GROUP</span>
                <span>{{ $prescription->patient->blood_group ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- RX Section -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="rx-symbol">℞</span>
            <span class="fw-bold text-muted text-uppercase" style="letter-spacing: 0.1em; font-size: 0.85rem;">Medication Schedule</span>
        </div>

        <!-- Prescription Items Table -->
        <table class="table table-bordered table-rx align-middle mb-4">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Medicine & Strength</th>
                    <th style="width: 15%;">Dosage</th>
                    <th style="width: 15%;">Frequency</th>
                    <th style="width: 15%;">Duration</th>
                    <th style="width: 15%;">Instructions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescription->items as $index => $item)
                    <tr>
                        <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $item->medicine_name }}</td>
                        <td>{{ $item->dosage }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $item->frequency }}</span></td>
                        <td>{{ $item->duration }}</td>
                        <td class="small text-muted">{{ $item->instructions ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- General Doctor Advice -->
        @if($prescription->general_instructions)
            <div class="border rounded p-3 mb-4 bg-white">
                <h6 class="fw-bold text-uppercase text-secondary mb-2" style="font-size: 0.75rem;">Doctor Advice & Instructions:</h6>
                <p class="mb-0 text-dark small" style="white-space: pre-line;">{{ $prescription->general_instructions }}</p>
            </div>
        @endif
    </div>

    <!-- Footer Signature Block -->
    <div class="pt-5 d-flex align-items-end justify-content-between">
        <div class="small text-muted">
            <i class="bi bi-info-circle me-1"></i> Generated digitally via {{ $setting->clinic_name }} CRM.
        </div>
        <div class="signature-box">
            <div class="fw-bold text-dark">{{ $prescription->doctor->name }}</div>
            <div class="small text-muted">Authorized Physician Signature</div>
        </div>
    </div>
</div>

</body>
</html>
