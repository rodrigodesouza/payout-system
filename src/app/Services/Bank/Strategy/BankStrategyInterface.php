<?php

namespace App\Services\Bank\Strategy;

use App\Models\PaymentOrder;

interface BankStrategyInterface {

    public function __construct();

    public function registerPayment(PaymentOrder $paymentOrder);

    public function consultPayment(PaymentOrder $paymentOrder);
}
