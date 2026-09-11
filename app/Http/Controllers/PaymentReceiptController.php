<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PaymentReceiptController extends Controller
{
    public function __invoke(Payment $payment): View
    {
        Gate::authorize('view', $payment);
        $payment->load(['reservation.user', 'reservation.timeSlot', 'processor', 'refunder']);

        return view('payments.receipt', compact('payment'));
    }
}
