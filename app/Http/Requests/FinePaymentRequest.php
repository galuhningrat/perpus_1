<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    if ($this->route('fine')) {
                        $fine = $this->route('fine');
                        $remaining = $fine->getRemainingAmount();

                        if ($value > $remaining) {
                            $fail('Jumlah pembayaran melebihi sisa denda (Rp ' .
                                number_format($remaining, 0, ',', '.') . ')');
                        }
                    }
                },
            ],
            'payment_method' => 'nullable|in:cash,transfer,qris',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'amount' => 'jumlah pembayaran',
            'payment_method' => 'metode pembayaran',
            'notes' => 'catatan',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Jumlah pembayaran harus diisi',
            'amount.numeric' => 'Jumlah pembayaran harus berupa angka',
            'amount.min' => 'Jumlah pembayaran minimal Rp 1',
            'payment_method.in' => 'Metode pembayaran tidak valid',
            'notes.max' => 'Catatan maksimal 500 karakter',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->route('fine')) {
                $fine = $this->route('fine');

                if ($fine->isFullyPaid()) {
                    $validator->errors()->add(
                        'fine',
                        'Denda sudah lunas.'
                    );
                }
            }
        });
    }
}
