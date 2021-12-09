<?php

namespace App\Services\Bank\Strategy;

use App\Models\PaymentOrder;
use App\Services\Bank\Strategy\BankStrategyInterface;

class BankContext
{
    private BankStrategyInterface $bankInterface;

    /**
     * @param BankStrategyInterface $bankInterface
     */
    public function __construct(BankStrategyInterface $bankInterface)
    {
        $this->bankInterface = $bankInterface;
    }

    /**
     * @param PaymentOrder $paymentOrder
     */
    public function registerPayment(PaymentOrder $paymentOrder): bool
    {
        return $this->bankInterface->registerPayment($paymentOrder);
    }

    public function consultPayment(PaymentOrder $paymentOrder)
    {
        return $this->bankInterface->consultPayment($paymentOrder);
    }
}
