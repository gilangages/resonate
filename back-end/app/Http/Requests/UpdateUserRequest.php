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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            // 'email' => 'required|email|unique:users,email,' . $this->user()->id,
            // Password nullable (boleh kosong), jika diisi minimal 6 karakter & harus confirmed
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'max: 2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name . required' => 'Nama pengguna wajib diisi . ',
            'password . min' => 'Password baru minimal harus 8 karakter . ',
            'password . confirmed' => 'Konfirmasi password baru tidak cocok . ',
            'avatar . image' => 'File harus berupa gambar . ',
            'avatar . max' => 'Ukuran gambar maksimal 2MB . ',
        ];
    }
}
