<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DutyAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'duty_date',
        'shift_start',
        'shift_end',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
