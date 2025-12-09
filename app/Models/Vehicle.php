<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicles';
    protected $primaryKey = 'vehicle_id';
    protected $fillable = [
        'owner_id',
        'plate_number',
        'vehicle_type',
    ];

    public function owner()
    {
        return $this->belongsTo(VehicleOwner::class, 'owner_id', 'owner_id');
    }
    public function getRfidCodeAttribute()
    {
        return $this->owner ? $this->owner->rfid_code : null;
    }
}