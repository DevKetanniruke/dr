<?php

namespace App\Http\Controllers;

use App\Models\ClinicSetting;
use Illuminate\Http\Request;

class ClinicSettingController extends Controller
{
    public function edit()
    {
        $setting = ClinicSetting::getSettings();
        return view('settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = ClinicSetting::getSettings();

        $validated = $request->validate([
            'clinic_name' => 'required|string|max:200',
            'tagline' => 'nullable|string|max:200',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'required|string',
            'currency' => 'required|string|max:10',
            'invoice_prefix' => 'required|string|max:10',
            'patient_prefix' => 'required|string|max:10',
            'slot_duration_minutes' => 'required|integer|min:5|max:120',
        ]);

        $setting->update($validated);

        return back()->with('success', 'Clinic settings updated successfully.');
    }
}
