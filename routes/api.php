<?php

declare(strict_types=1);

use App\Modules\Health\Presentation\Http\Controllers\HealthController;
use FastRoute\RouteCollector;

return function (RouteCollector $router): void {
    $router->addRoute('GET', '/api/health', [HealthController::class, 'show']);
};