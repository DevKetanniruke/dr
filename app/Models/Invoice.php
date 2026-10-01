<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'patient_id',
        'visit_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'payment_status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    public function getDueBalanceAttribute(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public static function generateInvoiceNumber(): string
    {
        $setting = ClinicSetting::getSettings();
        $prefix = $setting->invoice_prefix ?? 'INV-';
        $year = date('Y');
        $lastInvoice = static::withTrashed()
            ->where('invoice_number', 'LIKE', "{$prefix}{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastInvoice) {
            $number = 1;
        } else {
            $parts = explode('-', $lastInvoice->invoice_number);
            $number = (int) end($parts) + 1;
        }

        return $prefix . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
