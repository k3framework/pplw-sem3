<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeSlot extends Model
{
    protected $fillable = ['start_time', 'end_time', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function label(): string
    {
        return str_replace(':', '.', substr($this->start_time, 0, 5)).' - '.str_replace(':', '.', substr($this->end_time, 0, 5));
    }

    public function startsOn(string $date): CarbonImmutable
    {
        return CarbonImmutable::parse($date.' '.$this->start_time, config('restaurant.timezone'));
    }

    public function endsOn(string $date): CarbonImmutable
    {
        return CarbonImmutable::parse($date.' '.$this->end_time, config('restaurant.timezone'));
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
