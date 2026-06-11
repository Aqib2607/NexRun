<?php

namespace App\Repositories;

use App\Models\Color;

class ColorRepository extends BaseRepository implements ColorRepositoryInterface
{
    public function __construct(Color $model)
    {
        parent::__construct($model);
    }
}
