<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;

    protected $primaryKey = 'logs_id';
    protected $fillable = ['vehicle_id', 'owner_id', 'rfid_id'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_id');
    }

    public function owner()
    {
        return $this->belongsTo(VehicleOwner::class, 'owner_id', 'owner_id');
    }

    public function rfid()
    {
        return $this->belongsTo(Rfid::class, 'rfid_id', 'rfid_id');
    }

    public function timeLog()
    {
        return $this->hasOne(TimeLog::class, 'logs_id', 'logs_id');
    }
}
