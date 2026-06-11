<?php

namespace App\Repositories;

use App\Models\CustomerAddress;

class CustomerAddressRepository extends BaseRepository implements CustomerAddressRepositoryInterface
{
    public function __construct(CustomerAddress $model)
    {
        parent::__construct($model);
    }
}
