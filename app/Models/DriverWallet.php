<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'balance',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}