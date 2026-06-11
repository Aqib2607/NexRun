<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesSummaryDaily extends Model
{
    protected $table = 'sales_summary_daily';
    protected $primaryKey = 'sales_date';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sales_date', 'total_orders', 'total_revenue',
        'total_customers', 'average_order_value', 'total_items_sold',
    ];

    protected function casts(): array
    {
        return [
            'sales_date'          => 'date',
            'total_revenue'       => 'decimal:2',
            'average_order_value' => 'decimal:2',
        ];
    }
}
