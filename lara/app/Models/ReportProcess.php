<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportProcess extends Model
{
    protected $table = 'report_process';
    protected $primaryKey = 'rp_id';
    public $timestamps = false;

    protected $casts = [
        'rp_start_datetime' => 'datetime',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProcessStatus::class, 'ps_id', 'ps_id');
    }
}
