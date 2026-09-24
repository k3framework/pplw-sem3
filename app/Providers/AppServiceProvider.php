<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\Reservation;
use App\Policies\PaymentPolicy;
use App\Policies\ReservationPolicy;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Carbon::setLocale('id');
        CarbonImmutable::setLocale('id');
        Gate::policy(Reservation::class, ReservationPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Blade::directive('rupiah', fn ($value) => "<?php echo 'Rp'.number_format((float) ($value), fmod((float) ($value), 1) == 0 ? 0 : 2, ',', '.'); ?>");
    }
}
