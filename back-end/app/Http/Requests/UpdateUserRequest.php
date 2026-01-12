<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ubah jadi true karena user yang login boleh akses ini
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // Password nullable (boleh kosong), jika diisi minimal 6 karakter & harus confirmed
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ];
    }
}
