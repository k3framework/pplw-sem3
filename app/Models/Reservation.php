<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    public const BLOCKING_STATUSES = ['pending', 'confirmed'];

    public const LABELS = [
        'pending' => 'Menunggu pembayaran',
        'confirmed' => 'Terkonfirmasi',
        'cancelled' => 'Dibatalkan',
        'completed' => 'Selesai',
        'no_show' => 'Tidak hadir',
    ];

    protected $fillable = ['code', 'user_id', 'restaurant_table_id', 'time_slot_id', 'reservation_date', 'guest_count', 'notes', 'deposit_amount', 'status'];

    protected function casts(): array
    {
        return ['reservation_date' => 'immutable_date', 'guest_count' => 'integer', 'deposit_amount' => 'decimal:2'];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'restaurant_table_id');
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function isFuture(): bool
    {
        return $this->timeSlot->startsOn($this->reservation_date->format('Y-m-d'))->isFuture();
    }

    public function statusLabel(): string
    {
        return self::LABELS[$this->status];
    }

    public function hasEnded(): bool
    {
        return $this->timeSlot->endsOn($this->reservation_date->format('Y-m-d'))->isPast();
    }
}
