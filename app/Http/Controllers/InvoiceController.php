<?php

namespace App\Http\Controllers;

use App\Models\ClinicSetting;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['patient', 'creator', 'payments']);

        if ($request->filled('status')) {
            $query->where('payment_status', $request->input('status'));
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->input('patient_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('patient', function ($pq) use ($search) {
                      $pq->where('first_name', 'LIKE', "%{$search}%")
                         ->orWhere('last_name', 'LIKE', "%{$search}%")
                         ->orWhere('patient_code', 'LIKE', "%{$search}%");
                  });
            });
        }

        $invoices = $query->latest('invoice_date')->paginate(15)->withQueryString();
        $setting = ClinicSetting::getSettings();

        return view('invoices.index', compact('invoices', 'setting'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('first_name')->get();
        $patientId = $request->input('patient_id');
        $visitId = $request->input('visit_id');
        $visit = null;

        if ($visitId) {
            $visit = Visit::find($visitId);
            if ($visit) {
                $patientId = $visit->patient_id;
            }
        }

        $setting = ClinicSetting::getSettings();

        return view('invoices.create', compact('patients', 'patientId', 'visitId', 'visit', 'setting'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'discount_amount' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $invoice = DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $lineTotal = round($item['quantity'] * $item['unit_price'], 2);
                $subtotal += $lineTotal;
                $itemsData[] = [
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $lineTotal,
                ];
            }

            $discount = (float) $validated['discount_amount'];
            $tax = (float) $validated['tax_amount'];
            $totalAmount = max(0, round($subtotal - $discount + $tax, 2));

            $inv = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'patient_id' => $validated['patient_id'],
                'visit_id' => $validated['visit_id'] ?? null,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'payment_status' => 'unpaid',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($itemsData as $item) {
                $inv->items()->create($item);
            }

            return $inv;
        });

        return redirect()->route('invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_number} created successfully.");
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['patient', 'visit', 'items', 'payments.receiver', 'creator']);
        $setting = ClinicSetting::getSettings();
        return view('invoices.show', compact('invoice', 'setting'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['patient', 'visit', 'items', 'payments.receiver']);
        $setting = ClinicSetting::getSettings();
        return view('invoices.print', compact('invoice', 'setting'));
    }

    public function destroy(Invoice $invoice)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only clinic administrators can soft-delete billing invoices.');
        }

        if ($invoice->paid_amount > 0) {
            return back()->withErrors(['error' => 'Cannot delete an invoice that has payments recorded against it. Please reverse payments first.']);
        }

        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', "Invoice {$invoice->invoice_number} archived successfully.");
    }
}
