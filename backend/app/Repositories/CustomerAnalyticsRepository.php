<?php

namespace App\Repositories;

use App\Models\CustomerAnalytics;

class CustomerAnalyticsRepository extends BaseRepository implements CustomerAnalyticsRepositoryInterface
{
    public function __construct(CustomerAnalytics $model)
    {
        parent::__construct($model);
    }
}
