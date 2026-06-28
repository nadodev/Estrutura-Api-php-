<?php

declare(strict_types=1);

namespace App\Modules\Users\Presentation\Http\Controllers;

use App\Shared\Http\Request;

final class UserController
{
    
    public function show(Request $request): array
    {
        return [
            'message' => 'Usuário encontrado',
            'id' => $request->route('id'),
        ];
    }
}