<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'visit_code',
        'patient_id',
        'doctor_id',
        'appointment_id',
        'visit_date',
        'systolic_bp',
        'diastolic_bp',
        'pulse_rate',
        'temperature',
        'weight_kg',
        'height_cm',
        'chief_complaint',
        'diagnosis',
        'clinical_notes',
        'advice',
        'follow_up_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'datetime',
            'follow_up_date' => 'date',
            'temperature' => 'decimal:1',
            'weight_kg' => 'decimal:2',
            'height_cm' => 'decimal:2',
        ];
    }

    public function getBmiAttribute(): ?float
    {
        if ($this->weight_kg > 0 && $this->height_cm > 0) {
            $heightInMeters = $this->height_cm / 100;
            return round($this->weight_kg / ($heightInMeters * $heightInMeters), 1);
        }
        return null;
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public static function generateVisitCode(): string
    {
        $year = date('Y');
        $lastVisit = static::withTrashed()
            ->where('visit_code', 'LIKE', "VST-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastVisit) {
            $number = 1;
        } else {
            $parts = explode('-', $lastVisit->visit_code);
            $number = (int) end($parts) + 1;
        }

        return 'VST-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
