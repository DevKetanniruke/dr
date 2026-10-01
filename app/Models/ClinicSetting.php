<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    protected $fillable = [
        'clinic_name',
        'tagline',
        'phone',
        'email',
        'address',
        'logo_path',
        'currency',
        'invoice_prefix',
        'patient_prefix',
        'slot_duration_minutes',
    ];

    public static function getSettings()
    {
        return static::first() ?? static::create([
            'clinic_name' => 'CarePlus Medical Clinic',
            'tagline' => 'Quality Healthcare with Compassion',
            'phone' => '+1 (555) 234-5678',
            'email' => 'contact@careplusclinic.com',
            'address' => '123 Healthcare Ave, Suite 100, Medical City',
            'currency' => '$',
            'invoice_prefix' => 'INV-',
            'patient_prefix' => 'PAT-',
            'slot_duration_minutes' => 15,
        ]);
    }
}
