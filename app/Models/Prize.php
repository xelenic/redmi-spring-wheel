<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prize extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'display_name',
        'stock_total',
        'stock_remaining',
        'image_path',
        'sort_order',
    ];

    protected $casts = [
        'stock_total' => 'integer',
        'stock_remaining' => 'integer',
    ];

    public function spinsAwarded(): HasMany
    {
        return $this->hasMany(Spin::class, 'awarded_prize_id');
    }

    public function spinsRequested(): HasMany
    {
        return $this->hasMany(Spin::class, 'requested_prize_id');
    }

    public function scopeLimited(Builder $query): Builder
    {
        return $query->whereNotNull('stock_total');
    }

    public function isLimited(): bool
    {
        return $this->stock_total !== null;
    }

    public function decrementStock(): void
    {
        if (!$this->isLimited()) {
            return;
        }

        if ($this->stock_remaining === null) {
            $this->stock_remaining = 0;
        }

        $this->stock_remaining = max(0, $this->stock_remaining - 1);
        $this->save();
    }

    public function issue(): bool
    {
        if (!$this->isLimited()) {
            return true;
        }

        if ($this->stock_remaining !== null && $this->stock_remaining > 0) {
            $this->stock_remaining -= 1;
            $this->save();
            return true;
        }

        return false;
    }

    public function remaining(): ?int
    {
        return $this->stock_remaining;
    }
}

