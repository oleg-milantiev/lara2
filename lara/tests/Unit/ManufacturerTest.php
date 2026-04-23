<?php

namespace Tests\Unit;

use App\Models\Manufacturer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManufacturerTest extends TestCase
{
    use RefreshDatabase;

    public function test_manufacturer_can_be_created()
    {
        $manufacturer = Manufacturer::factory()->create([
            'manufacturer_name' => 'Test Manufacturer',
        ]);

        $this->assertDatabaseHas('manufacturer', [
            'manufacturer_id' => $manufacturer->manufacturer_id,
            'manufacturer_name' => 'Test Manufacturer',
        ]);
    }

    public function test_manufacturer_has_many_products()
    {
        $manufacturer = Manufacturer::factory()->create();
        Product::factory()->count(3)->create([
            'manufacturer_id' => $manufacturer->manufacturer_id,
        ]);

        $this->assertCount(3, $manufacturer->products);
        $this->assertInstanceOf(Product::class, $manufacturer->products->first());
    }
}
