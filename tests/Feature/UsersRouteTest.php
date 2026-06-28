<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Modules\Users\Infrastructure\Persistence\Models\User;
use App\Modules\Users\Presentation\Http\Controllers\UserController;
use App\Shared\Database\Connection;
use App\Shared\Http\Request;
use FastRoute\Dispatcher;
use PHPUnit\Framework\TestCase;
use function FastRoute\simpleDispatcher;

final class UsersRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = ':memory:';
        Connection::reset();

        $pdo = Connection::pdo();
        $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    password TEXT NOT NULL
)
SQL
        );
    }

    protected function tearDown(): void
    {
        Connection::reset();

        parent::tearDown();
    }

    public function testUsersRoutesAreRegistered(): void
    {
        $dispatcher = simpleDispatcher(require dirname(__DIR__, 2) . '/routes/api.php');

        $this->assertSame(Dispatcher::FOUND, $dispatcher->dispatch('GET', '/api/users')[0]);
        $this->assertSame(Dispatcher::FOUND, $dispatcher->dispatch('GET', '/api/users/1')[0]);
        $this->assertSame(Dispatcher::FOUND, $dispatcher->dispatch('POST', '/api/users')[0]);
        $this->assertSame(Dispatcher::FOUND, $dispatcher->dispatch('PUT', '/api/users/1')[0]);
        $this->assertSame(Dispatcher::FOUND, $dispatcher->dispatch('DELETE', '/api/users/1')[0]);
    }

    public function testUserCrudWorkflow(): void
    {
        $controller = new UserController();

        $storeRequest = new Request([], ['name' => 'Test User', 'email' => 'test@example.com', 'password' => 'secret'], [], []);
        $storeResponse = $controller->store($storeRequest);

        $this->assertSame('Usuário criado com sucesso.', $storeResponse['message']);
        $this->assertArrayHasKey('user', $storeResponse);
        $this->assertSame('Test User', $storeResponse['user']['name']);
        $this->assertSame('test@example.com', $storeResponse['user']['email']);

        $userId = (int) $storeResponse['user']['id'];

        $indexResponse = $controller->index(new Request([], [], [], []));

        $this->assertCount(1, $indexResponse['users']);
        $this->assertSame($userId, $indexResponse['users'][0]['id']);

        $showRequest = new Request([], [], [], []);
        $showRequest->setRouteParams(['id' => $userId]);
        $showResponse = $controller->show($showRequest);

        $this->assertSame('Usuário encontrado.', $showResponse['message']);
        $this->assertSame($userId, $showResponse['user']['id']);

        $updateRequest = new Request([], ['name' => 'Updated User', 'email' => 'updated@example.com', 'password' => 'newsecret'], [], []);
        $updateRequest->setRouteParams(['id' => $userId]);
        $updateResponse = $controller->update($updateRequest);

        $this->assertSame('Usuário atualizado com sucesso.', $updateResponse['message']);
        $this->assertSame('Updated User', $updateResponse['user']['name']);
        $this->assertSame('updated@example.com', $updateResponse['user']['email']);

        $deleteRequest = new Request([], [], [], []);
        $deleteRequest->setRouteParams(['id' => $userId]);
        $deleteResponse = $controller->destroy($deleteRequest);

        $this->assertSame('Usuário excluído com sucesso.', $deleteResponse['message']);

        $notFoundRequest = new Request([], [], [], []);
        $notFoundRequest->setRouteParams(['id' => $userId]);
        $notFoundResponse = $controller->show($notFoundRequest);

        $this->assertSame('Usuário não encontrado.', $notFoundResponse['message']);
    }
}
