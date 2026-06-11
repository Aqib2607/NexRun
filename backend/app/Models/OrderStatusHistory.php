<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    public $timestamps = false;
    protected $table = 'order_status_history';

    protected $fillable = ['order_id', 'previous_status', 'new_status', 'note', 'changed_by'];

    protected function casts(): array
    {
        return ['changed_at' => 'datetime'];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
