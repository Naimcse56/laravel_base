<?php

namespace App\Repository\Eloquents;

use App\Repository\Interfaces\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements EloquentRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array|string[] $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * @param array $condition
     * @param array $relations
     * @param array|string[] $columns
     * @return Collection
     */

    public function getByCondition(array $condition, array $relations = [], array $columns = ['*']): Collection
    {
        return $this->model->where($condition)->with($relations)->get($columns);
    }

    /**
     * @param int $modelId
     * @param array|string[] $columns
     * @param array $relations
     * @param array $appends
     * @return Model|null
     */
    public function findById(int $modelId, array $columns = ['*'], array $relations = [], array $appends = []): ?Model
    {
        return $this->model->select($columns)->with($relations)->findOrFail($modelId)->append($appends);
    }

    /**
     * @param array $payload
     * @return Model|null
     */
    public function create(array $payload): ?Model
    {
        $model = $this->model->create($payload);
        return $model->fresh();
    }

    /**
     * @param int $modelId
     * @param array $payload
     * @return bool
     */
    public function update(int $modelId, array $payload): bool
    {
        $model = $this->findById($modelId);
        return $model->update($payload);
    }

    /**
     * @param int $modelId
     * @return bool
     */
    public function deleteById(int $modelId, array $relations = []): bool
    {
        $model = $this->findById($modelId);
        if (!$model) {
            return false;
        }
        foreach ($relations as $relation) {
            $this->deleteRelatedModels($model, $relation);
        }
         // Finally, delete the main model
        return $model->delete();
    }

    // Custom Function Start
    public function firstData( array $relations = [], array $conditions = [] , array $columns = ['*'])
    {
        return $this->model->with($relations)->where($conditions)->select($columns)->first();
    }

    public function getByConditionOrder(array $conditions= [], array $relations = [], string $order= null )
    {
        return $this->model->where($conditions)->with($relations)->orderBy($order, 'asc')->get();
    }

    /**
     * Delete related models for a given model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $relation The relationship to delete.
     * @return void
     */
    protected function deleteRelatedModels(Model $model, string $relation): void
    {
        if (!method_exists($model, $relation)) {
            return;
        }

        $relatedQuery = $model->{$relation}();

        // If it's a 'hasMany', 'hasOne', or 'belongsToMany', delete related models
        if ($relatedQuery instanceof \Illuminate\Database\Eloquent\Relations\HasMany) {
            $relatedQuery->delete();  // Delete all related records
        } elseif ($relatedQuery instanceof \Illuminate\Database\Eloquent\Relations\HasOne) {
            $relatedQuery->delete();  // Delete the related record
        } elseif ($relatedQuery instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
            $model->{$relation}()->detach();  // Detach many-to-many relationships (doesn't delete pivot data)
        } elseif ($relatedQuery instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo) {
            // For a belongsTo relationship, delete the related model
            $relatedModel = $model->{$relation};
            if ($relatedModel) {
                $relatedModel->delete();
            }
        }
    }
}
