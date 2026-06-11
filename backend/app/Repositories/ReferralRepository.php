<?php

namespace App\Repositories;

use App\Models\Referral;

class ReferralRepository extends BaseRepository implements ReferralRepositoryInterface
{
    public function __construct(Referral $model)
    {
        parent::__construct($model);
    }
}
