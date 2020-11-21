<?php

namespace App\Providers;

use App\Events\NewOrderDeliveryEvent;
use App\Events\NewOrderEvent;
use App\Events\OrderAcceptedDeliveryEvent;
use App\Events\OrderAcceptedEvent;
use App\Listeners\NotificationListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        NewOrderEvent::class => [
            NotificationListener::class,
        ],
        NewOrderDeliveryEvent::class => [
            NotificationListener::class,
        ],
        OrderAcceptedDeliveryEvent::class => [
            NotificationListener::class,
        ],
        OrderAcceptedEvent::class => [
            NotificationListener::class,
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
