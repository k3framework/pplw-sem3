<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\ReservationStatusRequest;
use App\Models\Payment;
use App\Models\Reservation;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $service) {}

    public function index(Request $request): View
    {
        $filter = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(Payment::LABELS))],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $payments = Payment::with('reservation')
            ->when($filter['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filter['q'] ?? null, function ($query, $q) {
                $search = '%'.addcslashes($q, '%_\\').'%';
                $query->where(fn ($group) => $group->where('code', 'like', $search)
                    ->orWhereHas('reservation', fn ($booking) => $booking->where('code', 'like', $search)));
            })
            ->orderByDesc('paid_at')->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function create(Reservation $reservation): View
    {
        Gate::authorize('pay', $reservation);

        return view('admin.payments.form', compact('reservation'));
    }

    public function store(PaymentRequest $request, Reservation $reservation): RedirectResponse
    {
        try {
            $this->service->record($request->user(), $reservation, $request->validated());
        } catch (ValidationException $exception) {
            throw $exception->redirectTo(route('admin.reservations.show', $reservation));
        }

        return redirect()->route('admin.reservations.show', $reservation)->with('success', 'Deposit berhasil dicatat. Reservasi terkonfirmasi.');
    }

    public function refund(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->service->refund($request->user(), $reservation);

        return redirect()->route('admin.reservations.show', $reservation)->with('success', 'Pengembalian deposit penuh tercatat. Reservasi dibatalkan dan meja dilepas.');
    }

    public function finish(ReservationStatusRequest $request, Reservation $reservation): RedirectResponse
    {
        $this->service->finish($request->user(), $reservation, $request->validated('status'));

        return redirect()->route('admin.reservations.show', $reservation)->with('success', 'Status akhir kunjungan berhasil dicatat.');
    }
}
