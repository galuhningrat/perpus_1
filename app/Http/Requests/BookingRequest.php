<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Members can create bookings
        if ($this->user()->isMember()) {
            return true;
        }

        // Admin can create bookings on behalf of members
        if ($this->user()->hasAdminAccess() && $this->has('user_id')) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'book_id' => [
                'required',
                'exists:books,id',
                function ($attribute, $value, $fail) {
                    $book = \App\Models\Book::find($value);
                    if (!$book || !$book->is_active) {
                        $fail('Buku tidak tersedia.');
                    }
                    if (!$book->isAvailable()) {
                        $fail('Buku sedang tidak tersedia untuk booking.');
                    }
                },
            ],
            'notes' => 'nullable|string|max:500',
        ];

        // If admin is creating booking for a member
        if ($this->user()->hasAdminAccess()) {
            $rules['user_id'] = [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = \App\Models\User::find($value);
                    if (!$user || $user->role !== 'member') {
                        $fail('User harus berupa member.');
                    }
                    if (!$user->is_active) {
                        $fail('User tidak aktif.');
                    }
                },
            ];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'book_id' => 'buku',
            'user_id' => 'anggota',
            'notes' => 'catatan',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'book_id.required' => 'Buku harus dipilih',
            'book_id.exists' => 'Buku tidak ditemukan',
            'user_id.required' => 'Anggota harus dipilih',
            'user_id.exists' => 'Anggota tidak ditemukan',
            'notes.max' => 'Catatan maksimal 500 karakter',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $userId = $this->user()->hasAdminAccess()
                ? $this->user_id
                : $this->user()->id;

            $user = \App\Models\User::find($userId);

            if (!$user) {
                return;
            }

            // Check if user has overdue books
            if ($user->hasOverdueBooks()) {
                $validator->errors()->add(
                    'user',
                    'Tidak dapat booking karena memiliki buku yang terlambat dikembalikan.'
                );
            }

            // Check if user has unpaid fines
            if ($user->hasUnpaidFines()) {
                $validator->errors()->add(
                    'user',
                    'Tidak dapat booking karena memiliki denda yang belum dibayar.'
                );
            }

            // Check booking limit (max 3 active bookings)
            $activeBookingsCount = \App\Models\Booking::where('user_id', $userId)
                ->where('status', 'pending')
                ->count();

            if ($activeBookingsCount >= 3) {
                $validator->errors()->add(
                    'booking_limit',
                    'Sudah mencapai batas maksimal booking (3 buku).'
                );
            }

            // Check if user already has active booking for this book
            if ($this->has('book_id')) {
                $existingBooking = \App\Models\Booking::where('user_id', $userId)
                    ->where('book_id', $this->book_id)
                    ->where('status', 'pending')
                    ->exists();

                if ($existingBooking) {
                    $validator->errors()->add(
                        'book_id',
                        'Anda sudah memiliki booking aktif untuk buku ini.'
                    );
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // If member is creating booking, use their own user_id
        if ($this->user()->isMember() && !$this->has('user_id')) {
            $this->merge([
                'user_id' => $this->user()->id,
            ]);
        }
    }
}
