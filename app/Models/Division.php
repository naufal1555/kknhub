<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'catalog_id',
        'name',
        'job_description',
        'requirements',
        'quota',
    ];

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(OprecCatalog::class, 'catalog_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'division_id');
    }

    public function acceptedCount(): int
    {
        return $this->applications()->where('status', 'accepted')->count();
    }

    public function isFull(): bool
    {
        return $this->acceptedCount() >= $this->quota;
    }

    public function remainingQuota(): int
    {
        return max(0, $this->quota - $this->acceptedCount());
    }
}
