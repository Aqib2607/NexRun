<?php

namespace App\Repositories;

use App\Models\Size;

class SizeRepository extends BaseRepository implements SizeRepositoryInterface
{
    public function __construct(Size $model)
    {
        parent::__construct($model);
    }
}
