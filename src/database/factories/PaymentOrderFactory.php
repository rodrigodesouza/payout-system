<?php

namespace Database\Factories;

use App\Http\Requests\PaymentOrderRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentOrderFactory extends Factory
{
    private PaymentOrderRequest $paymentOrderRequest;

    private $user_id;

    public function __construct()
    {
        parent::__construct();
        $this->paymentOrderRequest = new PaymentOrderRequest();
    }

    public function setUserId(int $user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'invoice' => $this->faker->uuid(),
            'user_id' => $this->user_id,
            'beneficiary_name' => $this->faker->name,
            'code_bank' => $this->faker->randomNumber($this->paymentOrderRequest::MAX_CODE_BANK, true),
            'number_agency' => $this->faker->randomNumber($this->paymentOrderRequest::MAX_NUMBER_AGENCY, true),
            'number_account' => $this->faker->ean13(),
            'value' => $this->faker->randomFloat(2, $this->paymentOrderRequest::MIN_VALUE, $this->paymentOrderRequest::MAX_VALUE),
            'status' => null,
            'processor_bank_id' => null,
        ];
    }
}
