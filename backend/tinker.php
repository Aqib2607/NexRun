<?php

$users = \App\Models\User::count();
$categories = \App\Models\Category::count();
$brands = \App\Models\Brand::count();
$products = \App\Models\Product::count();

echo "--------------------------------------------------\n";
echo " NexRun Project Tinker Summary \n";
echo "--------------------------------------------------\n";
echo "Total Users: {$users}\n";
echo "Total Categories: {$categories}\n";
echo "Total Brands: {$brands}\n";
echo "Total Products: {$products}\n";
echo "--------------------------------------------------\n";

if ($products > 0) {
    $firstProduct = \App\Models\Product::first();
    echo "Sample Product: " . $firstProduct->product_name . "\n";
    echo "Product Price: $" . $firstProduct->base_price . "\n";
}

if ($users > 0) {
    $firstUser = \App\Models\User::with('customerProfile')->first();
    echo "Sample User: " . $firstUser->first_name . " " . $firstUser->last_name . "\n";
    if ($firstUser->customerProfile) {
        echo "User Phone: " . $firstUser->customerProfile->phone . "\n";
    }
}
echo "--------------------------------------------------\n";

exit;
