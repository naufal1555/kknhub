<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactPerson extends Model
{
    use HasFactory;

    protected $table = 'contact_persons';

    protected $fillable = [
        'team_id',
        'name',
        'role_title',
        'phone_number',
        'line_id',
    ];

    public function getRoleLabelAttribute(): string
    {
        return $this->role_title ?: 'Narahubung';
    }

    public function getPhoneAttribute(): string
    {
        return $this->phone_number;
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(KknTeam::class, 'team_id');
    }
}
