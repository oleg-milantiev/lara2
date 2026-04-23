<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ReportController;

Route::get('/', [ReportController::class, 'index']);
