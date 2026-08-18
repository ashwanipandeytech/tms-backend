<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = [], array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with($relations);

        if (isset($filters['search']) && !empty($filters['search']) && method_exists($this->model, 'scopeSearch')) {
            $query->search($filters['search']);
        }

        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage, $columns);
    }

    public function find(int|string $id, array $relations = []): ?Model
    {
        return $this->model->with($relations)->find($id);
    }

    public function findOrFail(int|string $id, array $relations = []): Model
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    public function create(array $payload): Model
    {
        return $this->model->create($payload);
    }

    public function update(int|string $id, array $payload): Model
    {
        $record = $this->findOrFail($id);
        $record->update($payload);
        return $record->fresh();
    }

    public function delete(int|string $id): bool
    {
        $record = $this->findOrFail($id);
        return (bool) $record->delete();
    }

    public function findBy(array $criteria, array $relations = []): ?Model
    {
        return $this->model->with($relations)->where($criteria)->first();
    }

    public function getWhere(array $criteria, array $relations = []): Collection
    {
        return $this->model->with($relations)->where($criteria)->get();
    }
}
