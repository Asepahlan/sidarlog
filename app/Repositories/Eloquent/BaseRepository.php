<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Fetch all records (no relations).
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Fetch all records with eager-loaded relations.
     */
    public function allWithRelations(array $relations)
    {
        return $this->model->with($relations)->get();
    }

    /**
     * Fetch paginated records, optionally with eager-loaded relations.
     */
    public function paginate(int $perPage = 15, array $relations = [])
    {
        $query = $this->model->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Find a single record by ID.
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Find a single record by ID with eager-loaded relations.
     */
    public function findWithRelations($id, array $relations)
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    /**
     * Create a new record.
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID.
     */
    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    /**
     * Soft-delete a record by ID.
     */
    public function delete($id)
    {
        $record = $this->find($id);
        return $record->delete();
    }
}
