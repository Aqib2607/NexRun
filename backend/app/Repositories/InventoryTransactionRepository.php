<?php

namespace App\Repositories;

use App\Models\InventoryTransaction;

class InventoryTransactionRepository extends BaseRepository implements InventoryTransactionRepositoryInterface
{
    public function __construct(InventoryTransaction $model)
    {
        parent::__construct($model);
    }
}
