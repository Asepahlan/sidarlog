<?php

namespace App\Repositories\Eloquent;

use App\Models\StockOpname;
use App\Repositories\Interfaces\StockOpnameRepositoryInterface;

class StockOpnameRepository extends BaseRepository implements StockOpnameRepositoryInterface
{
    /**
     * Default relations to eager-load for opname display.
     */
    protected const DEFAULT_RELATIONS = [
        'barang',
        'gudang',
        'pengguna',
    ];

    public function __construct(StockOpname $model)
    {
        parent::__construct($model);
    }

    /**
     * Override: fetch all opnames with eager-loaded relations.
     * Accepts optional $relations array for compatibility with
     * StockOpnameService::getAllOpnames() which passes ['barang','gudang','pengguna'].
     */
    public function all(array $relations = [])
    {
        $relations = empty($relations) ? self::DEFAULT_RELATIONS : $relations;
        return $this->model->with($relations)->latest()->get();
    }

    /**
     * Override: paginated opnames with eager-loaded relations.
     */
    public function paginate(int $perPage = 15, array $relations = [])
    {
        $relations = empty($relations) ? self::DEFAULT_RELATIONS : $relations;
        return $this->model->with($relations)->latest()->paginate($perPage);
    }
}
