<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\CustomerAnalytics;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductAnalytics;
use App\Models\SalesSummaryDaily;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends ApiController
{
    public function dashboard(): JsonResponse
    {
        $today = now()->toDateString();
        $thisMonth = now()->startOfMonth();

        return $this->success([
            'today' => [
                'orders'    => Order::whereDate('placed_at', $today)->count(),
                'revenue'   => (float) Order::whereDate('placed_at', $today)->paid()->sum('total_amount'),
            ],
            'this_month' => [
                'orders'    => Order::where('placed_at', '>=', $thisMonth)->count(),
                'revenue'   => (float) Order::where('placed_at', '>=', $thisMonth)->paid()->sum('total_amount'),
                'customers' => User::where('created_at', '>=', $thisMonth)->count(),
            ],
            'totals' => [
                'products'   => Product::count(),
                'customers'  => User::whereHas('roles', fn($q) => $q->where('slug', 'customer'))->count(),
                'orders'     => Order::count(),
                'revenue'    => (float) Order::paid()->sum('total_amount'),
            ],
            'recent_orders' => Order::with('customer')
                ->recent()
                ->limit(5)
                ->get()
                ->map(fn($o) => [
                    'order_number' => $o->order_number,
                    'customer'     => $o->customer->full_name,
                    'total'        => (float) $o->total_amount,
                    'status'       => $o->order_status,
                    'placed_at'    => $o->placed_at?->toIso8601String(),
                ]),
        ]);
    }

    public function sales(Request $request): JsonResponse
    {
        $from = $request->get('from', now()->subDays(30)->toDateString());
        $to = $request->get('to', now()->toDateString());

        $daily = SalesSummaryDaily::whereBetween('sales_date', [$from, $to])
            ->orderBy('sales_date')
            ->get();

        return $this->success([
            'period'     => ['from' => $from, 'to' => $to],
            'summary'    => [
                'total_revenue' => (float) $daily->sum('total_revenue'),
                'total_orders'  => $daily->sum('total_orders'),
                'avg_order_value' => $daily->avg('average_order_value') ?? 0,
            ],
            'daily_data' => $daily,
        ]);
    }

    public function products(Request $request): JsonResponse
    {
        $topProducts = ProductAnalytics::with('product:id,product_name,sku')
            ->orderByDesc('revenue_generated')
            ->limit($request->get('limit', 20))
            ->get();

        return $this->success($topProducts);
    }

    public function customers(Request $request): JsonResponse
    {
        $topCustomers = CustomerAnalytics::with('customer:id,first_name,last_name,email')
            ->orderByDesc('lifetime_value')
            ->limit($request->get('limit', 20))
            ->get();

        return $this->success($topCustomers);
    }
}
