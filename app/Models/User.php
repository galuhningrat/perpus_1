<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nim_nidn',
        'name',
        'email',
        'password',
        'role',
        'prodi',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi: User has many Borrowings
     */
    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * Relasi: User has many Bookings
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Relasi: User has many Activity Logs
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Check if user is Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is Pustakawan
     */
    public function isPustakawan(): bool
    {
        return $this->role === 'pustakawan';
    }

    /**
     * Check if user is Member
     */
    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    /**
     * Check if user has admin access (Super Admin or Pustakawan)
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['super_admin', 'pustakawan']);
    }

    /**
     * Get active borrowings (belum dikembalikan)
     */
    public function activeBorrowings()
    {
        return $this->borrowings()
            ->whereIn('status', ['borrowed', 'overdue'])
            ->with('book');
    }

    /**
     * Get active bookings (pending)
     */
    public function activeBookings()
    {
        return $this->bookings()
            ->where('status', 'pending')
            ->where('expiry_date', '>', now())
            ->with('book');
    }

    /**
     * Check if user has overdue books
     */
    public function hasOverdueBooks(): bool
    {
        return $this->borrowings()
            ->where('status', 'overdue')
            ->exists();
    }

    /**
     * Check if user has unpaid fines
     */
    public function hasUnpaidFines(): bool
    {
        return $this->borrowings()
            ->whereHas('fine', function ($query) {
                $query->where('is_paid', false);
            })
            ->exists();
    }

    /**
     * Get total unpaid fines
     */
    public function getTotalUnpaidFines(): float
    {
        return $this->borrowings()
            ->whereHas('fine', function ($query) {
                $query->where('is_paid', false);
            })
            ->with('fine')
            ->get()
            ->sum(function ($borrowing) {
                return $borrowing->fine?->total_fine ?? 0;
            });
    }
}
