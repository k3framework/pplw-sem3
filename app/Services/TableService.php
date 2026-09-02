<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\RestaurantTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TableService
{
    public function __construct(private ReservationService $reservations) {}

    public function update(RestaurantTable $table, array $data): void
    {
        DB::transaction(function () use ($table, $data) {
            $locked = $this->reservations->lockTables()->find($table->id);
            abort_unless($locked, 404);
            $history = $locked->reservations()->exists();
            if ($history && ($data['code'] !== $locked->code || (int) $data['capacity'] !== $locked->capacity)) {
                throw ValidationException::withMessages(['code' => 'Kode dan kapasitas meja dengan riwayat reservasi tidak dapat diubah.']);
            }
            if (! $data['is_active']) {
                $open = $locked->reservations()->whereIn('status', Reservation::BLOCKING_STATUSES)
                    ->with('timeSlot')->get()->contains(fn ($booking) => $booking->timeSlot->endsOn($booking->reservation_date->format('Y-m-d'))->isFuture());
                if ($open) {
                    throw ValidationException::withMessages(['is_active' => 'Meja masih dipakai reservasi aktif. Batalkan reservasi dahulu.']);
                }
            }
            $locked->update($data);
        }, 5);
    }

    public function delete(RestaurantTable $table): void
    {
        DB::transaction(function () use ($table) {
            $locked = $this->reservations->lockTables()->find($table->id);
            abort_unless($locked, 404);
            if ($locked->reservations()->exists()) {
                throw ValidationException::withMessages(['code' => 'Meja memiliki riwayat reservasi dan tidak dapat dihapus. Gunakan status nonaktif jika tidak ada reservasi aktif.']);
            }
            $locked->delete();
        }, 5);
    }
}
