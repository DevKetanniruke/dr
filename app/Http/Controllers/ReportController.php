<?php

namespace App\Http\Controllers;

use App\Models\ClinicSetting;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Visit;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function revenue(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));

        $payments = Payment::with(['invoice.patient', 'receiver'])
            ->whereDate('payment_date', '>=', $startDate)
            ->whereDate('payment_date', '<=', $endDate)
            ->orderBy('payment_date', 'desc')
            ->get();

        $totalCollected = $payments->sum('amount');
        $byMethod = $payments->groupBy('payment_method')->map(function ($group) {
            return $group->sum('amount');
        });

        $setting = ClinicSetting::getSettings();

        return view('reports.revenue', compact('payments', 'startDate', 'endDate', 'totalCollected', 'byMethod', 'setting'));
    }

    public function visits(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));

        $visits = Visit::with(['patient', 'doctor'])
            ->whereDate('visit_date', '>=', $startDate)
            ->whereDate('visit_date', '<=', $endDate)
            ->latest('visit_date')
            ->get();

        return view('reports.visits', compact('visits', 'startDate', 'endDate'));
    }
}
