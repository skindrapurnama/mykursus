<?php

namespace App\Providers;

use App\Models\Payment;
use App\Observers\PaymentObserver;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Payment::observe(PaymentObserver::class);

        Carbon::setLocale('id');
        \Illuminate\Support\Carbon::setLocale('id');
    }
}
