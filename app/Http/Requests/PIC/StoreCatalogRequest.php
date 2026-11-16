<?php

namespace App\Http\Requests\PIC;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isPic();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['required', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul katalog OPREC wajib diisi.',
            'deadline.required' => 'Batas waktu pendaftaran (deadline) wajib ditentukan.',
            'deadline.after' => 'Deadline pendaftaran harus berada di masa depan.',
        ];
    }
}
