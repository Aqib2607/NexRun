<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\Size;
use App\Models\Color;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Payment Methods ────────────────────────────────
        $methods = [
            ['method_name' => 'SSLCommerz',       'slug' => 'sslcommerz',        'display_name' => 'SSLCommerz',        'sort_order' => 1],
            ['method_name' => 'bKash',             'slug' => 'bkash',             'display_name' => 'bKash',             'sort_order' => 2],
            ['method_name' => 'Nagad',             'slug' => 'nagad',             'display_name' => 'Nagad',             'sort_order' => 3],
            ['method_name' => 'Rocket',            'slug' => 'rocket',            'display_name' => 'Rocket',            'sort_order' => 4],
            ['method_name' => 'Stripe',            'slug' => 'stripe',            'display_name' => 'Credit/Debit Card', 'sort_order' => 5],
            ['method_name' => 'PayPal',            'slug' => 'paypal',            'display_name' => 'PayPal',            'sort_order' => 6],
            ['method_name' => 'CashOnDelivery',    'slug' => 'cod',               'display_name' => 'Cash on Delivery',  'sort_order' => 7],
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(['slug' => $method['slug']], $method);
        }

        // ── Sizes ──────────────────────────────────────────
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];
        foreach ($sizes as $i => $size) {
            Size::firstOrCreate(['size_code' => $size], ['sort_order' => $i]);
        }

        // ── Colors ─────────────────────────────────────────
        $colors = [
            ['color_name' => 'Black',      'hex_code' => '#000000'],
            ['color_name' => 'White',      'hex_code' => '#FFFFFF'],
            ['color_name' => 'Navy Blue',  'hex_code' => '#1B2A4A'],
            ['color_name' => 'Red',        'hex_code' => '#DC2626'],
            ['color_name' => 'Grey',       'hex_code' => '#6B7280'],
            ['color_name' => 'Green',      'hex_code' => '#059669'],
            ['color_name' => 'Orange',     'hex_code' => '#EA580C'],
            ['color_name' => 'Pink',       'hex_code' => '#EC4899'],
            ['color_name' => 'Yellow',     'hex_code' => '#EAB308'],
            ['color_name' => 'Teal',       'hex_code' => '#0D9488'],
            ['color_name' => 'Brown',      'hex_code' => '#78350F'],
            ['color_name' => 'Beige',      'hex_code' => '#D4C5A9'],
        ];

        foreach ($colors as $color) {
            Color::firstOrCreate(['color_name' => $color['color_name']], $color);
        }

        // ── Default Warehouse ──────────────────────────────
        Warehouse::firstOrCreate(
            ['warehouse_name' => 'Main Warehouse'],
            ['location' => 'Dhaka, Bangladesh', 'status' => 'active']
        );
    }
}
