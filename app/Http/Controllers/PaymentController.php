<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->due_balance,
            'payment_method' => 'required|in:cash,card,upi,bank_transfer,other',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($invoice, $validated) {
            Payment::create([
                'payment_number' => Payment::generatePaymentNumber(),
                'invoice_id' => $invoice->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'received_by' => Auth::id(),
            ]);

            $totalPaid = $invoice->payments()->sum('amount');
            $status = 'unpaid';

            if ($totalPaid >= $invoice->total_amount) {
                $status = 'paid';
            } elseif ($totalPaid > 0) {
                $status = 'partially_paid';
            }

            $invoice->update([
                'paid_amount' => $totalPaid,
                'payment_status' => $status,
            ]);
        });

        return back()->with('success', 'Payment recorded successfully.');
    }
}
