<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimeLog extends Model
{
    use HasFactory;

    protected $table = 'time_log';
    protected $primaryKey = 'time_log_id';
    protected $fillable = ['logs_id', 'time_in', 'time_out'];

    public function log()
    {
        return $this->belongsTo(Log::class, 'logs_id', 'logs_id');
    }
}
