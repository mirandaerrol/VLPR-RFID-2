<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicles';
    protected $primaryKey = 'vehicle_id';

    // REMOVED 'rfid_id' from this list
    protected $fillable = [
        'owner_id',
        'plate_number',
    ];

    // Relationship to Owner
    public function owner()
    {
        return $this->belongsTo(VehicleOwner::class, 'owner_id', 'owner_id');
    }

    // REMOVE the 'rfid' function if it existed here previously.
    // The vehicle now finds its RFID through the owner.
    
    // Optional: Accessor to easily get RFID code via vehicle->rfid_code
    public function getRfidCodeAttribute()
    {
        return $this->owner ? $this->owner->rfid_code : null;
    }
}