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
        ];
    }

    public function messages(): array
    {
        return [
            'email.regex' => 'Hanya email resmi dengan domain @ugm.ac.id yang diizinkan untuk registrasi akun.',
            'email.unique' => 'Email ini sudah terdaftar pada sistem KKNHub.',
        ];
    }
}
