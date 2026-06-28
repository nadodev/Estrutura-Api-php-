<?php

declare(strict_types=1);

use App\Shared\Exceptions\ErrorHandler;
use App\Shared\Http\Cors;
use App\Shared\Http\JsonResponse;
use App\Shared\Http\Request;
use FastRoute\Dispatcher;

use function FastRoute\simpleDispatcher;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$container = require dirname(__DIR__) . '/bootstrap/container.php';

Cors::handle();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    JsonResponse::send(null, 204);
    exit;
}

$routes = require dirname(__DIR__) . '/routes/api.php';

$dispatcher = simpleDispatcher($routes);

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

try {
    switch ($routeInfo[0]) {
        case Dispatcher::NOT_FOUND:
            JsonResponse::send([
                'message' => 'Rota não encontrada.',
            ], 404);
            break;

        case Dispatcher::METHOD_NOT_ALLOWED:
            JsonResponse::send([
                'message' => 'Método não permitido.',
            ], 405);
            break;

        case Dispatcher::FOUND:
            [$controllerClass, $method] = $routeInfo[1];

            $routeParams = $routeInfo[2];

            $request = Request::capture();
            $request->setRouteParams($routeParams);

            $controller = $container->make($controllerClass);

            $response = $controller->{$method}($request);

            if ($response instanceof JsonResponse) {
                $response->emit();
                break;
            }

            JsonResponse::send($response);
            break;
    }
} catch (Throwable $exception) {
    ErrorHandler::handle($exception);
}