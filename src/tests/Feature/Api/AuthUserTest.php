<?php

namespace Tests\Feature\Api;

use App\Services\UserService;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthUserTest extends TestCase
{
    const ROUTE_SHOW = 'api.users.show';

    const ROUTE_LOGIN = 'api.users.login';

    private UserService $userService;

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = app(UserService::class);
    }

    // não deve ter acesso aos detalhes de seu usuário sem autenticar
    /** @test */
    public function should_not_have_access_to_your_user_details_without_authenticating()
    {
        $user = (new UserFactory)->make();
        $newUser = $this->userService->createUser($user->name, $user->email, $user->password_uncrypted);

        $response = $this->getJson(route(self::ROUTE_SHOW, $newUser->id));
        $response->assertUnauthorized();
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }

    // deve ser capaz de fazer login via Api
    /** @test */
    public function must_be_able_to_login_via_Api()
    {
        $user = (new UserFactory)->make();
        $this->userService->createUser($user->name, $user->email, $user->password_uncrypted);

        $response = $this->postJson(route(self::ROUTE_LOGIN), [
            'email' => $user->email,
            'password' => $user->password_uncrypted
        ]);

        $response->assertOk();
        $response->assertJson([
            'token' => true
        ]);
    }

    // deve ser capaz de retornar detalhes de seu usuário estando autenticado
    /** @test */
    public function must_be_able_to_return_your_user_details_while_being_authenticated()
    {
        $user = (new UserFactory)->make();
        $newUser = $this->userService->createUser($user->name, $user->email, $user->password_uncrypted);

        Sanctum::actingAs($newUser, ['*']);

        $response = $this->getJson(route(self::ROUTE_SHOW, $newUser->id));

        $response->assertOk();

        $response->assertJson([
            'id' => $newUser->id,
            'name' => $newUser->name,
            'email' => $newUser->email,
        ]);
    }
}
