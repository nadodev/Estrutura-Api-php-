<?php

declare(strict_types=1);

namespace App\Modules\Health\Presentation\Http\Controllers;

use App\Shared\Http\JsonResponse;
use App\Shared\Http\Request;

final class HealthController
{
    public function show(Request $request, array $params = []): JsonResponse
    {
        return new JsonResponse([
            'app' => $_ENV['APP_NAME'] ?? 'PHP API',
            'status' => 'ok',
            'time' => date(DATE_ATOM),
        ]);
    }
}