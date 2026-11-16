<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KknTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'period',
        'target_location',
        'description',
        'pic_user_id',
    ];

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    public function contactPersons(): HasMany
    {
        return $this->hasMany(ContactPerson::class, 'team_id');
    }

    public function catalogs(): HasMany
    {
        return $this->hasMany(OprecCatalog::class, 'team_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'team_id');
    }
}
