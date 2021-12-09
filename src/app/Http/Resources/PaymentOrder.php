<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentOrder extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'invoice' => $this->invoice,
            'status' => $this->status,
            'beneficiary_name' => $this->beneficiary_name,
            'code_bank' => $this->code_bank,
            'number_agency' => $this->number_agency,
            'number_account' => $this->number_account,
            'value' => $this->value,
            'status' => $this->status,
            'processor_bank_id' => $this->processor_bank_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
