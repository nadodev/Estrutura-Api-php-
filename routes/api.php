<?php

declare(strict_types=1);

use App\Modules\Health\Presentation\Http\Controllers\HealthController;
use App\Modules\Users\Presentation\Http\Controllers\UserController;
use FastRoute\RouteCollector;

return function (RouteCollector $router): void {
   $router->addRoute('GET', '/api/health', [HealthController::class, 'show']);
    $router->addRoute('GET', '/api/users', [UserController::class, 'index']);
    $router->addRoute('GET', '/api/users/{id}', [UserController::class, 'show']);
    $router->addRoute('POST', '/api/users', [UserController::class, 'store']);
};