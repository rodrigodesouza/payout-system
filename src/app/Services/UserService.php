<?php

namespace App\Services;

use App\Repositories\Contract\UserInterface;

class UserService extends BaseService
{
    public function __construct(UserInterface $userInterface)
    {
        parent::__construct($userInterface);
    }

    /**
     * @description responsável por criar um novo usuário e gerar um token de acesso via Api.
     */
    public function createUser(string $name, string $email, string $password)
    {
        $password = bcrypt($password);

        if ($this->findColumn('email', $email)) {
            throw new \Exception("E-mail já está cadastrado", 1);
        }

        $user = $this->repository->create([
            'name'  => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $user->token = $user->createToken('auth_token')->plainTextToken;

        return $user;
    }

    public function authenticateUser($request)
    {
        $user = $this->where('email', $request->get('email'))->first();
        $user->token = $user->createToken('auth_token')->plainTextToken;

        return $user;
    }
}
