<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = now(config('restaurant.timezone'))->toDateString();
        $todayCount = Reservation::whereDate('reservation_date', $today)->whereIn('status', Reservation::BLOCKING_STATUSES)->count();
        $pendingCount = Reservation::where('status', 'pending')->whereDate('reservation_date', '>=', $today)->count();
        $start = now(config('restaurant.timezone'))->startOfDay();
        $end = $start->copy()->addDay();
        $depositTotal = Payment::where('paid_at', '>=', $start->utc())->where('paid_at', '<', $end->utc())->sum('amount');
        $reservations = Reservation::with(['user', 'table', 'timeSlot'])->whereDate('reservation_date', $today)->orderBy('time_slot_id')->orderBy('id')->get();

        return view('admin.dashboard', compact('todayCount', 'pendingCount', 'depositTotal', 'reservations'));
    }
}
