<?php

namespace Tests\Feature\Api;

use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    private UserRequest $userRequest;

    private UserService $userService;

    /**
     * Rota de salvamento
     */
    const ROUTE_STORE = 'api.users.store';

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRequest = new UserRequest();
        $this->userService = app(UserService::class);
    }

    // tenta cadastrar um usuário sem preencher nome
    /** @test */
    public function try_to_register_a_user_without_filling_in_name()
    {
        $user = (new UserFactory)->make();

        $response = $this->postJson(route(self::ROUTE_STORE), [
            'email' => $user->email,
            'password' => $user->password_uncrypted
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => true,
            'errors' => [
                'name' => [
                    trans('validation.required', ['attribute' => trans('validation.attributes.name')])
                ]
            ]
        ]);
    }

    // tenta cadastrar um usuário enviando nome menor que o permitido
    /** @test */
    public function try_to_register_a_user_by_sending_a_smaller_name_than_allowed()
    {
        $user = (new UserFactory)->make();
        $userName = Str::random($this->userRequest::MIN_NAME_SIZE - 1);

        $response = $this->postJson(route(self::ROUTE_STORE), [
            'name' => $userName,
            'email' => $user->email,
            'password' => $user->password_uncrypted
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => true,
            'errors' => [
                'name' => [
                    trans('validation.min.string', ['attribute' => trans('validation.attributes.name'), 'min' => $this->userRequest::MIN_NAME_SIZE])
                ]
            ]
        ]);
    }

    // tenta cadastrar um usuário enviando nome maior que o permitido
    /** @test */
    public function try_to_register_a_user_by_sending_a_name_longer_than_allowed()
    {
        $user = (new UserFactory)->make();
        $userName = Str::random($this->userRequest::MAX_NAME_SIZE + 1);

        $response = $this->postJson(route(self::ROUTE_STORE), [
            'name' => $userName,
            'email' => $user->email,
            'password' => $user->password_uncrypted
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => true,
            'errors' => [
                'name' => [
                    trans('validation.max.string', ['attribute' => trans('validation.attributes.name'), 'max' => $this->userRequest::MAX_NAME_SIZE])
                ]
            ]
        ]);
    }

    // tenta cadastrar um usuário sem enviar email
    /** @test */
    public function try_to_register_a_user_without_sending_an_email()
    {
        $user = (new UserFactory)->make();

        $response = $this->postJson(route(self::ROUTE_STORE), [
            'name' => $user->name,
            'email' => null,
            'password' => $user->password_uncrypted
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => true,
            'errors' => [
                'email' => [
                    trans('validation.required', ['attribute' => trans('validation.attributes.email')])
                ]
            ]
        ]);
    }

    // tenta cadastrar um usuário enviando e-mail inválido
    /** @test */
    public function tries_to_register_a_user_by_sending_invalid_email()
    {
        $user = (new UserFactory)->make();

        $response = $this->postJson(route(self::ROUTE_STORE), [
            'name' => $user->name,
            'email' => Str::random(20),
            'password' => $user->password_uncrypted
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => true,
            'errors' => [
                'email' => [
                    trans('validation.email', ['attribute' => trans('validation.attributes.email')])
                ]
            ]
        ]);
    }

    // tenta cadastrar um usuário enviando e-mail já cadastrado
    /** @test */
    public function tries_to_register_a_user_by_sending_an_already_registered_email()
    {
        $otherUser = (new UserFactory)->make();

        $this->userService->createUser($otherUser->name, $otherUser->email, $otherUser->password_uncrypted);

        $user = (new UserFactory)->make();

        $response = $this->postJson(route(self::ROUTE_STORE), [
            'name' => $user->name,
            'email' => $otherUser->email,
            'password' => $user->password_uncrypted
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => true,
            'errors' => [
                'email' => [
                    trans('validation.unique', ['attribute' => trans('validation.attributes.email')])
                ]
            ]
        ]);
    }

    // tenta cadastrar um usuário sem preencher o campo senha
    /** @test */
    public function tries_to_register_a_user_without_filling_in_the_password_field()
    {
        $user = (new UserFactory)->make();

        $response = $this->postJson(route(self::ROUTE_STORE), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => ''
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => true,
            'errors' => [
                'password' => [
                    trans('validation.required', ['attribute' => trans('validation.attributes.password')])
                ]
            ]
        ]);
    }

    // tenta cadastrar um usuário enviando senha menor que o permitido
    /** @test */
    public function tries_to_register_a_user_by_sending_a_password_smaller_than_the_allowed()
    {
        $user = (new UserFactory)->make();

        $password = Str::random($this->userRequest::MIN_PASSWORD_SIZE - 1);
        $response = $this->postJson(route(self::ROUTE_STORE), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => true,
            'errors' => [
                'password' => [
                    trans('validation.min.string', [
                        'attribute' => trans('validation.attributes.password'),
                        'min' => $this->userRequest::MIN_PASSWORD_SIZE
                    ])
                ]
            ]
        ]);
    }

    // deve ser capaz de cadastrar um usuário com sucesso
    /** @test */
    public function must_be_able_to_register_a_user_successfully()
    {
        $user = (new UserFactory)->make();

        $response = $this->postJson(route(self::ROUTE_STORE), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson([
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => true,
            'updated_at' => true
        ]);

        $this->assertDatabaseHas($this->userService->getRepository()->getTable(), [
            'name' => $user->name,
            'email' => $user->email
        ]);
    }
}
