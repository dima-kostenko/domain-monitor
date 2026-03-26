<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainCheck extends Model
{
    use HasFactory;

    // domain_checks has no updated_at column
    public const UPDATED_AT = null;

    protected $fillable = [
        'domain_id',
        'status',
        'response_code',
        'response_time',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'response_code' => 'integer',
            'response_time' => 'integer',
            'created_at'    => 'datetime',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isOnline(): bool
    {
        return $this->status === 'online';
    }

    public function getResponseTimeFormattedAttribute(): string
    {
        if ($this->response_time === null) {
            return '—';
        }

        return $this->response_time >= 1000
            ? number_format($this->response_time / 1000, 2) . ' s'
            : $this->response_time . ' ms';
    }
}
