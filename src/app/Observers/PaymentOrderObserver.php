<?php

namespace App\Observers;

use App\Events\PaymentOrderCreatedEvent;
use App\Jobs\ConsultPaymentOrderJob;
use App\Models\PaymentOrder;
use App\Services\PaymentOrderService;

class PaymentOrderObserver
{
    private PaymentOrderService $paymentOrderService;

    public function __construct()
    {
        $this->paymentOrderService = new PaymentOrderService;
    }
    /**
     * Handle the PaymentOrder "created" event.
     *
     * @param  \App\Models\PaymentOrder  $paymentOrder
     * @return void
     */
    public function created(PaymentOrder $paymentOrder)
    {
        // Através do evento, podemos iniciar o Job, enviar um e-mail e etc.
        event(new PaymentOrderCreatedEvent($paymentOrder));
    }

    /**
     * Handle the PaymentOrder "updated" event.
     *
     * @param  \App\Models\PaymentOrder  $paymentOrder
     * @return void
     */
    public function updated(PaymentOrder $paymentOrder)
    {
        if ($this->paymentOrderService->isProcessing($paymentOrder)) {
            $this->paymentOrderService->processPayment($paymentOrder);
        }

        if ($this->paymentOrderService->isProcessed($paymentOrder)) {
            ConsultPaymentOrderJob::dispatch($paymentOrder)->delay(now()->addMinutes(2));
        }
    }

    /**
     * Handle the PaymentOrder "deleted" event.
     *
     * @param  \App\Models\PaymentOrder  $paymentOrder
     * @return void
     */
    public function deleted(PaymentOrder $paymentOrder)
    {
        //
    }

    /**
     * Handle the PaymentOrder "restored" event.
     *
     * @param  \App\Models\PaymentOrder  $paymentOrder
     * @return void
     */
    public function restored(PaymentOrder $paymentOrder)
    {
        //
    }

    /**
     * Handle the PaymentOrder "force deleted" event.
     *
     * @param  \App\Models\PaymentOrder  $paymentOrder
     * @return void
     */
    public function forceDeleted(PaymentOrder $paymentOrder)
    {
        //
    }
}
