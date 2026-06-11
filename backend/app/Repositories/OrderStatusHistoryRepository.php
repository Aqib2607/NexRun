<?php

namespace App\Repositories;

use App\Models\OrderStatusHistory;

class OrderStatusHistoryRepository extends BaseRepository implements OrderStatusHistoryRepositoryInterface
{
    public function __construct(OrderStatusHistory $model)
    {
        parent::__construct($model);
    }
}
