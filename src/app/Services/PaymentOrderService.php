<?php

namespace App\Services;

use App\Models\PaymentOrder;
use App\Repositories\Contract\PaymentOrderInterface;
use App\Services\Bank\BankOne;
use App\Services\Bank\BankTwo;
use App\Services\Bank\Strategy\BankContext;

class PaymentOrderService extends BaseService
{
    public function __construct()
    {
        $paymentOrderInterface = app(PaymentOrderInterface::class);
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

        $paymentOrder = $this->repository->create([
            'invoice' => $invoice,
            'beneficiary_name' => $beneficiary_name,
            'code_bank' => $code_bank,
            'number_agency' => $number_agency,
            'number_account' => $number_account,
            'value' => $value,
            'user_id' => auth()->id()
        ]);

        return $this->repository->find($paymentOrder->id);
    }

    public function allOrders()
    {
        $paymentOrders = $this->repository->newQuery()->byUser()->get();

        return $paymentOrders;
    }

    /**
     * @description encontra um pagamento pelo seu 'id' ou 'invoice' e consulta seu status caso esteja dentro do período.
     *
     * @param mixed $idOrInvoice
     *
     * @return PaymentOrder|null
     */
    public function findPayment($idOrInvoice)
    {
        try {
            $paymentOrder = $this->repository->newQuery()
                ->byUser()->where(function ($query) use ($idOrInvoice) {
                $query->where('invoice', $idOrInvoice)
                    ->orWhere('id', $idOrInvoice);
            })->first();

            if (!$paymentOrder) {
                return null;
            }

            $paymentOrder = $this->checkStatus($paymentOrder);

            return $paymentOrder;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function checkStatus($paymentOrder)
    {
        // se o status estiver em processado e já tiver passado 1 minuto, faz a consulta no banco de origem e
        // atualiza seu status para PAGO ou REJEITADO
        if (
            isset($paymentOrder->id)
            && $paymentOrder->created_at->diffInMinutes(now()) >= 1
            && $paymentOrder->status == $this->repository::PAYMENT_PROCESSED
        ) {
            $bank = $this->getBank($paymentOrder);
            $paymentOrder = (new BankContext($bank))->consultPayment($paymentOrder);
        }

        return $paymentOrder;
    }

    /**
     * @description atualiza o status do pagamento para PROCESSANDO
     * @param PaymentOrder $paymentOrder
     */
    public function toProcessing(PaymentOrder $paymentOrder): void
    {
        $paymentOrder->update(['status' => $this->repository::PAYMENT_PROCESSING]);
    }

    /**
     * @description verifica se o status do pagamento é PROCESSADO
     * @param PaymentOrder $paymentOrder
     */
    public function isProcessed(PaymentOrder $paymentOrder): bool
    {
        return $paymentOrder->status == $this->repository::PAYMENT_PROCESSED;
    }

    /**
     * @description verifica se o status do pagamento é PROCESSANDO
     * @param PaymentOrder $paymentOrder
     */
    public function isProcessing(PaymentOrder $paymentOrder): bool
    {
        return $paymentOrder->status == $this->repository::PAYMENT_PROCESSING;
    }

    /**
     * @description processa o pagamento pelo banco selecionado.
     * @param PaymentOrder $paymentOrder
     */
    public function processPayment(PaymentOrder $paymentOrder): bool
    {
        $bank = $this->getBank($paymentOrder);

        return (new BankContext($bank))->registerPayment($paymentOrder);
    }

    /**
     * @description retorna uma classe de Banco selecionado por par ou ímpar.
     *
     * @param PaymentOrder $paymentOrder
     *
     * @return BankOne|BankTwo
     */
    private function getBank(PaymentOrder $paymentOrder)
    {
        if ($paymentOrder->id % 2 != 0) {
            // é ímpar";
            $bank = new BankOne;
        } else {
            // é par";
            $bank = new BankTwo;
        }

        return $bank;
    }
}
