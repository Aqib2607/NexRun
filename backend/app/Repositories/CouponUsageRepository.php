<?php

namespace App\Repositories;

use App\Models\CouponUsage;

class CouponUsageRepository extends BaseRepository implements CouponUsageRepositoryInterface
{
    public function __construct(CouponUsage $model)
    {
        parent::__construct($model);
    }
}
