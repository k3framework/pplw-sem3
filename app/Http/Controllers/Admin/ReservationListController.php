<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReservationListController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filter = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', Rule::in(array_keys(Reservation::LABELS))],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $reservations = Reservation::with(['user', 'table', 'timeSlot'])
            ->when($filter['date'] ?? null, fn ($query, $date) => $query->whereDate('reservation_date', $date))
            ->when($filter['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filter['q'] ?? null, fn ($query, $q) => $query->where('code', 'like', '%'.addcslashes($q, '%_\\').'%'))
            ->orderByDesc('reservation_date')->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admin.reservations', compact('reservations'));
    }
}
