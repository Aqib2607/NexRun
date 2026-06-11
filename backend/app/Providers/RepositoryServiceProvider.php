<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\App\Repositories\UserRepositoryInterface::class, \App\Repositories\UserRepository::class);
        $this->app->bind(\App\Repositories\RoleRepositoryInterface::class, \App\Repositories\RoleRepository::class);
        $this->app->bind(\App\Repositories\PermissionRepositoryInterface::class, \App\Repositories\PermissionRepository::class);
        $this->app->bind(\App\Repositories\CustomerProfileRepositoryInterface::class, \App\Repositories\CustomerProfileRepository::class);
        $this->app->bind(\App\Repositories\CustomerAddressRepositoryInterface::class, \App\Repositories\CustomerAddressRepository::class);
        $this->app->bind(\App\Repositories\CategoryRepositoryInterface::class, \App\Repositories\CategoryRepository::class);
        $this->app->bind(\App\Repositories\BrandRepositoryInterface::class, \App\Repositories\BrandRepository::class);
        $this->app->bind(\App\Repositories\ProductRepositoryInterface::class, \App\Repositories\ProductRepository::class);
        $this->app->bind(\App\Repositories\ProductImageRepositoryInterface::class, \App\Repositories\ProductImageRepository::class);
        $this->app->bind(\App\Repositories\SizeRepositoryInterface::class, \App\Repositories\SizeRepository::class);
        $this->app->bind(\App\Repositories\ColorRepositoryInterface::class, \App\Repositories\ColorRepository::class);
        $this->app->bind(\App\Repositories\ProductVariantRepositoryInterface::class, \App\Repositories\ProductVariantRepository::class);
        $this->app->bind(\App\Repositories\WarehouseRepositoryInterface::class, \App\Repositories\WarehouseRepository::class);
        $this->app->bind(\App\Repositories\InventoryRepositoryInterface::class, \App\Repositories\InventoryRepository::class);
        $this->app->bind(\App\Repositories\InventoryTransactionRepositoryInterface::class, \App\Repositories\InventoryTransactionRepository::class);
        $this->app->bind(\App\Repositories\CartRepositoryInterface::class, \App\Repositories\CartRepository::class);
        $this->app->bind(\App\Repositories\CartItemRepositoryInterface::class, \App\Repositories\CartItemRepository::class);
        $this->app->bind(\App\Repositories\WishlistRepositoryInterface::class, \App\Repositories\WishlistRepository::class);
        $this->app->bind(\App\Repositories\WishlistItemRepositoryInterface::class, \App\Repositories\WishlistItemRepository::class);
        $this->app->bind(\App\Repositories\OrderRepositoryInterface::class, \App\Repositories\OrderRepository::class);
        $this->app->bind(\App\Repositories\OrderItemRepositoryInterface::class, \App\Repositories\OrderItemRepository::class);
        $this->app->bind(\App\Repositories\OrderStatusHistoryRepositoryInterface::class, \App\Repositories\OrderStatusHistoryRepository::class);
        $this->app->bind(\App\Repositories\PaymentMethodRepositoryInterface::class, \App\Repositories\PaymentMethodRepository::class);
        $this->app->bind(\App\Repositories\PaymentRepositoryInterface::class, \App\Repositories\PaymentRepository::class);
        $this->app->bind(\App\Repositories\RefundRepositoryInterface::class, \App\Repositories\RefundRepository::class);
        $this->app->bind(\App\Repositories\CouponRepositoryInterface::class, \App\Repositories\CouponRepository::class);
        $this->app->bind(\App\Repositories\CouponUsageRepositoryInterface::class, \App\Repositories\CouponUsageRepository::class);
        $this->app->bind(\App\Repositories\ReferralRepositoryInterface::class, \App\Repositories\ReferralRepository::class);
        $this->app->bind(\App\Repositories\LoyaltyTransactionRepositoryInterface::class, \App\Repositories\LoyaltyTransactionRepository::class);
        $this->app->bind(\App\Repositories\ReviewRepositoryInterface::class, \App\Repositories\ReviewRepository::class);
        $this->app->bind(\App\Repositories\SupportTicketRepositoryInterface::class, \App\Repositories\SupportTicketRepository::class);
        $this->app->bind(\App\Repositories\SupportMessageRepositoryInterface::class, \App\Repositories\SupportMessageRepository::class);
        $this->app->bind(\App\Repositories\NotificationRepositoryInterface::class, \App\Repositories\NotificationRepository::class);
        $this->app->bind(\App\Repositories\ProductViewRepositoryInterface::class, \App\Repositories\ProductViewRepository::class);
        $this->app->bind(\App\Repositories\RecommendationLogRepositoryInterface::class, \App\Repositories\RecommendationLogRepository::class);
        $this->app->bind(\App\Repositories\SalesSummaryDailyRepositoryInterface::class, \App\Repositories\SalesSummaryDailyRepository::class);
        $this->app->bind(\App\Repositories\ProductAnalyticsRepositoryInterface::class, \App\Repositories\ProductAnalyticsRepository::class);
        $this->app->bind(\App\Repositories\CustomerAnalyticsRepositoryInterface::class, \App\Repositories\CustomerAnalyticsRepository::class);
        $this->app->bind(\App\Repositories\AuditLogRepositoryInterface::class, \App\Repositories\AuditLogRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
