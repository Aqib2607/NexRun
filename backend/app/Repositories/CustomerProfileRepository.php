<?php

namespace App\Repositories;

use App\Models\CustomerProfile;

class CustomerProfileRepository extends BaseRepository implements CustomerProfileRepositoryInterface
{
    public function __construct(CustomerProfile $model)
    {
        parent::__construct($model);
    }
}
