<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\CustomerProfile;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Project Seeder...');

        // 1. Create Users & Customer Profiles
        $this->command->info('Creating Users...');
        $users = User::factory(20)->create()->each(function ($user) {
            CustomerProfile::factory()->create(['user_id' => $user->id]);
        });

        // 2. Create Categories
        $this->command->info('Creating Categories...');
        $categories = Category::factory(10)->create();

        // 3. Create Brands
        $this->command->info('Creating Brands...');
        $brands = Brand::factory(10)->create();

        // 4. Create Products
        $this->command->info('Creating Products...');
        Product::factory(50)
            ->recycle($categories)
            ->recycle($brands)
            ->create();

        $this->command->info('Project Seeder finished successfully!');
    }
}
