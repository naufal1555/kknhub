<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'registration_type' => ['nullable', 'in:mahasiswa,pic'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@ugm\.ac\.id$/',
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nim' => ['nullable', 'string', 'max:30'],
            'faculty' => ['nullable', 'string', 'max:100'],
            'major' => ['nullable', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'proposed_team_name' => ['required_if:registration_type,pic', 'nullable', 'string', 'max:255'],
            'target_location' => ['required_if:registration_type,pic', 'nullable', 'string', 'max:255'],
            'period' => ['required_if:registration_type,pic', 'nullable', 'string', 'max:100'],
            'team_description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.regex' => 'Hanya email resmi dengan domain @ugm.ac.id yang diizinkan untuk registrasi akun.',
            'email.unique' => 'Email ini sudah terdaftar pada sistem KKNHub.',
            'proposed_team_name.required_if' => 'Nama tim KKN wajib diisi jika mendaftar sebagai Koordinator / PIC Tim.',
            'target_location.required_if' => 'Lokasi target pengabdian wajib diisi jika mendaftar sebagai Koordinator / PIC Tim.',
            'period.required_if' => 'Periode pelaksanaan KKN wajib diisi jika mendaftar sebagai Koordinator / PIC Tim.',
        ];
    }
}
