<?php

namespace App\Http\Requests\PIC;

use Illuminate\Foundation\Http\FormRequest;

class StoreDivisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isPic();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'job_description' => ['nullable', 'string', 'max:3000'],
            'requirements' => ['nullable', 'string', 'max:3000'],
            'quota' => ['required', 'integer', 'min:1'], // Sesuai FR-07: Kuota tidak boleh bernilai negatif atau nol
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama divisi wajib diisi.',
            'quota.required' => 'Kuota divisi wajib ditentukan.',
            'quota.min' => 'Kuota divisi minimal 1 orang (tidak boleh bernilai negatif atau nol).',
            'quota.integer' => 'Kuota divisi harus berupa angka bulat positif.',
        ];
    }
}
