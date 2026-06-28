<?php

declare(strict_types=1);

namespace App\Modules\Users\Infrastructure\Persistence\Models;

use App\Shared\Database\Model;

final class User extends Model
{
    protected string $table = 'users';

    protected string $primaryKey = 'id';

    protected array $fillable = [
        'name',
        'email',
        'password',
    ];
}