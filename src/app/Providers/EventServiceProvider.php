<?php

namespace App\Providers;

use App\Events\PaymentOrderCreatedEvent;
use App\Listeners\PaymentOrderCreatedListen;
use App\Models\PaymentOrder;
use App\Observers\PaymentOrderObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        PaymentOrderCreatedEvent::class => [
            PaymentOrderCreatedListen::class
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        PaymentOrder::observe(PaymentOrderObserver::class);
    }
}
