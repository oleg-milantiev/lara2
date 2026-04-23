<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_price_can_be_created()
    {
        $product = Product::factory()->create();
        $price = Price::factory()->create([
            'product_id' => $product->product_id,
            'price' => 123.4567,
            'price_date' => '2026-04-23',
        ]);

        $this->assertDatabaseHas('price', [
            'price_id' => $price->price_id,
            'product_id' => $product->product_id,
            'price' => 123.4567,
            'price_date' => '2026-04-23',
        ]);
    }

    public function test_price_belongs_to_product()
    {
        $product = Product::factory()->create();
        $price = Price::factory()->create([
            'product_id' => $product->product_id,
        ]);

        $this->assertInstanceOf(Product::class, $price->product);
        $this->assertEquals($product->product_id, $price->product->product_id);
    }
}
