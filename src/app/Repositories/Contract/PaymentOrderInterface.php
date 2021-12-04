<?php

namespace App\Repositories\Contract;

interface PaymentOrderInterface extends BaseInterface
{
    const PAYMENT_CREATED = 'created';
    const PAYMENT_PROCESSING = 'processing';
    const PAYMENT_PROCESSED = 'processed';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_REJECTED = 'rejected';

    public function checkUniqueInvoiceClient($invoice);
}
