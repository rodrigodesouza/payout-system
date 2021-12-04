<?php

namespace App\Rules;

use App\Repositories\Contract\PaymentOrderInterface;
use Illuminate\Contracts\Validation\Rule;

class ClientInvoiceUnique implements Rule
{
    private PaymentOrderInterface $paymentorderInterface;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->paymentorderInterface = app(PaymentOrderInterface::class);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $invoice)
    {
        return $this->paymentorderInterface->checkUniqueInvoiceClient($invoice);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
