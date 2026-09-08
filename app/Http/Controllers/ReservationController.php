<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Http\Requests\ScheduleRequest;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(private ReservationService $service) {}

    public function index(Request $request): View
    {
        $reservations = $request->user()->reservations()->with(['timeSlot', 'table'])
            ->orderByDesc('reservation_date')->orderByDesc('id')->paginate(10);

        return view('reservations.index', compact('reservations'));
    }

    public function create(): View
    {
        return $this->form();
    }

    public function check(ScheduleRequest $request, ?Reservation $reservation = null): RedirectResponse
    {
        if ($reservation) {
            Gate::authorize('update', $reservation);
        }
        $data = $request->validated();
        $notes = $request->input('notes');
        $input = [...$data, 'notes' => is_string($notes) ? mb_substr($notes, 0, 500) : '', 'time_slot_id' => $request->input('time_slot_id')];
        $prefix = $request->user()->isAdmin() ? 'admin.reservations.' : 'reservations.';

        return redirect()->route($prefix.($reservation ? 'edit' : 'create'), $reservation)
            ->with('booking_check', ['reservation_code' => $reservation?->code, 'input' => $input]);
    }

    public function availability(ScheduleRequest $request): JsonResponse
    {
        $data = $request->validated();

        return response()->json(['slots' => $this->service->availability($data['reservation_date'], (int) $data['guest_count'])]);
    }

    public function store(ReservationRequest $request): RedirectResponse
    {
        $reservation = $this->service->create($request->user(), $request->validated());

        return redirect()->route('reservations.show', $reservation)->with('success', 'Reservasi berhasil dibuat. Simpan kode reservasi Anda.');
    }

    public function show(Reservation $reservation): View
    {
        Gate::authorize('view', $reservation);
        $reservation->load(['user', 'table', 'timeSlot', 'payment']);

        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation): View
    {
        Gate::authorize('update', $reservation);
        $reservation->load(['user', 'timeSlot', 'table']);

        return $this->form($reservation);
    }

    public function update(ReservationRequest $request, Reservation $reservation): RedirectResponse
    {
        Gate::authorize('update', $reservation);
        $reservation = $this->service->update($request->user(), $reservation, $request->validated());
        $route = $request->user()->isAdmin() ? 'admin.reservations.show' : 'reservations.show';

        return redirect()->route($route, $reservation)->with('success', 'Perubahan reservasi berhasil disimpan.');
    }

    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->service->cancel($request->user(), $reservation);
        $route = $request->user()->isAdmin() ? 'admin.reservations.show' : 'reservations.show';

        return redirect()->route($route, $reservation)->with('success', 'Reservasi dibatalkan. Meja tersedia kembali untuk pelanggan lain.');
    }

    private function form(?Reservation $reservation = null): View
    {
        $check = session('booking_check');
        $input = $reservation ? ['reservation_date' => $reservation->reservation_date->format('Y-m-d'), 'guest_count' => $reservation->guest_count, 'time_slot_id' => $reservation->time_slot_id, 'notes' => $reservation->notes] : [];
        if ($check && $check['reservation_code'] === $reservation?->code) {
            $input = $check['input'];
        }
        $old = session()->getOldInput();
        if (array_key_exists('reservation_date', $old)) {
            $input = array_intersect_key($old, array_flip(['reservation_date', 'guest_count', 'time_slot_id', 'notes']));
        }
        $availability = Validator::make($input, (new ScheduleRequest)->rules())->passes()
            ? $this->service->availability($input['reservation_date'], (int) $input['guest_count'], $reservation) : null;

        return view('reservations.form', compact('reservation', 'availability', 'input'));
    }
}
