<?php

namespace App\Controllers\V1;

use App\Models\Admin;
use App\Requests\Admin\LoginForm;
use App\Requests\Admin\RegisterForm;
use App\Services\AuthService;
use Core\Basic\Messages;
use Core\Http\Controller;
use Core\Http\Responses\JsonResponse;

class AuthController extends Controller
{
    public function login(AuthService $auth, LoginForm $form): JsonResponse
    {
        $messages = $form->handle($this->request)->validate();
        if (count($messages) > 0) {
            return $this->json([
                'validations' => $messages
            ], 400);
        }

        /**
         * @var Admin[] $admins
         */
        $admins = $this->connection->find(Admin::class, $form->filter());
        if (count($admins) > 0) {
            return $this->json([
                '_token' => $auth->setConnection($this->connection)->authenticate($admins[0]),
            ]);
        }

        return $this->json([
            'error' => [
                'badCredentials' => Messages::get('errors', 'badCredentials')
            ],
        ], 404);
    }

    public function register(AuthService $auth, RegisterForm $form): JsonResponse
    {
        $messages = $form->handle($this->request)->validate();
        if (count($messages) > 0) {
            return $this->json([
                'validations' => $messages
            ], 400);
        }

        /**
         * @var Admin $admin
         */
        $admin = $this->connection->save(Admin::class, $form->filter());

        return $this->json([
            '_token' => $auth->setConnection($this->connection)->authenticate($admin),
        ]);
    }
}
