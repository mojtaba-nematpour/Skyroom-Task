<?php

namespace App\Controllers\V1;

use App\Models\User;
use App\Requests\UserForm;
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

    public function search(): JsonResponse
    {
        $like = $this->request->attrs(1);
        $users = $this->connection->search(User::class, [
            'id' => $like,
            'firstname' => "$like%",
            'lastname' => "$like%",
            'email' => "$like%",
            'mobile' => "$like%",
        ]);

        if (count($users) > 0) {
            return $this->json([
                'data' => $users
            ]);
        }

        return $this->json([
            'error' => [
                Messages::get('errors', '404')
            ]
        ]);
    }

    public function view(): JsonResponse
    {
        /**
         * @var User[] $users
         */
        $users = $this->connection->find(User::class, [
            'id' => $this->request->attrs(1)
        ]);

        if (count($users) > 0) {
            return $this->json([
                'user' => $users[0]
            ]);
        }

        return $this->json([
            'errors' => [
                Messages::get('errors', '404')
            ]
        ], 404);
    }

    public function new(UserForm $form): JsonResponse
    {
        $messages = $form->handle($this->request)->validate();
        if (count($messages) > 0) {
            return $this->json([
                'validations' => $messages
            ], 400);
        }

        /**
         * @var User $user
         */
        $user = $this->connection->save(User::class, $form->filter());

        return $this->json([
            'user' => $user->getId(),
            'messages' => [
                sprintf(Messages::get('messages', 'created'), Messages::get('form', 'user'))
            ]
        ], 201);
    }

    public function edit(UserForm $form): JsonResponse
    {
        $messages = $form->handle($this->request)->validate();
        if (count($messages) > 0) {
            return $this->json([
                'validations' => $messages
            ], 400);
        }

        /**
         * @var User $user
         */
        $user = $this->connection->update(User::class, [
            'id' => $this->request->attrs(1)
        ], $form->filter());

        return $this->json([
            'user' => $user,
            'messages' => [
                sprintf(Messages::get('messages', 'updated'), Messages::get('form', 'user'))
            ]
        ]);
    }

    public function destroy(): JsonResponse
    {
        $this->connection->remove(User::class, [
            'id' => $this->request->attrs(1)
        ]);

        return $this->json([
            'messages' => [
                sprintf(Messages::get('messages', 'removed'), Messages::get('form', 'user'))
            ]
        ]);
    }
}
