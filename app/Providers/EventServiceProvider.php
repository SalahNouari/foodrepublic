<?php

namespace App\Providers;

use App\Events\AdminNotification;
use App\Events\DeliveryManNotification;
use App\Events\deliveryNotification;
use App\Events\vendorOrderNotification;
use App\Events\OrderAcceptedDeliveryEvent;
use App\Events\userOrderNotification;
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
        AdminNotification::class => [
            NotificationListener::class,
        ],
        vendorOrderNotification::class => [
            NotificationListener::class,
        ],
        deliveryNotification::class => [
            NotificationListener::class,
        ],
        DeliveryManNotification::class => [
            NotificationListener::class,
        ],
        OrderAcceptedDeliveryEvent::class => [
            NotificationListener::class,
        ],
        userOrderNotification::class => [
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
