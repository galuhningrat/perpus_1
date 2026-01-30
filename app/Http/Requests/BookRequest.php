<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
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
        $bookId = $this->route('book') ? $this->route('book')->id : null;

        return [
            'title' => 'required|string|max:500',
            'author' => 'required|string|max:255',
            'edition' => 'nullable|string|max:100',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'category_id' => 'required|exists:categories,id',
            'isbn' => [
                'nullable',
                'string',
                'max:20',
                'unique:books,isbn,' . $bookId,
                'regex:/^(?:\d{10}|\d{13})$/', // ISBN-10 or ISBN-13
            ],
            'stock' => 'required|integer|min:1|max:1000',
            'shelf_location' => 'required|string|max:50|regex:/^[A-Z]{2}-[A-Z0-9]{3}$/',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul buku',
            'author' => 'pengarang',
            'edition' => 'edisi',
            'publisher' => 'penerbit',
            'publication_year' => 'tahun terbit',
            'category_id' => 'kategori',
            'isbn' => 'ISBN',
            'stock' => 'stok',
            'shelf_location' => 'lokasi rak',
            'description' => 'deskripsi',
            'cover_image' => 'gambar sampul',
            'is_active' => 'status aktif',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul buku harus diisi',
            'title.max' => 'Judul buku maksimal 500 karakter',
            'author.required' => 'Pengarang harus diisi',
            'category_id.required' => 'Kategori harus dipilih',
            'category_id.exists' => 'Kategori yang dipilih tidak valid',
            'isbn.unique' => 'ISBN sudah terdaftar',
            'isbn.regex' => 'ISBN harus 10 atau 13 digit angka',
            'stock.required' => 'Jumlah stok harus diisi',
            'stock.min' => 'Stok minimal 1',
            'shelf_location.required' => 'Lokasi rak harus diisi',
            'shelf_location.regex' => 'Format lokasi rak: XX-XXX (contoh: IF-001)',
            'cover_image.image' => 'File harus berupa gambar',
            'cover_image.mimes' => 'Gambar harus format: jpeg, jpg, png, atau webp',
            'cover_image.max' => 'Ukuran gambar maksimal 2MB',
            'publication_year.min' => 'Tahun terbit minimal 1900',
            'publication_year.max' => 'Tahun terbit tidak valid',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate shelf location if not provided
        if (!$this->has('shelf_location') && $this->has('category_id')) {
            $category = \App\Models\Category::find($this->category_id);
            if ($category) {
                $latestBook = \App\Models\Book::where('category_id', $category->id)
                    ->where('shelf_location', 'LIKE', $category->code . '-%')
                    ->orderBy('shelf_location', 'desc')
                    ->first();

                if ($latestBook) {
                    $lastNumber = intval(substr($latestBook->shelf_location, -3));
                    $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '001';
                }

                $this->merge([
                    'shelf_location' => $category->code . '-' . $newNumber,
                ]);
            }
        }

        // Set default is_active if not provided
        if (!$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        }
    }
}
