<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const METHODS = ['cash' => 'Tunai', 'transfer' => 'Transfer'];

    public const LABELS = ['paid' => 'Dibayar', 'refunded' => 'Dikembalikan'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'paid_at' => 'immutable_datetime', 'refunded_at' => 'immutable_datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function statusLabel(): string
    {
        return self::LABELS[$this->status];
    }

    public function methodLabel(): string
    {
        return self::METHODS[$this->method];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function refunder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }
}
