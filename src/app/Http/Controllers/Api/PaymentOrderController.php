<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentOrderRequest;
use App\Http\Resources\PaymentOrder;
use App\Http\Resources\PaymentOrderCollection;
use App\Http\Resources\PaymentOrderCreated;
use App\Services\PaymentOrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentOrderController extends Controller
{
    private PaymentOrderService $paymentOrderService;

    public function __construct(PaymentOrderService $paymentOrderService)
    {
        $this->paymentOrderService = $paymentOrderService;
    }

    /**
     * Cria um novo pagamento para o usuário autenticado.
     * @param PaymentOrderRequest $paymentOrderRequest
     *
     * @return Response
     */
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
            DB::rollback();

            return response()->json(['message' => 'Error! Payment not created'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retorna os detalhes de um pagamento para o usuário autenticado.
     * @param mixed $id
     */
    public function show($id)
    {
        try {
            $paymentOrder = $this->paymentOrderService->findPayment($id);

            if (!isset($paymentOrder->id)) {
                return response()->json(['message' => 'Payment not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(new PaymentOrder($paymentOrder), Response::HTTP_FOUND);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['message' => 'Error! Payment not returned'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function index()
    {
        try {
            $paymentOrders = $this->paymentOrderService->allOrders();

            return response()->json(new PaymentOrderCollection($paymentOrders), Response::HTTP_FOUND);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['message' => 'Error! Payment not returned'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
