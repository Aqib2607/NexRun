<?php

namespace App\Repositories;

use App\Models\ProductView;

class ProductViewRepository extends BaseRepository implements ProductViewRepositoryInterface
{
    public function __construct(ProductView $model)
    {
        parent::__construct($model);
    }
}
