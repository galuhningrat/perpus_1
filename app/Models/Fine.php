<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    protected $fillable = [
        'borrowing_id',
        'days_overdue',
        'fine_per_day',
        'total_fine',
        'paid_amount',
        'payment_date',
        'is_paid',
    ];

    protected $casts = [
        'days_overdue' => 'integer',
        'fine_per_day' => 'decimal:2',
        'total_fine' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'is_paid' => 'boolean',
    ];

    /**
     * Relasi: Fine belongs to Borrowing
     */
    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    /**
     * Get remaining fine amount
     */
    public function getRemainingAmount(): float
    {
        return max(0, $this->total_fine - $this->paid_amount);
    }

    /**
     * Check if fine is fully paid
     */
    public function isFullyPaid(): bool
    {
        return $this->is_paid && $this->paid_amount >= $this->total_fine;
    }

    /**
     * Record payment
     */
    public function recordPayment(float $amount): bool
    {
        $this->paid_amount += $amount;

        if ($this->paid_amount >= $this->total_fine) {
            $this->is_paid = true;
            $this->payment_date = now();
        }

        return $this->save();
    }

    /**
     * Scope: Unpaid fines
     */
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    /**
     * Scope: Paid fines
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }
}
