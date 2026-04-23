<?php

namespace App\Http\Controllers;

use App\Models\ReportProcess;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $processes = ReportProcess::with('status')
            ->orderBy('rp_start_datetime', 'desc')
            ->paginate(25);

        return view('index', compact('processes'));
    }
}
