<?php

namespace App\Repositories;

use App\Models\SalesSummaryDaily;

class SalesSummaryDailyRepository extends BaseRepository implements SalesSummaryDailyRepositoryInterface
{
    public function __construct(SalesSummaryDaily $model)
    {
        parent::__construct($model);
    }
}
