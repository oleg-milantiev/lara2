<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Manufacturer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Carbon\Carbon;

class ReportCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_command_fails_if_no_products()
    {
        $this->artisan('app:report 999')
            ->assertExitCode(1)
            ->expectsOutput('В категории с ID 999 нет продуктов.');
    }

    public function test_report_command_creates_file_and_logs_process()
    {
        // Setup data
        $manufacturer = Manufacturer::factory()->create(['manufacturer_name' => 'BrandA']);
        $product = Product::factory()->create([
            'product_name' => 'Gadget',
            'category_id' => 1,
            'manufacturer_id' => $manufacturer->manufacturer_id
        ]);

        // Use DB directly for price to avoid potential issues with model if it's not fully ready
        DB::table('price')->insert([
            'product_id' => $product->product_id,
            'price' => 100.00,
            'price_date' => Carbon::now()->subDays(10)->format('Y-m-d')
        ]);

        $this->artisan('app:report 1')
            ->assertExitCode(0)
            ->expectsOutputToContain('Отчет успешно создан');

        $this->assertDatabaseHas('report_process', [
            'ps_id' => 2, // Завершен
        ]);
    }
}
