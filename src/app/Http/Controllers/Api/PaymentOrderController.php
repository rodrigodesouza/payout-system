<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentOrderRequest;
use App\Http\Resources\PaymentOrderCreated;
use App\Services\PaymentOrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PaymentOrderController extends Controller
{
    private PaymentOrderService $paymentOrderService;

    public function __construct(PaymentOrderService $paymentOrderService)
    {
        $this->paymentOrderService = $paymentOrderService;
    }

    public function store(PaymentOrderRequest $paymentOrderRequest)
    {
        DB::beginTransaction();

        try {
            $input = $paymentOrderRequest->validated();

            $paymentOrder = $this->paymentOrderService->createPaymentOrder(
                invoice: $input['invoice'],
                beneficiary_name: $input['beneficiary_name'],
                code_bank: $input['code_bank'],
                number_agency: $input['number_agency'],
                number_account: $input['number_account'],
                value: $input['value'],
            );

            if (!isset($paymentOrder->id)) {
                DB::rollBack();
                return response()->json(['message' => 'Payment not created'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            DB::commit();

            return response()->json(new PaymentOrderCreated($paymentOrder), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // throw $e;
            return response()->json(['message' => 'Error! Payment not created'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
