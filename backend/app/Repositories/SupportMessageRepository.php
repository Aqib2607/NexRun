<?php

namespace App\Repositories;

use App\Models\SupportMessage;

class SupportMessageRepository extends BaseRepository implements SupportMessageRepositoryInterface
{
    public function __construct(SupportMessage $model)
    {
        parent::__construct($model);
    }
}
