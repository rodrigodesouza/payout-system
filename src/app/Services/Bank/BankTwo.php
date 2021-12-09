<?php

namespace App\Services\Bank;

use App\Models\PaymentOrder;
use App\Repositories\Contract\PaymentOrderInterface;
use App\Services\Bank\Strategy\BankStrategyInterface;

class BankTwo implements BankStrategyInterface
{
    const BANK_ID = 2;

    public function __construct()
    {
        $this->paymentOrderInterface = app(PaymentOrderInterface::class);
    }

    public function registerPayment(PaymentOrder $paymentOrder)
    {
        sleep(4);
        return $paymentOrder->update([
            'processor_bank_id' => self::BANK_ID,
            'status' => $this->paymentOrderInterface::PAYMENT_PROCESSED
        ]);
    }

    /**
     * @description consulta um pagamento e retorna aleatóriamente um status
     */
    public function consultPayment(PaymentOrder $paymentOrder)
    {
        // A mesma regra foi escrita nos 2 bancos, mas aqui poderia ter regras diferentes para cada banco.
        $status = [
            $this->paymentOrderInterface::PAYMENT_PAID,
            $this->paymentOrderInterface::PAYMENT_REJECTED
        ];

        $paymentOrder->status = $status[rand(0, 1)];
        $paymentOrder->save();

        return $paymentOrder;
    }
}
