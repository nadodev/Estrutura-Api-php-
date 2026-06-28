<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Modules\Health\Presentation\Http\Controllers\HealthController;
use App\Shared\Http\Request;
use FastRoute\Dispatcher;
use PHPUnit\Framework\TestCase;
use function FastRoute\simpleDispatcher;

final class HealthRouteTest extends TestCase
{
    public function testHealthRouteIsRegistered(): void
    {
        $dispatcher = simpleDispatcher(require dirname(__DIR__, 2) . '/routes/api.php');

        $routeInfo = $dispatcher->dispatch('GET', '/api/health');

        $this->assertSame(Dispatcher::FOUND, $routeInfo[0]);
        $this->assertSame(HealthController::class, $routeInfo[1][0]);
        $this->assertSame('show', $routeInfo[1][1]);
    }

    public function testHealthRouteReturnsOk(): void
    {
        $request = new Request([], [], ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/api/health'], []);
        $response = (new HealthController())->show($request);

        ob_start();
        $response->emit();
        $payload = ob_get_clean();

        $this->assertJson($payload);

        $json = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

        $this->assertTrue($json['success']);
        $this->assertSame('ok', $json['data']['status']);
        $this->assertArrayHasKey('app', $json['data']);
    }
}
