<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []): Collection;

    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = [], array $filters = []): LengthAwarePaginator;

    public function find(int|string $id, array $relations = []): ?Model;

    public function findOrFail(int|string $id, array $relations = []): Model;

    public function create(array $payload): Model;

    public function update(int|string $id, array $payload): Model;

    public function delete(int|string $id): bool;

    public function findBy(array $criteria, array $relations = []): ?Model;

    public function getWhere(array $criteria, array $relations = []): Collection;
}
