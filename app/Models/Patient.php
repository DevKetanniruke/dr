<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_code',
        'first_name',
        'last_name',
        'dob',
        'gender',
        'phone',
        'email',
        'address',
        'blood_group',
        'allergies',
        'medical_history',
        'emergency_contact_name',
        'emergency_contact_phone',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): ?int
    {
        return $this->dob ? $this->dob->age : null;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public static function generatePatientCode(): string
    {
        $setting = ClinicSetting::getSettings();
        $prefix = $setting->patient_prefix ?? 'PAT-';
        $year = date('Y');
        $lastPatient = static::withTrashed()
            ->where('patient_code', 'LIKE', "{$prefix}{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastPatient) {
            $number = 1;
        } else {
            $parts = explode('-', $lastPatient->patient_code);
            $number = (int) end($parts) + 1;
        }

        return $prefix . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
