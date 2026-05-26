<?php

namespace App\Repositories\Interfaces;

interface ItemRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCode($code);
    public function getStockHistory($id);
    public function getLowStock();
    public function getNearExpiry(int $days = 30);
    public function onlyTrashed();
    public function restore($id);
    public function forceDelete($id);
}
