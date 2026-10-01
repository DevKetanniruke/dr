<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_number',
        'invoice_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference_number',
        'notes',
        'received_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public static function generatePaymentNumber(): string
    {
        $year = date('Y');
        $lastPayment = static::withTrashed()
            ->where('payment_number', 'LIKE', "PAY-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastPayment) {
            $number = 1;
        } else {
            $parts = explode('-', $lastPayment->payment_number);
            $number = (int) end($parts) + 1;
        }

        return 'PAY-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
