<?php

namespace App\Repositories;

use App\Models\Inventory;

class InventoryRepository extends BaseRepository implements InventoryRepositoryInterface
{
    public function __construct(Inventory $model)
    {
        parent::__construct($model);
    }
}
