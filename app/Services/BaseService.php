<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

abstract class BaseService
{
    protected BaseRepositoryInterface $repository;

    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->repository->all($columns, $relations);
    }

    public function getPaginated(int $perPage = 15, array $columns = ['*'], array $relations = [], array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $columns, $relations, $filters);
    }

    public function getById(int|string $id, array $relations = []): Model
    {
        return $this->repository->findOrFail($id, $relations);
    }

    /**
     * Create resource wrapped in a DB transaction.
     *
     * @throws Throwable
     */
    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            return $this->repository->create($data);
        });
    }

    /**
     * Update resource wrapped in a DB transaction.
     *
     * @throws Throwable
     */
    public function update(int|string $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->repository->update($id, $data);
        });
    }

    /**
     * Delete resource wrapped in a DB transaction.
     *
     * @throws Throwable
     */
    public function delete(int|string $id): bool
    {
        return DB::transaction(function () use ($id) {
            return $this->repository->delete($id);
        });
    }
}
