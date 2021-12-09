<?php

namespace App\Repositories;

use App\Models\PaymentOrder;
use App\Repositories\BaseRepository;
use App\Repositories\Contract\PaymentOrderInterface;

class PaymentOrderRepository extends BaseRepository implements PaymentOrderInterface
{
    public function __construct()
    {
        $paymentOrder = new PaymentOrder;
        parent::__construct($paymentOrder);
    }

    /**
     * @description checa se o invoice já foi cadastrado para o usuário atual.
     */
    public function checkUniqueInvoiceClient($invoice)
    {
        $testInvoice = $this->where('invoice', $invoice)->where('user_id', auth()->id())->count();

        return $testInvoice == 0 ? true : false;
    }
}
