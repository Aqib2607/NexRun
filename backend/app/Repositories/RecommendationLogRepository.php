<?php

namespace App\Repositories;

use App\Models\RecommendationLog;

class RecommendationLogRepository extends BaseRepository implements RecommendationLogRepositoryInterface
{
    public function __construct(RecommendationLog $model)
    {
        parent::__construct($model);
    }
}
