<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| NexRun API Routes — /api/v1
|--------------------------------------------------------------------------
*/

// ── Public Routes ──────────────────────────────────────────

// Auth
Route::prefix('auth')->group(function () {
    Route::post('register',        [\App\Http\Controllers\Auth\AuthController::class, 'register']);
    Route::post('login',           [\App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::post('forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLink']);
    Route::post('reset-password',  [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset']);
    Route::post('otp/send',        [\App\Http\Controllers\Auth\OtpController::class, 'send']);
    Route::post('otp/verify',      [\App\Http\Controllers\Auth\OtpController::class, 'verify']);
    Route::get('social/{provider}',          [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirect']);
    Route::get('social/{provider}/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'callback']);
});

// Products (public browsing)
Route::prefix('products')->group(function () {
    Route::get('/',          [\App\Http\Controllers\Product\ProductController::class, 'index']);
    Route::get('/featured',  [\App\Http\Controllers\Product\ProductController::class, 'featured']);
    Route::get('/search',    [\App\Http\Controllers\Product\ProductController::class, 'search']);
    Route::get('/{product}', [\App\Http\Controllers\Product\ProductController::class, 'show']);
    Route::get('/{product}/reviews',  [\App\Http\Controllers\Review\ReviewController::class, 'forProduct']);
    Route::get('/{product}/related',  [\App\Http\Controllers\Product\ProductController::class, 'related']);
});

// Categories (public)
Route::prefix('categories')->group(function () {
    Route::get('/',           [\App\Http\Controllers\Category\CategoryController::class, 'index']);
    Route::get('/tree',       [\App\Http\Controllers\Category\CategoryController::class, 'tree']);
    Route::get('/{category}', [\App\Http\Controllers\Category\CategoryController::class, 'show']);
});

// Payment methods (public)
Route::get('payment-methods', [\App\Http\Controllers\Payment\PaymentMethodController::class, 'index']);

// Payment webhooks (public, verified by signature)
Route::prefix('payments/webhook')->group(function () {
    Route::post('/sslcommerz', [\App\Http\Controllers\Payment\WebhookController::class, 'sslcommerz']);
    Route::post('/bkash',      [\App\Http\Controllers\Payment\WebhookController::class, 'bkash']);
    Route::post('/nagad',      [\App\Http\Controllers\Payment\WebhookController::class, 'nagad']);
    Route::post('/stripe',     [\App\Http\Controllers\Payment\WebhookController::class, 'stripe']);
    Route::post('/paypal',     [\App\Http\Controllers\Payment\WebhookController::class, 'paypal']);
});


// ── Authenticated Routes ───────────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('auth/logout',  [\App\Http\Controllers\Auth\AuthController::class, 'logout']);
    Route::post('auth/refresh', [\App\Http\Controllers\Auth\AuthController::class, 'refresh']);
    Route::get('auth/me',       [\App\Http\Controllers\Auth\AuthController::class, 'me']);
    Route::post('auth/verify-email', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'verify']);
    Route::post('auth/resend-verification', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'resend']);

    // User Profile & Addresses
    Route::prefix('profile')->group(function () {
        Route::get('/',     [\App\Http\Controllers\User\ProfileController::class, 'show']);
        Route::put('/',     [\App\Http\Controllers\User\ProfileController::class, 'update']);
        Route::post('/avatar', [\App\Http\Controllers\User\ProfileController::class, 'updateAvatar']);
        Route::put('/password', [\App\Http\Controllers\User\ProfileController::class, 'changePassword']);
    });

    Route::apiResource('addresses', \App\Http\Controllers\User\AddressController::class);
    Route::put('addresses/{address}/default', [\App\Http\Controllers\User\AddressController::class, 'setDefault']);

    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/',              [\App\Http\Controllers\Cart\CartController::class, 'show']);
        Route::post('/items',        [\App\Http\Controllers\Cart\CartController::class, 'addItem']);
        Route::put('/items/{item}',  [\App\Http\Controllers\Cart\CartController::class, 'updateItem']);
        Route::delete('/items/{item}', [\App\Http\Controllers\Cart\CartController::class, 'removeItem']);
        Route::delete('/clear',      [\App\Http\Controllers\Cart\CartController::class, 'clear']);
        Route::post('/merge',        [\App\Http\Controllers\Cart\CartController::class, 'merge']);
    });

    // Wishlist
    Route::prefix('wishlist')->group(function () {
        Route::get('/',                    [\App\Http\Controllers\Wishlist\WishlistController::class, 'index']);
        Route::post('/',                   [\App\Http\Controllers\Wishlist\WishlistController::class, 'add']);
        Route::delete('/{product}',        [\App\Http\Controllers\Wishlist\WishlistController::class, 'remove']);
        Route::post('/{product}/move-to-cart', [\App\Http\Controllers\Wishlist\WishlistController::class, 'moveToCart']);
    });

    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/',          [\App\Http\Controllers\Order\OrderController::class, 'index']);
        Route::post('/',         [\App\Http\Controllers\Order\OrderController::class, 'store']);
        Route::get('/{order}',   [\App\Http\Controllers\Order\OrderController::class, 'show']);
        Route::post('/{order}/cancel', [\App\Http\Controllers\Order\OrderController::class, 'cancel']);
        Route::get('/{order}/tracking', [\App\Http\Controllers\Order\OrderController::class, 'tracking']);
    });

    // Payments
    Route::prefix('payments')->group(function () {
        Route::post('/initiate',   [\App\Http\Controllers\Payment\PaymentController::class, 'initiate']);
        Route::get('/{payment}/status', [\App\Http\Controllers\Payment\PaymentController::class, 'status']);
    });

    // Coupons
    Route::post('coupons/validate', [\App\Http\Controllers\Coupon\CouponController::class, 'validateCoupon']);

    // Reviews
    Route::post('reviews', [\App\Http\Controllers\Review\ReviewController::class, 'store']);

    // Referrals
    Route::prefix('referrals')->group(function () {
        Route::get('/code',  [\App\Http\Controllers\Referral\ReferralController::class, 'getCode']);
        Route::get('/stats', [\App\Http\Controllers\Referral\ReferralController::class, 'stats']);
    });

    // Loyalty
    Route::prefix('loyalty')->group(function () {
        Route::get('/balance',      [\App\Http\Controllers\Loyalty\LoyaltyController::class, 'balance']);
        Route::get('/transactions', [\App\Http\Controllers\Loyalty\LoyaltyController::class, 'transactions']);
        Route::post('/redeem',      [\App\Http\Controllers\Loyalty\LoyaltyController::class, 'redeem']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/',           [\App\Http\Controllers\Notification\NotificationController::class, 'index']);
        Route::put('/{id}/read',  [\App\Http\Controllers\Notification\NotificationController::class, 'markAsRead']);
        Route::put('/read-all',   [\App\Http\Controllers\Notification\NotificationController::class, 'markAllAsRead']);
        Route::get('/unread-count', [\App\Http\Controllers\Notification\NotificationController::class, 'unreadCount']);
    });

    // Support
    Route::prefix('support')->group(function () {
        Route::get('/tickets',          [\App\Http\Controllers\Support\SupportController::class, 'index']);
        Route::post('/tickets',         [\App\Http\Controllers\Support\SupportController::class, 'store']);
        Route::get('/tickets/{ticket}', [\App\Http\Controllers\Support\SupportController::class, 'show']);
        Route::post('/tickets/{ticket}/messages', [\App\Http\Controllers\Support\SupportController::class, 'addMessage']);
        Route::put('/tickets/{ticket}/close',     [\App\Http\Controllers\Support\SupportController::class, 'close']);
    });


    // ── Admin Routes ───────────────────────────────────────

    Route::prefix('admin')->middleware('role:administrator')->group(function () {

        // Users
        Route::apiResource('users', \App\Http\Controllers\Admin\UserManagementController::class);
        Route::put('users/{user}/roles', [\App\Http\Controllers\Admin\UserManagementController::class, 'updateRoles']);

        // Products (admin CRUD)
        Route::apiResource('products', \App\Http\Controllers\Admin\AdminProductController::class);

        // Categories (admin CRUD)
        Route::apiResource('categories', \App\Http\Controllers\Admin\AdminCategoryController::class);

        // Inventory
        Route::prefix('inventory')->group(function () {
            Route::get('/',            [\App\Http\Controllers\Admin\InventoryController::class, 'index']);
            Route::put('/{inventory}', [\App\Http\Controllers\Admin\InventoryController::class, 'update']);
            Route::get('/low-stock',   [\App\Http\Controllers\Admin\InventoryController::class, 'lowStock']);
            Route::post('/adjust',     [\App\Http\Controllers\Admin\InventoryController::class, 'adjust']);
        });

        // Orders (admin management)
        Route::get('orders',               [\App\Http\Controllers\Admin\AdminOrderController::class, 'index']);
        Route::get('orders/{order}',       [\App\Http\Controllers\Admin\AdminOrderController::class, 'show']);
        Route::put('orders/{order}/status', [\App\Http\Controllers\Admin\AdminOrderController::class, 'updateStatus']);

        // Payments (admin)
        Route::get('payments',               [\App\Http\Controllers\Admin\AdminPaymentController::class, 'index']);
        Route::post('payments/{payment}/refund', [\App\Http\Controllers\Admin\AdminPaymentController::class, 'refund']);

        // Coupons (admin CRUD)
        Route::apiResource('coupons', \App\Http\Controllers\Admin\AdminCouponController::class);

        // Reviews (moderation)
        Route::get('reviews',               [\App\Http\Controllers\Admin\AdminReviewController::class, 'index']);
        Route::put('reviews/{review}/moderate', [\App\Http\Controllers\Admin\AdminReviewController::class, 'moderate']);

        // Support (admin)
        Route::get('support/tickets',                  [\App\Http\Controllers\Admin\AdminSupportController::class, 'index']);
        Route::put('support/tickets/{ticket}/assign',  [\App\Http\Controllers\Admin\AdminSupportController::class, 'assign']);

        // Analytics
        Route::prefix('analytics')->group(function () {
            Route::get('/dashboard',  [\App\Http\Controllers\Admin\AnalyticsController::class, 'dashboard']);
            Route::get('/sales',      [\App\Http\Controllers\Admin\AnalyticsController::class, 'sales']);
            Route::get('/products',   [\App\Http\Controllers\Admin\AnalyticsController::class, 'products']);
            Route::get('/customers',  [\App\Http\Controllers\Admin\AnalyticsController::class, 'customers']);
        });

        // Audit Logs
        Route::get('audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index']);
    });
});
