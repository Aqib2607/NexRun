<?php

namespace App\Repositories;

use App\Models\WishlistItem;

class WishlistItemRepository extends BaseRepository implements WishlistItemRepositoryInterface
{
    public function __construct(WishlistItem $model)
    {
        parent::__construct($model);
    }
}
