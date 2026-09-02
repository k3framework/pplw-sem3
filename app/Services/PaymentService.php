<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(private ReservationService $reservations) {}

    public function record(User $actor, Reservation $reservation, array $data): Payment
    {
        Gate::forUser($actor)->authorize('managePayments', Reservation::class);

        return DB::transaction(function () use ($actor, $reservation, $data) {
            $locked = $this->lockReservation($reservation);
            $payment = $locked->payment()->lockForUpdate()->first();
            if ($payment || $locked->status !== 'pending') {
                throw ValidationException::withMessages(['payment' => 'Pembayaran sudah tercatat atau reservasi tidak lagi menunggu pembayaran.']);
            }
            if (! $locked->isFuture()) {
                throw ValidationException::withMessages(['payment' => 'Waktu mulai sudah lewat. Deposit tidak dapat dicatat.']);
            }
            $payment = Payment::create([
                'code' => 'pay-'.now(config('restaurant.timezone'))->format('Ymd').'-'.Str::lower((string) Str::ulid()),
                'reservation_id' => $locked->id,
                'processed_by' => $actor->id,
                'amount' => $locked->deposit_amount,
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            $locked->update(['status' => 'confirmed']);

            return $payment;
        }, 5);
    }

    public function refund(User $actor, Reservation $reservation): void
    {
        Gate::forUser($actor)->authorize('managePayments', Reservation::class);
        DB::transaction(function () use ($actor, $reservation) {
            $locked = $this->lockReservation($reservation);
            $payment = $locked->payment()->lockForUpdate()->first();
            if ($locked->status !== 'confirmed' || ! $payment || $payment->status !== 'paid') {
                throw ValidationException::withMessages(['payment' => 'Hanya reservasi terkonfirmasi dengan deposit dibayar yang dapat dikembalikan.']);
            }
            if (! $locked->isFuture()) {
                throw ValidationException::withMessages(['payment' => 'Waktu mulai sudah lewat. Reservasi terkonfirmasi tidak dapat dibatalkan atau dikembalikan depositnya.']);
            }
            $payment->update(['status' => 'refunded', 'refunded_at' => now(), 'refunded_by' => $actor->id]);
            $locked->update(['status' => 'cancelled']);
        }, 5);
    }

    public function finish(User $actor, Reservation $reservation, string $status): void
    {
        Gate::forUser($actor)->authorize('managePayments', Reservation::class);
        DB::transaction(function () use ($reservation, $status) {
            $locked = $this->lockReservation($reservation);
            $payment = $locked->payment()->lockForUpdate()->first();
            if (! in_array($status, ['completed', 'no_show'], true) || $locked->status !== 'confirmed' || ! $payment || $payment->status !== 'paid') {
                throw ValidationException::withMessages(['status' => 'Status akhir hanya dapat dicatat untuk reservasi terkonfirmasi dengan deposit dibayar.']);
            }
            if (! $locked->hasEnded()) {
                throw ValidationException::withMessages(['status' => 'Tunggu sampai slot kunjungan berakhir untuk mencatat status akhir.']);
            }
            $locked->update(['status' => $status]);
        }, 5);
    }

    private function lockReservation(Reservation $reservation): Reservation
    {
        $this->reservations->lockTables();

        return Reservation::whereKey($reservation->id)->lockForUpdate()->firstOrFail();
    }
}
