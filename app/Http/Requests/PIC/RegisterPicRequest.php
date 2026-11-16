<?php

namespace App\Http\Requests\PIC;

use Illuminate\Foundation\Http\FormRequest;

class RegisterPicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'proposed_team_name' => ['required', 'string', 'max:255'],
            'target_location' => ['required', 'string', 'max:255'],
            'period' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'proposed_team_name.required' => 'Nama tim KKN yang diajukan wajib diisi.',
            'target_location.required' => 'Lokasi target pengabdian KKN wajib diisi.',
            'period.required' => 'Periode KKN wajib diisi.',
        ];
    }
}
