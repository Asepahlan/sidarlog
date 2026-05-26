<?php

namespace App\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    public function all();
    public function allWithRelations(array $relations);
    public function paginate(int $perPage = 15, array $relations = []);
    public function find($id);
    public function findWithRelations($id, array $relations);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
