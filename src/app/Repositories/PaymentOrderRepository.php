<?php

namespace App\Repositories;

use App\Models\PaymentOrder;
use App\Repositories\BaseRepository;
use App\Repositories\Contract\PaymentOrderInterface;

class PaymentOrderRepository extends BaseRepository implements PaymentOrderInterface
{
    public function __construct(PaymentOrder $paymentOrder)
    {
        parent::__construct($paymentOrder);
    }

    public function checkUniqueInvoiceClient($invoice)
    {
        $testInvoice = $this->where('invoice', $invoice)->where('user_id', auth()->id())->count();
        return $testInvoice == 0 ? true : false;
    }
}
