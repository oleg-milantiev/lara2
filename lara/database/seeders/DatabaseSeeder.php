<?php

namespace Database\Seeders;

use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\Price;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $manufacturers = Manufacturer::factory()->count(50)->create();

        $totalProducts = 5000;
        $productsPerCategory = 1000;

        $today = Carbon::parse('2026-04-23');
        $threeDaysAgo = Carbon::parse('2026-04-20');

        for ($i = 0; $i < $totalProducts; $i++) {
            $categoryId = intdiv($i, $productsPerCategory) + 1;

            $product = Product::create([
                'product_name' => 'Product ' . ($i + 1),
                'category_id' => $categoryId,
                'manufacturer_id' => $manufacturers->random()->manufacturer_id,
            ]);

            // Цены: по 0..5 к каждому продукту
            // Распределение дат:
            // - 0..2 старых цен (до апреля 2026)
            // - 0..1 цена сегодняшняя (2026-04-23)
            // - 0..2 цены 3 дня назад (2026-04-20)

            $oldPricesCount = rand(0, 2);
            for ($j = 0; $j < $oldPricesCount; $j++) {
                Price::create([
                    'product_id' => $product->product_id,
                    'price' => rand(10000, 1000000) / 10000,
                    'price_date' => Carbon::create(2025, rand(1, 12), rand(1, 28))->format('Y-m-d'),
                ]);
            }

            // today prices
            if (rand(0, 1)) {
                Price::create([
                    'product_id' => $product->product_id,
                    'price' => rand(10000, 1000000) / 10000,
                    'price_date' => $today->format('Y-m-d'),
                ]);
            }

            $threeDaysAgoCount = rand(0, 2);
            for ($j = 0; $j < $threeDaysAgoCount; $j++) {
                Price::create([
                    'product_id' => $product->product_id,
                    'price' => rand(10000, 1000000) / 10000,
                    'price_date' => $threeDaysAgo->format('Y-m-d'),
                ]);
            }
        }
    }
}
