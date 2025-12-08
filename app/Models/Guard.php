<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guard extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'guards'; 
    protected $fillable = ['name', 'password']; 
}
