<?php

use App\Models\AuditLog;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Color;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\CustomerAddress;
use App\Models\CustomerAnalytics;
use App\Models\CustomerProfile;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\LoyaltyTransaction;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductAnalytics;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductView;
use App\Models\RecommendationLog;
use App\Models\Referral;
use App\Models\Refund;
use App\Models\Review;
use App\Models\Role;
use App\Models\SalesSummaryDaily;
use App\Models\Size;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Wishlist;
use App\Models\WishlistItem;

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can query all tables in the database to verify integrity', function () {
    $models = [
        AuditLog::class,
        Brand::class,
        Cart::class,
        CartItem::class,
        Category::class,
        Color::class,
        Coupon::class,
        CouponUsage::class,
        CustomerAddress::class,
        CustomerAnalytics::class,
        CustomerProfile::class,
        Inventory::class,
        InventoryTransaction::class,
        LoyaltyTransaction::class,
        Notification::class,
        Order::class,
        OrderItem::class,
        OrderStatusHistory::class,
        Payment::class,
        PaymentMethod::class,
        Permission::class,
        Product::class,
        ProductAnalytics::class,
        ProductImage::class,
        ProductVariant::class,
        ProductView::class,
        RecommendationLog::class,
        Referral::class,
        Refund::class,
        Review::class,
        Role::class,
        SalesSummaryDaily::class,
        Size::class,
        SupportMessage::class,
        SupportTicket::class,
        User::class,
        Warehouse::class,
        Wishlist::class,
        WishlistItem::class,
    ];

    foreach ($models as $modelClass) {
        // Just verify that we can execute a count query without SQL syntax errors
        // This guarantees the table exists and the model is wired correctly.
        $count = $modelClass::count();
        expect($count)->toBeGreaterThanOrEqual(0);
        
        // If there are records, grab the first one to ensure attribute hydration works
        if ($count > 0) {
            $record = $modelClass::first();
            expect($record)->not->toBeNull();
        }
    }
});
