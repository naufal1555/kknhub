<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class DocumentStorageService
{
    /**
     * Simpan file dokumen lamaran ke storage privat (AWS S3 atau Local fallback).
     *
     * @param Application $application
     * @param UploadedFile $file
     * @param string $type 'cv' atau 'portfolio'
     * @return ApplicationDocument
     */
    public function storeDocument(Application $application, UploadedFile $file, string $type): ApplicationDocument
    {
        if (!in_array($type, ['cv', 'portfolio'], true)) {
            throw new InvalidArgumentException("Tipe dokumen tidak valid: {$type}");
        }

        // Validasi ekstensi dan MIME
        $mime = $file->getMimeType();
        if ($file->getClientOriginalExtension() !== 'pdf' && !str_contains($mime, 'pdf')) {
            throw new InvalidArgumentException("Format file harus berupa PDF.");
        }

        // Validasi ukuran batas: CV maks 5MB, Portofolio maks 10MB
        $maxBytes = $type === 'cv' ? 5 * 1024 * 1024 : 10 * 1024 * 1024;
        if ($file->getSize() > $maxBytes) {
            $limitMb = $type === 'cv' ? 5 : 10;
            throw new InvalidArgumentException("Ukuran berkas {$type} melebihi batas maksimum {$limitMb} MB.");
        }

        $disk = config('filesystems.default');
        $randomHash = Str::random(24);
        $fileName = "{$type}_{$randomHash}.pdf";
        $folder = "applications/{$application->id}";

        // Simpan file dengan visibilitas private
        $path = $file->storeAs($folder, $fileName, [
            'disk' => $disk,
            'visibility' => 'private',
        ]);

        return ApplicationDocument::create([
            'application_id' => $application->id,
            'document_type' => $type,
            's3_key' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_size_bytes' => $file->getSize(),
            'mime_type' => $mime ?: 'application/pdf',
        ]);
    }

    /**
     * Dapatkan URL aman sementara (Pre-signed URL) untuk melihat dokumen.
     */
    public function getTemporaryUrl(ApplicationDocument $document, int $minutes = 15): string
    {
        $disk = config('filesystems.default');

        if ($disk === 's3') {
            return Storage::disk('s3')->temporaryUrl(
                $document->s3_key,
                now()->addMinutes($minutes)
            );
        }

        // Untuk driver lokal dalam pengujian / simulasi lokal
        if (Storage::disk($disk)->exists($document->s3_key)) {
            // Jika driver lokal mendukung temporaryUrl (Laravel 11 URL signing)
            try {
                return Storage::disk($disk)->temporaryUrl($document->s3_key, now()->addMinutes($minutes));
            } catch (\Throwable $e) {
                // Fallback ke endpoint route download internal
                return route('applications.documents.download', $document->id);
            }
        }

        return route('applications.documents.download', $document->id);
    }

    /**
     * Hapus dokumen dari storage fisik
     */
    public function deleteDocument(ApplicationDocument $document): bool
    {
        $disk = config('filesystems.default');
        if (Storage::disk($disk)->exists($document->s3_key)) {
            Storage::disk($disk)->delete($document->s3_key);
        }
        return $document->delete();
    }
}
