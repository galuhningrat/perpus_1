<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class BorrowingRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         */
        public function authorize(): bool
        {
            // Only admin (pustakawan) can create borrowing records
            return $this->user()->hasAdminAccess();
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
         */
        public function rules(): array
        {
            return [
                'user_id' => [
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
                ],
                'book_id' => [
                    'required',
                    'exists:books,id',
                    function ($attribute, $value, $fail) {
                        $book = \App\Models\Book::find($value);
                        if (!$book || !$book->is_active) {
                            $fail('Buku tidak tersedia.');
                        }
                        if (!$book->isAvailable()) {
                            $fail('Buku sedang tidak tersedia untuk dipinjam.');
                        }
                    },
                ],
                'borrow_date' => 'nullable|date',
                'due_date' => 'nullable|date|after:borrow_date',
                'notes' => 'nullable|string|max:500',
            ];
        }

        /**
         * Get custom attributes for validator errors.
         */
        public function attributes(): array
        {
            return [
                'user_id' => 'peminjam',
                'book_id' => 'buku',
                'borrow_date' => 'tanggal pinjam',
                'due_date' => 'tanggal jatuh tempo',
                'notes' => 'catatan',
            ];
        }

        /**
         * Get custom messages for validator errors.
         */
        public function messages(): array
        {
            return [
                'user_id.required' => 'Peminjam harus dipilih',
                'user_id.exists' => 'Peminjam tidak ditemukan',
                'book_id.required' => 'Buku harus dipilih',
                'book_id.exists' => 'Buku tidak ditemukan',
                'borrow_date.date' => 'Format tanggal pinjam tidak valid',
                'due_date.date' => 'Format tanggal jatuh tempo tidak valid',
                'due_date.after' => 'Tanggal jatuh tempo harus setelah tanggal pinjam',
                'notes.max' => 'Catatan maksimal 500 karakter',
            ];
        }

        /**
         * Configure the validator instance.
         */
        public function withValidator($validator): void
        {
            $validator->after(function ($validator) {
                if (!$this->has('user_id')) {
                    return;
                }

                $user = \App\Models\User::find($this->user_id);

                if (!$user) {
                    return;
                }

                // Check if user has overdue books
                if ($user->hasOverdueBooks()) {
                    $validator->errors()->add(
                        'user_id',
                        'Peminjam memiliki buku yang terlambat dikembalikan.'
                    );
                }

                // Check if user has unpaid fines
                if ($user->hasUnpaidFines()) {
                    $validator->errors()->add(
                        'user_id',
                        'Peminjam memiliki denda yang belum dibayar sebesar Rp ' .
                        number_format($user->getTotalUnpaidFines(), 0, ',', '.')
                    );
                }

                // Check borrowing limit (max 5 active borrowings)
                $activeBorrowingsCount = $user->activeBorrowings()->count();

                if ($activeBorrowingsCount >= 5) {
                    $validator->errors()->add(
                        'user_id',
                        'Peminjam sudah mencapai batas maksimal peminjaman (5 buku).'
                    );
                }

                // Check if user already has this book
                if ($this->has('book_id')) {
                    $hasActiveBorrowing = \App\Models\Borrowing::where('user_id', $this->user_id)
                        ->where('book_id', $this->book_id)
                        ->whereIn('status', ['borrowed', 'overdue'])
                        ->exists();

                    if ($hasActiveBorrowing) {
                        $validator->errors()->add(
                            'book_id',
                            'Peminjam sudah meminjam buku ini.'
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
            // Set default borrow_date to today if not provided
            if (!$this->has('borrow_date')) {
                $this->merge([
                    'borrow_date' => now()->format('Y-m-d'),
                ]);
            }

            // Set default due_date to 7 days from borrow_date if not provided
            if (!$this->has('due_date') && $this->has('borrow_date')) {
                $borrowDate = \Carbon\Carbon::parse($this->borrow_date);
                $this->merge([
                    'due_date' => $borrowDate->addDays(7)->format('Y-m-d'),
                ]);
            }
        }
    }
