<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rfid extends Model
{
     use HasFactory;

    protected $primaryKey = 'rfid_id';
    protected $fillable = ['rfid_code'];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'rfid_id');
    }
}
