<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentOrderRequest extends FormRequest
{
    const MIN_VALUE = 0.01;
    const MAX_VALUE = 100000;
    const MIN_CODE_BANK = 1;
    const MAX_CODE_BANK = 3;
    const MIN_NUMBER_AGENCY = 1;
    const MAX_NUMBER_AGENCY = 4;
    const MIN_NUMBER_ACCOUNT = 1;
    const MAX_NUMBER_ACCOUNT = 15;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'value' => 'required|numeric|min:' . self::MIN_VALUE . '|max:' . self::MAX_VALUE,
            'code_bank' => 'required|digits_between:' . self::MIN_CODE_BANK . ',' . self::MAX_CODE_BANK,
            'number_agency' => 'required|digits_between:' . self::MIN_NUMBER_AGENCY . ',' . self::MAX_NUMBER_AGENCY,
            'number_account' => 'required|digits_between:' . self::MIN_NUMBER_ACCOUNT . ',' . self::MAX_NUMBER_ACCOUNT,
            'beneficiary_name' => 'required|min:2|max:255',
            'invoice' => 'required|unique_invoice',
        ];
    }
}
