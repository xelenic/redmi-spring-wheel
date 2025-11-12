<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Spin extends Model
{
    use HasFactory;

    protected $fillable = [
        'requested_prize_id',
        'awarded_prize_id',
        'issued',
        'result_label',
        'meta',
    ];

    protected $casts = [
        'issued' => 'boolean',
        'meta' => 'array',
    ];

    public function requestedPrize(): BelongsTo
    {
        return $this->belongsTo(Prize::class, 'requested_prize_id');
    }

    public function awardedPrize(): BelongsTo
    {
        return $this->belongsTo(Prize::class, 'awarded_prize_id');
    }
}

