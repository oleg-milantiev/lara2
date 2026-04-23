<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('manufacturer', function (Blueprint $table) {
            $table->id('manufacturer_id');
            $table->string('manufacturer_name');
        });

        Schema::create('product', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('manufacturer_id');

            $table->foreign('manufacturer_id')->references('manufacturer_id')->on('manufacturer')->onDelete('cascade');
        });

        Schema::create('price', function (Blueprint $table) {
            $table->id('price_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('price', 15, 4);
            $table->date('price_date');

            $table->foreign('product_id')->references('product_id')->on('product')->onDelete('cascade');
        });

        Schema::table('price', function (Blueprint $table) {
            $table->index('price_date', 'idx_price_price_date');
            $table->index(['product_id', DB::raw('price_date DESC')], 'idx_price_product_date');
            $table->index(['price_date', 'product_id', 'price'], 'idx_price_min_max');
            $table->index(['product_id', DB::raw('price DESC'), DB::raw('price_date ASC')], 'idx_last_week_price_max');
            $table->index(['product_id', DB::raw('price ASC'), DB::raw('price_date ASC')], 'idx_last_week_price_min');
        });

        Schema::table('product', function (Blueprint $table) {
            $table->index('category_id', 'idx_product_category_id');
        });

        Schema::create('process_status', function (Blueprint $table) {
            $table->unsignedBigInteger('ps_id')->primary();
            $table->string('ps_name');
        });

        Schema::create('report_process', function (Blueprint $table) {
            $table->id('rp_id');
            $table->integer('rp_pid');
            $table->dateTime('rp_start_datetime');
            $table->float('rp_exec_time')->nullable();
            $table->unsignedBigInteger('ps_id');
            $table->string('rp_file_save_path')->nullable();

            $table->foreign('ps_id')->references('ps_id')->on('process_status')->onDelete('cascade');
        });

        DB::table('process_status')->insert([
            ['ps_id' => 1, 'ps_name' => 'Запуск'],
            ['ps_id' => 2, 'ps_name' => 'Завершен'],
            ['ps_id' => 3, 'ps_name' => 'Ошибка'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropIndex('idx_product_category_id');
        });

        Schema::table('price', function (Blueprint $table) {
            $table->dropIndex('idx_last_week_price_min');
            $table->dropIndex('idx_last_week_price_max');
            $table->dropIndex('idx_price_min_max');
            $table->dropIndex('idx_price_product_date');
            $table->dropIndex('idx_price_price_date');
        });

        Schema::dropIfExists('report_process');
        Schema::dropIfExists('process_status');
        Schema::dropIfExists('price');
        Schema::dropIfExists('product');
        Schema::dropIfExists('manufacturer');
    }
};
