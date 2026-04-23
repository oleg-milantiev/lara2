<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessStatus extends Model
{
    protected $table = 'process_status';
    protected $primaryKey = 'ps_id';
    public $timestamps = false;
    public $incrementing = false;
}
