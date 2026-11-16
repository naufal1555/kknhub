<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OprecCatalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'title',
        'description',
        'start_date',
        'deadline',
        'status',
        'rejection_note',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'deadline' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(KknTeam::class, 'team_id');
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class, 'catalog_id');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isExpired(): bool
    {
        return $this->deadline && $this->deadline->isPast();
    }
}
