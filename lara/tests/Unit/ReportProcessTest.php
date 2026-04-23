<?php

namespace Tests\Unit;

use App\Models\ReportProcess;
use App\Models\ProcessStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ReportProcessTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_process_can_be_created()
    {
        $status = ProcessStatus::find(1); // 'Запуск' - created by migration
        $now = Carbon::now();

        $process = new ReportProcess();
        $process->rp_pid = 1234;
        $process->rp_start_datetime = $now;
        $process->ps_id = $status->ps_id;
        $process->save();

        $this->assertDatabaseHas('report_process', [
            'rp_id' => $process->rp_id,
            'rp_pid' => 1234,
            'ps_id' => 1,
        ]);
    }

    public function test_report_process_belongs_to_status()
    {
        $status = ProcessStatus::find(2); // 'Завершен'
        $process = new ReportProcess();
        $process->rp_pid = 1235;
        $process->rp_start_datetime = Carbon::now();
        $process->ps_id = $status->ps_id;
        $process->save();

        $this->assertInstanceOf(ProcessStatus::class, $process->status);
        $this->assertEquals('Завершен', $process->status->ps_name);
    }
}
