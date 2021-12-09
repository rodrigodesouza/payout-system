<?php

namespace App\Jobs;

use App\Services\PaymentOrderService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * @description Executa uma consulta para verificar o status do pagamento em 2 minutos após a criação
 */
class ConsultPaymentOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $paymentOrder;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($paymentOrder)
    {
        $this->paymentOrder = $paymentOrder;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new PaymentOrderService)->findPayment($this->paymentOrder->id);
    }

    /**
     * @param Exception $exception
     */
    public function failed(Exception $exception): void
    {
        Log::info($exception);
    }
}
