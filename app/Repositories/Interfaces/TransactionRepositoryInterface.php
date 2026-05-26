<?php

namespace App\Repositories\Interfaces;

interface TransactionRepositoryInterface extends BaseRepositoryInterface
{
    public function getRecent($limit = 10);
    public function generateReference($type);
}
