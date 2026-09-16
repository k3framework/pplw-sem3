<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\ReservationService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisitDemoSeeder extends Seeder
{
    public function run(ReservationService $reservations, PaymentService $payments): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('demo hanya untuk local/testing.');
        }
        $this->call(DatabaseSeeder::class);
        $customer = User::where('email', 'pelanggan@example.test')->sole();
        $admin = User::where('email', 'admin@example.test')->sole();
        $yesterday = now(config('restaurant.timezone'))->subDay()->startOfDay();
        $previous = Carbon::getTestNow();
        $previousImmutable = CarbonImmutable::getTestNow();
        try {
            Carbon::setTestNow($yesterday->copy()->setTime(10, 0)->utc());
            CarbonImmutable::setTestNow(Carbon::getTestNow());
            foreach ([1, 2] as $slot) {
                $marker = 'Kunjungan lalu ke '.$slot;
                if (Reservation::where('notes', $marker)->exists()) {
                    continue;
                }
                DB::transaction(function () use ($reservations, $payments, $customer, $admin, $yesterday, $slot, $marker) {
                    $booking = $reservations->create($customer, [
                        'reservation_date' => $yesterday->toDateString(), 'guest_count' => 2, 'time_slot_id' => $slot, 'notes' => $marker,
                    ]);
                    $payments->record($admin, $booking, ['method' => 'cash', 'reference' => 'skenario-demo-kunjungan-'.$slot]);
                });
            }
        } finally {
            Carbon::setTestNow($previous);
            CarbonImmutable::setTestNow($previousImmutable);
        }
    }
}
