<?php

namespace App\Repositories;

use App\Models\LoyaltyTransaction;

class LoyaltyTransactionRepository extends BaseRepository implements LoyaltyTransactionRepositoryInterface
{
    public function __construct(LoyaltyTransaction $model)
    {
        parent::__construct($model);
    }
}
