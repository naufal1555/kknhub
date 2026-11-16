<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ApplicationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'document_type',
        's3_key',
        'original_filename',
        'file_size_bytes',
        'mime_type',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    /**
     * Menghasilkan Pre-signed Temporary URL (S3 / MinIO) atau local signed URL
     */
    public function getTemporaryUrl(int $minutes = 15): string
    {
        $disk = config('filesystems.default');

        if ($disk === 's3') {
            return Storage::disk('s3')->temporaryUrl(
                $this->s3_key,
                now()->addMinutes($minutes)
            );
        }

        // Fallback untuk local storage / testing driver
        return Storage::disk($disk)->url($this->s3_key);
    }
}
