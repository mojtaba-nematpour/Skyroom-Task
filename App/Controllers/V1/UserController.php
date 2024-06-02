<?php

namespace App\Controllers\V1;

use App\Models\Admin;
use App\Models\User;
use App\Requests\Admin\LoginForm;
use App\Requests\Admin\RegisterForm;
use App\Services\AuthService;
use Core\Basic\Messages;
use Core\Http\Controller;
use Core\Http\Responses\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        /**
         * @var User[] $users
         */
        $users = $this->connection->find(User::class);

        return $this->json([
            'data' => $users
        ]);
    }
}
