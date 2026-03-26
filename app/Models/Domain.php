<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'check_interval',
        'timeout',
        'method',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'check_interval' => 'integer',
            'timeout'        => 'integer',
            'is_active'      => 'boolean',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checks(): HasMany
    {
        return $this->hasMany(DomainCheck::class);
    }

    public function latestCheck(): HasOne
    {
        return $this->hasOne(DomainCheck::class)->latestOfMany();
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isOnline(): bool
    {
        return $this->latestCheck?->status === 'online';
    }

    public function getFullUrlAttribute(): string
    {
        return 'https://' . $this->name;
    }
}
