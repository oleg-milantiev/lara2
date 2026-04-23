<?php

namespace Tests\Feature;

use App\Models\ReportProcess;
use App\Models\ProcessStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_displays_processes()
    {
        $status = ProcessStatus::find(1);
        ReportProcess::insert([
            'rp_pid' => 100,
            'rp_start_datetime' => Carbon::now(),
            'ps_id' => $status->ps_id,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Запуск');
        $response->assertSee('100');
    }
}
