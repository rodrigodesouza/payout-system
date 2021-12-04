<?php

namespace App\Services;

use App\Repositories\Contract\PaymentOrderInterface;

class PaymentOrderService extends BaseService
{
    public function __construct(PaymentOrderInterface $paymentOrderInterface)
    {
        parent::__construct($paymentOrderInterface);
    }

    public function createPaymentOrder(
        string $invoice,
        string $beneficiary_name,
        string $code_bank,
        string $number_agency,
        string $number_account,
        float $value
    ) {

        $payment = $this->repository->create([
            'invoice' => $invoice,
            'beneficiary_name' => $beneficiary_name,
            'code_bank' => $code_bank,
            'number_agency' => $number_agency,
            'number_account' => $number_account,
            'value' => $value,
            'user_id' => auth()->id()
        ]);

        return $this->repository->find($payment->id);
    }
}
