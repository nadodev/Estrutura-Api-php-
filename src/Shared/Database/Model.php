<?php

declare(strict_types=1);

namespace App\Shared\Database;

abstract class Model
{
    protected string $table;

    protected string $primaryKey = 'id';

    protected array $fillable = [];

    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public static function query(): QueryBuilder
    {
        return new QueryBuilder(static::class);
    }

    public static function all(): array
    {
        return static::query()->get();
    }

    public static function find(int|string $id): ?static
    {
        /** @var static|null $model */
        $model = static::query()->find($id);

        return $model;
    }

    public static function create(array $data): static
    {
        $model = new static();

        $data = $model->filterFillable($data);

        if (empty($data)) {
            throw new \InvalidArgumentException('Nenhum dado permitido para inserção.');
        }

        $columns = array_keys($data);
        $placeholders = array_map(fn (string $column) => ':' . $column, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $model->getTable(),
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $statement = Connection::pdo()->prepare($sql);

        foreach ($data as $column => $value) {
            $statement->bindValue(':' . $column, $value);
        }

        $statement->execute();

        $id = Connection::pdo()->lastInsertId();

        return static::find($id) ?? new static([
            ...$data,
            $model->getPrimaryKey() => $id,
        ]);
    }

    public function update(array $data): bool
    {
        $data = $this->filterFillable($data);

        if (empty($data)) {
            return false;
        }

        $id = $this->getKey();

        if ($id === null) {
            throw new \RuntimeException('Não é possível atualizar um model sem ID.');
        }

        $sets = [];

        foreach (array_keys($data) as $column) {
            $sets[] = "{$column} = :{$column}";
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :primary_key',
            $this->getTable(),
            implode(', ', $sets),
            $this->getPrimaryKey()
        );

        $statement = Connection::pdo()->prepare($sql);

        foreach ($data as $column => $value) {
            $statement->bindValue(':' . $column, $value);
        }

        $statement->bindValue(':primary_key', $id);

        $updated = $statement->execute();

        if ($updated) {
            $this->attributes = [
                ...$this->attributes,
                ...$data,
            ];
        }

        return $updated;
    }

    public function delete(): bool
    {
        $id = $this->getKey();

        if ($id === null) {
            throw new \RuntimeException('Não é possível deletar um model sem ID.');
        }

        $sql = sprintf(
            'DELETE FROM %s WHERE %s = :primary_key',
            $this->getTable(),
            $this->getPrimaryKey()
        );

        $statement = Connection::pdo()->prepare($sql);
        $statement->bindValue(':primary_key', $id);

        return $statement->execute();
    }

    public function getKey(): mixed
    {
        return $this->attributes[$this->primaryKey] ?? null;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_filter(
            $data,
            fn (string $key) => in_array($key, $this->fillable, true),
            ARRAY_FILTER_USE_KEY
        );
    }
}