<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $categoryId = $this->route('category') ? $this->route('category')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:categories,name,' . $categoryId,
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                'unique:categories,code,' . $categoryId,
                'regex:/^[A-Z]{2}$/', // 2 uppercase letters
            ],
            'description' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama kategori',
            'code' => 'kode kategori',
            'description' => 'deskripsi',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori harus diisi',
            'name.unique' => 'Nama kategori sudah ada',
            'code.required' => 'Kode kategori harus diisi',
            'code.unique' => 'Kode kategori sudah digunakan',
            'code.regex' => 'Kode kategori harus 2 huruf kapital (contoh: IF, EL, MS)',
            'description.max' => 'Deskripsi maksimal 1000 karakter',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert code to uppercase
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper($this->code),
            ]);
        }
    }
}
