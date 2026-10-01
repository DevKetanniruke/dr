<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Seeder;

class DemoClinicSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $doctor = User::where('role', 'doctor')->first();

        // 1. Patient 1: Eleanor Vance
        $p1 = Patient::firstOrCreate(
            ['patient_code' => 'PAT-2026-0001'],
            [
                'first_name' => 'Eleanor',
                'last_name' => 'Vance',
                'dob' => '1988-05-14',
                'gender' => 'female',
                'phone' => '+1 (555) 234-8899',
                'email' => 'eleanor.vance@example.com',
                'address' => '742 Evergreen Terrace, Springfield',
                'blood_group' => 'O+',
                'allergies' => 'Penicillin',
                'medical_history' => 'Hypertension, Mild Asthma',
                'emergency_contact_name' => 'Thomas Vance',
                'emergency_contact_phone' => '+1 (555) 234-9900',
                'created_by' => $admin->id,
            ]
        );

        // Patient 2: Marcus Brody
        $p2 = Patient::firstOrCreate(
            ['patient_code' => 'PAT-2026-0002'],
            [
                'first_name' => 'Marcus',
                'last_name' => 'Brody',
                'dob' => '1975-11-20',
                'gender' => 'male',
                'phone' => '+1 (555) 876-1122',
                'email' => 'mbrody@example.com',
                'address' => '1204 Museum Way, Suite 4B',
                'blood_group' => 'A+',
                'allergies' => 'None',
                'medical_history' => 'Type 2 Diabetes',
                'emergency_contact_name' => 'Marion Brody',
                'emergency_contact_phone' => '+1 (555) 876-3344',
                'created_by' => $admin->id,
            ]
        );

        // 2. Appointments for today
        $today = date('Y-m-d');

        $apt1 = Appointment::firstOrCreate(
            ['patient_id' => $p1->id, 'appointment_date' => $today, 'appointment_time' => '09:30:00'],
            [
                'doctor_id' => $doctor->id,
                'duration_minutes' => 15,
                'status' => 'completed',
                'reason' => 'High Fever & Acute Cough',
                'created_by' => $admin->id,
            ]
        );

        $apt2 = Appointment::firstOrCreate(
            ['patient_id' => $p2->id, 'appointment_date' => $today, 'appointment_time' => '10:15:00'],
            [
                'doctor_id' => $doctor->id,
                'duration_minutes' => 15,
                'status' => 'checked_in',
                'reason' => 'Routine Diabetes Follow-up',
                'created_by' => $admin->id,
            ]
        );

        // 3. Clinical Visit for Patient 1
        $visit1 = Visit::firstOrCreate(
            ['visit_code' => 'VST-2026-0001'],
            [
                'patient_id' => $p1->id,
                'doctor_id' => $doctor->id,
                'appointment_id' => $apt1->id,
                'visit_date' => "{$today} 09:35:00",
                'systolic_bp' => 125,
                'diastolic_bp' => 82,
                'pulse_rate' => 76,
                'temperature' => 99.8,
                'weight_kg' => 64.5,
                'height_cm' => 168.0,
                'chief_complaint' => 'Patient complains of dry cough and low-grade fever for 3 days.',
                'diagnosis' => 'Acute Upper Respiratory Tract Infection (J06.9)',
                'clinical_notes' => 'Chest clear on auscultation. Throat mild erythema.',
                'advice' => 'Drink warm fluids, rest for 3 days, avoid chilled items.',
                'follow_up_date' => date('Y-m-d', strtotime('+5 days')),
                'status' => 'completed',
            ]
        );

        // 4. Digital Prescription for Visit 1
        $rx1 = Prescription::firstOrCreate(
            ['visit_id' => $visit1->id],
            [
                'patient_id' => $p1->id,
                'doctor_id' => $doctor->id,
                'prescription_date' => $today,
                'general_instructions' => 'Take medicines after food. Complete full 5-day course.',
            ]
        );

        if ($rx1->items()->count() === 0) {
            PrescriptionItem::create([
                'prescription_id' => $rx1->id,
                'medicine_name' => 'Azithromycin 500mg',
                'dosage' => '1 Tablet',
                'frequency' => '1-0-0 (Once daily)',
                'duration' => '3 Days',
                'instructions' => 'After breakfast',
            ]);

            PrescriptionItem::create([
                'prescription_id' => $rx1->id,
                'medicine_name' => 'Paracetamol 650mg',
                'dosage' => '1 Tablet',
                'frequency' => '1-0-1 (Twice daily)',
                'duration' => '5 Days',
                'instructions' => 'When needed for fever',
            ]);
        }

        // 5. Billing Invoice & Payment for Visit 1
        $inv1 = Invoice::firstOrCreate(
            ['invoice_number' => 'INV-2026-0001'],
            [
                'patient_id' => $p1->id,
                'visit_id' => $visit1->id,
                'invoice_date' => $today,
                'due_date' => $today,
                'subtotal' => 75.00,
                'discount_amount' => 5.00,
                'tax_amount' => 0.00,
                'total_amount' => 70.00,
                'paid_amount' => 70.00,
                'payment_status' => 'paid',
                'notes' => 'Paid in full via Cash at front desk.',
                'created_by' => $admin->id,
            ]
        );

        if ($inv1->items()->count() === 0) {
            InvoiceItem::create([
                'invoice_id' => $inv1->id,
                'description' => 'Physician Consultation Fee',
                'quantity' => 1,
                'unit_price' => 50.00,
                'total_price' => 50.00,
            ]);

            InvoiceItem::create([
                'invoice_id' => $inv1->id,
                'description' => 'Rapid Influenza Diagnostic Test',
                'quantity' => 1,
                'unit_price' => 25.00,
                'total_price' => 25.00,
            ]);
        }

        if ($inv1->payments()->count() === 0) {
            Payment::create([
                'payment_number' => 'PAY-2026-0001',
                'invoice_id' => $inv1->id,
                'payment_date' => "{$today} 09:55:00",
                'amount' => 70.00,
                'payment_method' => 'cash',
                'reference_number' => 'CASH-REC-01',
                'notes' => 'Received by front desk',
                'received_by' => $admin->id,
            ]);
        }
    }
}
