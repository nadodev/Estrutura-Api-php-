<?php

declare(strict_types=1);

namespace App\Modules\Users\Presentation\Http\Controllers;

use App\Modules\Users\Infrastructure\Persistence\Models\User;
use App\Shared\Http\Request;

final class UserController
{
    public function show(Request $request): array
    {
        $id = (int) $request->route('id');

        $user = User::find($id);

        if (! $user) {
            return [
                'message' => 'Usuário não encontrado.',
            ];
        }

        return [
            'message' => 'Usuário encontrado.',
            'user' => $user->toArray(),
        ];
    }

    public function index(Request $request): array
    {
        $users = User::all();

        return [
            'users' => array_map(
                fn (User $user) => $user->toArray(),
                $users
            ),
        ];
    }

    public function store(Request $request): array
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => password_hash($request->input('password'), PASSWORD_DEFAULT),
        ]);

        return [
            'message' => 'Usuário criado com sucesso.',
            'user' => $user->toArray(),
        ];
    }

    public function destroy(Request $request): array
    {
        $id = (int) $request->route('id');

        $user = User::find($id);

        if (! $user) {
            return [
                'message' => 'Usuário não encontrado.',
            ];
        }

        $user->delete();

        return [
            'message' => 'Usuário excluído com sucesso.',
        ];
    }

    public function update(Request $request): array
    {
        $id = (int) $request->route('id');

        $user = User::find($id);

        if (! $user) {
            return [
                'message' => 'Usuário não encontrado.',
            ];
        }

        $user->update([
            'name' => $request->input('name', $user->name),
            'email' => $request->input('email', $user->email),
            'password' => password_hash($request->input('password', ''), PASSWORD_DEFAULT),
        ]);

        return [
            'message' => 'Usuário atualizado com sucesso.',
            'user' => $user->toArray(),
        ];
    }
}