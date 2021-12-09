<?php

namespace App\Providers;

use Illuminate\Queue\Events\JobProcessing;
use App\Repositories\Contract\{
    PaymentOrderInterface,
    UserInterface
};
use App\Repositories\{
    PaymentOrderRepository,
    UserRepository
};
use App\Rules\ClientInvoiceUnique;
use App\Services\PaymentOrderService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->bind(UserInterface::class, UserRepository::class);

        app()->bind(PaymentOrderInterface::class, PaymentOrderRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::extend('unique_invoice', function ($attribute, $value, $parameter, $validator) {
            return (new ClientInvoiceUnique)->passes($attribute, $value);
        });

        Queue::before(function (JobProcessing $event) {
            if ($event->job->resolveName() == "App\Jobs\PaymentOrderJob") {
                $payload = json_decode($event->job->getRawBody());
                $data = unserialize($payload->data->command);

                (new PaymentOrderService)->toProcessing($data->paymentOrder);
            }
        });
    }
}
