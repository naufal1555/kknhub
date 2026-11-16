<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PicRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        'proposed_team_name',
        'target_location',
        'period',
        'description',
        'status',
        'rejection_reason',
        'verified_by_admin_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(KknTeam::class, 'team_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_admin_id');
    }
}
