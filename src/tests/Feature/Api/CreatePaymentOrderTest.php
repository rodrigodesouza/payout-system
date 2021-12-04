<?php

namespace Tests\Feature\Api;

use App\Http\Requests\PaymentOrderRequest;
use App\Repositories\Contract\PaymentOrderInterface;
use App\Services\UserService;
use Database\Factories\PaymentOrderFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Faker\Factory;

class CreatePaymentOrderTest extends TestCase
{
    private UserService $userService;

    private PaymentOrderRequest $paymentOrderRequest;

    private PaymentOrderInterface $paymentOrderRepository;

    const ROUTE_STORE = 'api.payment_order.store';

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = app(UserService::class);
        $this->paymentOrderRequest = new PaymentOrderRequest();
        $this->faker = Factory::create();
        $this->paymentOrderRepository = app(PaymentOrderInterface::class);
    }

    /**
     * @description Cria um novo usuário para Api.
     */
    private function makeUser()
    {
        $user = (new UserFactory())->make();
        $newUser = $this->userService->createUser($user->name, $user->email, $user->password_uncrypted);

        return $newUser;
    }

    /**
     * @description Faz autenticação do usuário na Api.
     */
    private function userAuthenticate(): void
    {
        $newUser = $this->makeUser();
        Sanctum::actingAs($newUser, ['*']);
    }

    /**
     * tenta criar pagamento sem estar autenticado
     * @test
     */
    public function try_to_create_payment_without_being_authenticated()
    {
        $response = $this->postJson(route(self::ROUTE_STORE), []);
        $response->assertUnauthorized();
        $response->assertJson(
            [
                "message" => "Unauthenticated."
            ]
        );
    }

    /**
     * tenta criar pagamento sem enviar todos os campos obrigatórios
     * @test
     */
    public function try_to_create_payment_without_sending_all_required_fields()
    {
        $this->userAuthenticate();

        $response = $this->postJson(route(self::ROUTE_STORE), []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'message' => true,
            'errors' => [
                "invoice" =>  [
                    trans('validation.required', ['attribute' => trans('validation.attributes.invoice')])
                ],
                "beneficiary_name" => [
                    trans('validation.required', ['attribute' => trans('validation.attributes.beneficiary_name')])
                ],
                "code_bank" => [
                    trans('validation.required', ['attribute' => trans('validation.attributes.code_bank')])
                ],
                "number_agency" => [
                    trans('validation.required', ['attribute' => trans('validation.attributes.number_agency')])
                ],
                "number_account" => [
                    trans('validation.required', ['attribute' => trans('validation.attributes.number_account')])
                ],
                "value" => [
                    trans('validation.required', ['attribute' => trans('validation.attributes.value')])
                ]
            ]
        ]);
    }

    /**
     * tenta criar pagamento enviando valor menor que o permitido
     * @test
     */
    public function try_to_create_payment_by_sending_lower_value_than_allowed()
    {
        $this->userAuthenticate();

        $data = [
            'value' => $this->paymentOrderRequest::MIN_VALUE / 2
        ];

        $response = $this->postJson(route(self::ROUTE_STORE), $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => true,
            'errors' => [
                "value" => [
                    trans('validation.min.numeric', [
                        'attribute' => trans('validation.attributes.value'),
                        'min' => $this->paymentOrderRequest::MIN_VALUE,
                    ])
                ]
            ]
        ]);
    }

    /**
     * tenta criar pagamento enviando valor maior que o permitido
     * @test
     */
    public function try_to_create_payment_by_sending_value_higher_than_allowed()
    {
        $this->userAuthenticate();

        $data = [
            'value' => $this->paymentOrderRequest::MAX_VALUE + 1
        ];

        $response = $this->postJson(route(self::ROUTE_STORE), $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => true,
            'errors' => [
                "value" => [
                    trans('validation.max.numeric', [
                        'attribute' => trans('validation.attributes.value'),
                        'max' => $this->paymentOrderRequest::MAX_VALUE,
                    ])
                ]
            ]
        ]);
    }

    /**
     * tenta criar pagamento enviando code bank com mais digitos que o permitido
     * @test
     */
    public function try_to_create_payment_by_sending_code_bank_with_more_digits_than_allowed()
    {
        $this->userAuthenticate();

        $data = [
            'code_bank' => $this->faker->randomNumber($this->paymentOrderRequest::MAX_CODE_BANK + 1)
        ];

        $response = $this->postJson(route(self::ROUTE_STORE), $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => true,
            'errors' => [
                "code_bank" => [
                    trans('validation.digits_between', [
                        'attribute' => trans('validation.attributes.code_bank'),
                        'min' => $this->paymentOrderRequest::MIN_CODE_BANK,
                        'max' => $this->paymentOrderRequest::MAX_CODE_BANK,
                    ])
                ]
            ]
        ]);
    }

    /**
     * tenta criar pagamento enviando agencia com mais digitos que o permitido
     * @test
     */
    public function try_to_create_payment_by_sending_agency_with_more_digits_than_allowed()
    {
        $this->userAuthenticate();

        $data = [
            'number_agency' => $this->faker->randomNumber(($this->paymentOrderRequest::MAX_NUMBER_AGENCY + 1), true)
        ];

        $response = $this->postJson(route(self::ROUTE_STORE), $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => true,
            'errors' => [
                "number_agency" => [
                    trans('validation.digits_between', [
                        'attribute' => trans('validation.attributes.number_agency'),
                        'min' => $this->paymentOrderRequest::MIN_NUMBER_AGENCY,
                        'max' => $this->paymentOrderRequest::MAX_NUMBER_AGENCY,
                    ])
                ]
            ]
        ]);
    }

    /**
     * tenta criar pagamento enviando conta com mais digitos que o permitido
     * @test
     */
    public function try_to_create_payment_by_sending_account_with_more_digits_than_allowed()
    {
        $this->userAuthenticate();

        $data = [
            'number_account' => 12345678910111213
        ];

        $response = $this->postJson(route(self::ROUTE_STORE), $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => true,
            'errors' => [
                "number_account" => [
                    trans('validation.digits_between', [
                        'attribute' => trans('validation.attributes.number_account'),
                        'min' => $this->paymentOrderRequest::MIN_NUMBER_ACCOUNT,
                        'max' => $this->paymentOrderRequest::MAX_NUMBER_ACCOUNT,
                    ])
                ]
            ]
        ]);
    }

    /**
     * deve ser capaz de criar um pagamento com sucesso
     * @test
     */
    public function must_be_able_to_create_a_successful_payment()
    {
        $this->userAuthenticate();

        $paymentFaker = (new PaymentOrderFactory)->make();

        $data = $paymentFaker->toArray();

        $response = $this->postJson(route(self::ROUTE_STORE), $data);
        $response->assertCreated();
        $response->assertJson([
            'id' => true,
            'invoice' => $data['invoice'],
            'status' => $this->paymentOrderRepository::PAYMENT_CREATED
        ]);
    }

    /**
     * tenta criar pagamento enviando invoice já cadastrado pelo mesmo cliente
     * @test
     */
    public function try_to_create_payment_by_sending_an_invoice_already_registered_by_the_same_customer()
    {
        $this->userAuthenticate();

        $paymentFaker = (new PaymentOrderFactory)->make();
        $data         = $paymentFaker->toArray();
        $response     = $this->postJson(route(self::ROUTE_STORE), $data);
        $response->assertCreated();

        $newPaymentFaker = (new PaymentOrderFactory)->make();
        $newData            = $newPaymentFaker->toArray();
        $newData['invoice'] = $data['invoice'];

        $response = $this->postJson(route(self::ROUTE_STORE), $newData);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'message' => true,
            'errors' => [
                'invoice' => [
                    trans('validation.custom.invoice.unique_invoice', ['attribute' => trans('validation.attributes.invoice')])
                ]
            ]
        ]);
    }

    /**
     * deve ser capaz de criar pagamento usando um invoice identico ao cadastrado por outro cliente
     * @test
     */
    public function must_be_able_to_create_payment_using_invoice_registered_by_another_customer()
    {
        $this->userAuthenticate();

        $paymentFaker = (new PaymentOrderFactory)->make();
        $data         = $paymentFaker->toArray();
        $response     = $this->postJson(route(self::ROUTE_STORE), $data);
        $response->assertCreated();

        $this->userAuthenticate();
        $newPaymentFaker = (new PaymentOrderFactory)->make();
        $newData            = $newPaymentFaker->toArray();
        $newData['invoice'] = $data['invoice'];

        $response = $this->postJson(route(self::ROUTE_STORE), $newData);
        $response->assertCreated();
        $response->assertJson([
            'id' => true,
            'invoice' => $data['invoice'],
            'status' => $this->paymentOrderRepository::PAYMENT_CREATED
        ]);
    }

    //testar execução de Job, alteração de status, consulta de status

}
