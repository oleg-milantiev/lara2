<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Throwable;

#[Signature('app:report {category_id}')]
#[Description('Генерация CSV отчета по категории')]
class ReportCommand extends Command
{
    private const FILE_NAME_TEMPLATE = 'report_{category_id}_{datetime}.csv';

    public function handle()
    {
        $startDatetime = Carbon::now();
        $reportProcessId = DB::table('report_process')->insertGetId([
            'rp_pid' => getmypid(),
            'rp_start_datetime' => $startDatetime,
            'ps_id' => 1, // Запуск
        ], 'rp_id');

        try {
            $categoryId = $this->argument('category_id');

            $exists = DB::table('product')->where('category_id', $categoryId)->exists();

            if (!$exists) {
                $errorMessage = "В категории с ID {$categoryId} нет продуктов.";
                $this->error($errorMessage);

                DB::table('report_process')->where('rp_id', $reportProcessId)->update([
                    'ps_id' => 3, // Ошибка
                    'rp_exec_time' => (float) abs(microtime(true) - $startDatetime->getTimestamp()),
                ]);

                return 1;
            }

            $now = Carbon::now();
            $fileName = str_replace(
                ['{category_id}', '{datetime}'],
                [$categoryId, $now->format('Y-m-d_H-i-s')],
                self::FILE_NAME_TEMPLATE
            );

            $basePath = config('reports.path', 'reports');
            if (!file_exists($basePath)) {
                mkdir($basePath, 755, true);
            }

            $sql = file_get_contents(base_path('database/queries/report_data.sql'));
            $results = DB::select($sql, ['category_id' => $categoryId]);

            $fullPath = $basePath . DIRECTORY_SEPARATOR . $fileName;
            $handle = fopen($fullPath, 'w');

            fputcsv($handle, ['manufacturer_name', 'product_name', 'price', 'price_date']);

            foreach ($results as $row) {
                fputcsv($handle, [
                    $row->manufacturer_name,
                    $row->product_name,
                    $row->min_price,
                    $row->min_price_date,
                ]);
                fputcsv($handle, [
                    $row->manufacturer_name,
                    $row->product_name,
                    $row->max_price,
                    $row->max_price_date,
                ]);
            }

            fclose($handle);

            DB::table('report_process')->where('rp_id', $reportProcessId)->update([
                'ps_id' => 2, // Завершен
                'rp_exec_time' => (float) abs(microtime(true) - $startDatetime->getTimestamp()),
                'rp_file_save_path' => $fileName,
            ]);

            $this->info("Отчет успешно создан: {$fullPath}");
            return 0;

        } catch (Throwable $e) {
            Log::critical("Ошибка при создании отчета: " . $e->getMessage(), [
                'exception' => $e,
                'category_id' => $this->argument('category_id') ?? null,
                'report_process_id' => $reportProcessId
            ]);

            DB::table('report_process')->where('rp_id', $reportProcessId)->update([
                'ps_id' => 3, // Ошибка
                'rp_exec_time' => (float) abs(microtime(true) - $startDatetime->getTimestamp()),
            ]);

            $this->error("Ошибка при создании отчета: " . $e->getMessage());
            return 1;
        }
    }
}
