<?php

namespace App\Repositories;

use App\Models\CartItem;

class CartItemRepository extends BaseRepository implements CartItemRepositoryInterface
{
    public function __construct(CartItem $model)
    {
        parent::__construct($model);
    }
}
