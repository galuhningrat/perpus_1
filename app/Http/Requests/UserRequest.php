<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash; // Tambahkan ini agar Hash tidak merah

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User $user */
        $user = $this->user();

        // Admin can create/update users
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Users can update their own profile
        if ($this->route('user')) {
            return $user->id === $this->route('user')->id;
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
        /** @var \App\Models\User $user */
        $user = $this->user();

        $userId = $this->route('user') ? $this->route('user')->id : null;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $isAdmin = $user->hasAdminAccess();

        $rules = [
            'nim_nidn' => [
                'required',
                'string',
                'max:20',
                'unique:users,nim_nidn,' . $userId,
                'regex:/^[A-Z0-9]+$/',
            ],
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $userId,
            ],
            'phone' => 'nullable|string|max:20|regex:/^(\+62|62|0)[0-9]{9,12}$/',
        ];

        // Password rules
        if (!$isUpdate) {
            // Creating new user
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            // Updating user
            $rules['password'] = 'nullable|string|min:8|confirmed';

            // If user is changing their own password, require current password
            if (!$isAdmin && $this->filled('password')) {
                $rules['current_password'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($user) {
                        // Menggunakan Hash::check (tanpa backslash karena sudah di-import)
                        if (!Hash::check($value, $user->password)) {
                            $fail('Password saat ini tidak sesuai.');
                        }
                    },
                ];
            }
        }

        // Admin-only fields
        if ($isAdmin) {
            $rules['role'] = 'required|in:super_admin,pustakawan,member';
            $rules['prodi'] = 'nullable|in:Informatika,Elektro,Mesin,Umum';
            $rules['is_active'] = 'boolean';
        } else {
            // Members can only update certain fields
            $rules['prodi'] = 'nullable|in:Informatika,Elektro,Mesin,Umum';
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nim_nidn' => 'NIM/NIDN',
            'name' => 'nama',
            'email' => 'email',
            'password' => 'password',
            'current_password' => 'password saat ini',
            'role' => 'role',
            'prodi' => 'program studi',
            'phone' => 'nomor telepon',
            'is_active' => 'status aktif',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nim_nidn.required' => 'NIM/NIDN harus diisi',
            'nim_nidn.unique' => 'NIM/NIDN sudah terdaftar',
            'nim_nidn.regex' => 'NIM/NIDN hanya boleh berisi huruf kapital dan angka',
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            'current_password.required' => 'Password saat ini harus diisi',
            'role.required' => 'Role harus dipilih',
            'role.in' => 'Role tidak valid',
            'prodi.in' => 'Program studi tidak valid',
            'phone.regex' => 'Format nomor telepon tidak valid (contoh: 081234567890)',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /** @var \App\Models\User $user */
            $user = $this->user();

            // Validate role-prodi consistency
            if ($this->has('role') && $this->role === 'member') {
                if (!$this->filled('prodi')) {
                    $validator->errors()->add(
                        'prodi',
                        'Program studi harus diisi untuk member.'
                    );
                }
            }

            // Super admin cannot change their own role
            if ($this->route('user') && $user->id === $this->route('user')->id) {
                if ($this->has('role') && $user->role === 'super_admin') {
                    if ($this->role !== 'super_admin') {
                        $validator->errors()->add(
                            'role',
                            'Super admin tidak dapat mengubah role sendiri.'
                        );
                    }
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert nim_nidn to uppercase
        if ($this->has('nim_nidn')) {
            $this->merge([
                'nim_nidn' => strtoupper($this->nim_nidn),
            ]);
        }

        // Set default is_active for new users
        if (!$this->route('user') && !$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        }

        // Remove empty password field
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            if (!$this->filled('password')) {
                $this->request->remove('password');
                $this->request->remove('password_confirmation');
            }
        }
    }
}
