<?php

namespace App\Http\Requests\Application;

use App\Models\Application;
use App\Models\Division;
use App\Models\OprecCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SubmitApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'division_id' => ['required', 'integer', 'exists:divisions,id'],
            'cv_file' => ['required', 'file', 'mimes:pdf', 'max:5120'], // FR-11: CV wajib, PDF maks 5 MB
            'portfolio_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // FR-11: Portofolio opsional, PDF maks 10 MB
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'division_id.required' => 'Divisi wajib dipilih.',
            'division_id.exists' => 'Divisi yang dipilih tidak valid.',
            'cv_file.required' => 'Berkas CV wajib diunggah dalam format PDF.',
            'cv_file.mimes' => 'Format berkas CV harus PDF.',
            'cv_file.max' => 'Ukuran berkas CV tidak boleh melebihi 5 MB.',
            'portfolio_file.mimes' => 'Format berkas Portofolio harus PDF.',
            'portfolio_file.max' => 'Ukuran berkas Portofolio tidak boleh melebihi 10 MB.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $catalogId = $this->route('catalogId');
            $catalog = OprecCatalog::with('divisions')->find($catalogId);

            if (!$catalog) {
                $validator->errors()->add('catalog', 'Katalog OPREC tidak ditemukan.');
                return;
            }

            // Validasi status publikasi dan batas waktu
            if ($catalog->status !== 'published' || ($catalog->deadline && $catalog->deadline->isPast())) {
                $validator->errors()->add('catalog', 'Pendaftaran untuk katalog OPREC ini telah ditutup atau belum dibuka.');
                return;
            }

            // Validasi divisi milik katalog
            $division = $catalog->divisions->firstWhere('id', (int) $this->input('division_id'));
            if (!$division) {
                $validator->errors()->add('division_id', 'Divisi yang dipilih bukan bagian dari katalog ini.');
                return;
            }

            // FR-12: Single submission per tim (cegah lamaran ganda ke tim yang sama)
            $existingApplication = Application::where('user_id', auth()->id())
                ->where('team_id', $catalog->team_id)
                ->whereIn('status', ['pending', 'accepted'])
                ->first();

            if ($existingApplication) {
                $validator->errors()->add('division_id', 'Anda sudah mengajukan lamaran pada tim KKN ini.');
            }
        });
    }
}
