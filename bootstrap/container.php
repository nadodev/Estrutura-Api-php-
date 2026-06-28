<?php

declare(strict_types=1);

use App\Modules\Users\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Users\Infrastructure\Persistence\Repositories\PdoUserRepository;
use App\Shared\Container\Container;
use App\Shared\Database\Connection;

$container = new Container();

$container->instance(\PDO::class, Connection::pdo());

$container->bind(
    UserRepositoryInterface::class,
    PdoUserRepository::class
);

return $container;