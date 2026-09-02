<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function availability(string $date, int $guests, ?Reservation $exclude = null): array
    {
        $occupied = Reservation::whereDate('reservation_date', $date)
            ->whereIn('status', Reservation::BLOCKING_STATUSES)
            ->when($exclude, fn ($query) => $query->where('id', '!=', $exclude->id))
            ->get(['restaurant_table_id', 'time_slot_id']);
        $tables = RestaurantTable::where('is_active', true)->where('capacity', '>=', $guests)->get();

        return TimeSlot::where('is_active', true)->orderBy('start_time')->get()->map(function (TimeSlot $slot) use ($date, $tables, $occupied) {
            $taken = $occupied->where('time_slot_id', $slot->id)->pluck('restaurant_table_id');
            $count = $slot->startsOn($date)->isFuture() ? $tables->whereNotIn('id', $taken)->count() : 0;

            return ['id' => $slot->id, 'label' => $slot->label(), 'count' => $count];
        })->all();
    }

    public function create(User $user, array $data): Reservation
    {
        return DB::transaction(function () use ($user, $data) {
            $tables = $this->lockTables();
            $table = $this->allocate($tables, $data);

            return Reservation::create([
                ...$data,
                'code' => 'd10-'.str_replace('-', '', $data['reservation_date']).'-'.Str::lower((string) Str::ulid()),
                'user_id' => $user->id,
                'restaurant_table_id' => $table->id,
                'deposit_amount' => config('restaurant.deposit_amount'),
                'status' => 'pending',
            ]);
        }, 5);
    }

    public function update(User $actor, Reservation $reservation, array $data): Reservation
    {
        return DB::transaction(function () use ($actor, $reservation, $data) {
            $tables = $this->lockTables();
            $locked = Reservation::whereKey($reservation->id)->lockForUpdate()->firstOrFail();
            Gate::forUser($actor)->authorize('update', $locked);
            $table = $this->allocate($tables, $data, $locked->id);
            $locked->update([...$data, 'restaurant_table_id' => $table->id]);

            return $locked;
        }, 5);
    }

    public function cancel(User $actor, Reservation $reservation): void
    {
        DB::transaction(function () use ($actor, $reservation) {
            $this->lockTables();
            $locked = Reservation::whereKey($reservation->id)->lockForUpdate()->firstOrFail();
            Gate::forUser($actor)->authorize('cancel', $locked);
            $locked->update(['status' => 'cancelled']);
        }, 5);
    }

    public function lockTables(): Collection
    {
        // All booking and table mutations take these locks first, in the same order.
        return RestaurantTable::orderBy('id')->lockForUpdate()->get();
    }

    private function allocate(Collection $tables, array $data, ?int $excludeId = null): RestaurantTable
    {
        $slot = TimeSlot::where('is_active', true)->find($data['time_slot_id']);
        $today = now(config('restaurant.timezone'))->startOfDay();
        $date = $data['reservation_date'];
        if ($date < $today->toDateString() || $date > $today->addDays(config('restaurant.max_days_ahead'))->toDateString()) {
            throw ValidationException::withMessages(['reservation_date' => 'Pilih tanggal hari ini sampai 30 hari ke depan.']);
        }
        if (! $slot || ! $slot->startsOn($date)->isFuture()) {
            throw ValidationException::withMessages(['time_slot_id' => 'Jam kunjungan sudah lewat atau tidak aktif. Pilih jam lain.']);
        }
        $taken = Reservation::whereDate('reservation_date', $date)->where('time_slot_id', $slot->id)
            ->whereIn('status', Reservation::BLOCKING_STATUSES)
            ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
            ->lockForUpdate()->get()->pluck('restaurant_table_id');
        $table = $tables->where('is_active', true)->where('capacity', '>=', (int) $data['guest_count'])
            ->whereNotIn('id', $taken)->sortBy(fn ($table) => sprintf('%02d-%020d', $table->capacity, $table->id))->first();
        if (! $table) {
            throw ValidationException::withMessages(['time_slot_id' => 'Meja untuk jumlah tamu ini sudah penuh. Cek kembali atau pilih tanggal/jam lain.']);
        }

        return $table;
    }
}
