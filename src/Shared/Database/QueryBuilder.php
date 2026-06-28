<?php

declare(strict_types=1);

namespace App\Shared\Database;

use PDO;

final class QueryBuilder
{
    private string $table;

    private string $primaryKey;

    private array $wheres = [];

    private array $bindings = [];

    private ?int $limit = null;

    public function __construct(
        private readonly string $modelClass
    ) {
        /** @var Model $model */
        $model = new $this->modelClass();

        $this->table = $model->getTable();
        $this->primaryKey = $model->getPrimaryKey();
    }

    public function where(string $column, mixed $operatorOrValue, mixed $value = null): self
    {
        $this->assertSafeIdentifier($column);

        if ($value === null) {
            $operator = '=';
            $value = $operatorOrValue;
        } else {
            $operator = $operatorOrValue;
        }

        $allowedOperators = ['=', '!=', '<>', '>', '<', '>=', '<=', 'LIKE'];

        if (! in_array($operator, $allowedOperators, true)) {
            throw new \InvalidArgumentException("Operador inválido: {$operator}");
        }

        $placeholder = ':where_' . count($this->bindings);

        $this->wheres[] = "{$column} {$operator} {$placeholder}";
        $this->bindings[$placeholder] = $value;

        return $this;
    }

    public function find(int|string $id): ?Model
    {
        return $this
            ->where($this->primaryKey, $id)
            ->first();
    }

    public function first(): ?Model
    {
        $this->limit = 1;

        $results = $this->get();

        return $results[0] ?? null;
    }

    public function get(): array
    {
        $sql = "SELECT * FROM {$this->table}";

        if (! empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        $statement = Connection::pdo()->prepare($sql);

        foreach ($this->bindings as $placeholder => $value) {
            $statement->bindValue($placeholder, $value);
        }

        $statement->execute();

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn (array $row) => new $this->modelClass($row),
            $rows
        );
    }

    private function assertSafeIdentifier(string $identifier): void
    {
        if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
            throw new \InvalidArgumentException("Identificador inválido: {$identifier}");
        }
    }
}