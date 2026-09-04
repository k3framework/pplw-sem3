<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function managePayments(User $user): bool
    {
        return $user->isAdmin();
    }

    public function pay(User $user, Reservation $reservation): bool
    {
        return $user->isAdmin() && $reservation->status === 'pending' && ! $reservation->payment && $reservation->isFuture();
    }

    public function refund(User $user, Reservation $reservation): bool
    {
        return $user->isAdmin() && $reservation->status === 'confirmed' && $reservation->payment?->status === 'paid' && $reservation->isFuture();
    }

    public function finish(User $user, Reservation $reservation): bool
    {
        return $user->isAdmin() && $reservation->status === 'confirmed' && $reservation->payment?->status === 'paid' && $reservation->hasEnded();
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return $user->isAdmin() || $user->id === $reservation->user_id;
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $this->view($user, $reservation) && $reservation->status === 'pending' && $reservation->isFuture();
    }

    public function cancel(User $user, Reservation $reservation): bool
    {
        return $this->view($user, $reservation) && $reservation->status === 'pending'
            && ($user->isAdmin() || $reservation->isFuture());
    }
}
