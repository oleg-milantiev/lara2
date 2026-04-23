<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контроль выполнения процессов</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Контроль выполнения процессов</h1>

    @if($processes->isEmpty())
        <p>Нет процессов</p>
    @else
        <table class="table table-striped mt-4">
            <thead>
            <tr>
                <th>Дата процесса</th>
                <th>Время запуска</th>
                <th>Время выполнения</th>
                <th>Статус процесса</th>
                <th>Имя файла</th>
            </tr>
            </thead>
            <tbody>
            @foreach($processes as $process)
                <tr>
                    <td>{{ $process->rp_start_datetime->format('d.m.Y') }}</td>
                    <td>{{ $process->rp_start_datetime->format('H:i:s') }}</td>
                    <td>{{ $process->rp_exec_time }}</td>
                    <td>{{ $process->status->ps_name ?? 'N/A' }}</td>
                    <td>
                        @if($process->ps_id == 2 && $process->rp_file_save_path)
                            <a href="{{ config('reports.url') . '/' . basename($process->rp_file_save_path) }}" target="_blank">
                                {{ basename($process->rp_file_save_path) }}
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <nav>
            <ul class="pagination">
                @for ($i = 1; $i <= $processes->lastPage(); $i++)
                    <li class="page-item {{ ($processes->currentPage() == $i) ? ' active' : '' }}">
                        @if($processes->currentPage() == $i)
                            <span class="page-link"><strong>{{ $i }}</strong></span>
                        @else
                            <a class="page-link" href="{{ $processes->url($i) }}">{{ $i }}</a>
                        @endif
                    </li>
                @endfor
            </ul>
        </nav>
    @endif
</div>
</body>
</html>
