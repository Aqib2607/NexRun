<?php

namespace App\Repositories;

use App\Models\SupportTicket;

class SupportTicketRepository extends BaseRepository implements SupportTicketRepositoryInterface
{
    public function __construct(SupportTicket $model)
    {
        parent::__construct($model);
    }
}
