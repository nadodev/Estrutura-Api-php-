<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use App\Shared\Http\JsonResponse;
use Throwable;

final class ErrorHandler
{
    public static function handle(Throwable $exception): void
    {
        if ($exception instanceof HttpException) {
            JsonResponse::send([
                'message' => $exception->getMessage(),
                'details' => $exception->details(),
            ], $exception->statusCode());

            return;
        }

        $debug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

        JsonResponse::send([
            'message' => 'Erro interno do servidor.',
            'exception' => $debug ? [
                'class' => $exception::class,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ] : null,
        ], 500);
    }
}