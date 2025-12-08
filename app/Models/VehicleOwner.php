<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleOwner extends Model
{
    use HasFactory;

    protected $table = 'vehicle_owner';  

    protected $primaryKey = 'owner_id';  

    protected $fillable = [
        'f_name',
        'l_name',
        'address',
        'contact_number',
        'school_year',
        'type_of_owner',
        'valid_id',
        'rfid_code',
    ];
}
