<?php

namespace App\Repositories;

use App\Models\ProductAnalytics;

class ProductAnalyticsRepository extends BaseRepository implements ProductAnalyticsRepositoryInterface
{
    public function __construct(ProductAnalytics $model)
    {
        parent::__construct($model);
    }
}
