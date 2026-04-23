<?php

namespace Tests\Unit;

use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created()
    {
        $manufacturer = Manufacturer::factory()->create();
        $product = Product::factory()->create([
            'product_name' => 'Test Product',
            'category_id' => 10,
            'manufacturer_id' => $manufacturer->manufacturer_id,
        ]);

        $this->assertDatabaseHas('product', [
            'product_id' => $product->product_id,
            'product_name' => 'Test Product',
            'category_id' => 10,
            'manufacturer_id' => $manufacturer->manufacturer_id,
        ]);
    }

    public function test_product_belongs_to_manufacturer()
    {
        $manufacturer = Manufacturer::factory()->create();
        $product = Product::factory()->create([
            'manufacturer_id' => $manufacturer->manufacturer_id,
        ]);

        $this->assertInstanceOf(Manufacturer::class, $product->manufacturer);
        $this->assertEquals($manufacturer->manufacturer_id, $product->manufacturer->manufacturer_id);
    }

    public function test_product_has_many_prices()
    {
        $product = Product::factory()->create();
        Price::factory()->count(2)->create([
            'product_id' => $product->product_id,
        ]);

        $this->assertCount(2, $product->prices);
        $this->assertInstanceOf(Price::class, $product->prices->first());
    }
}
