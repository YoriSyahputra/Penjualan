<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'alamat_lengkap',
        'provinsi',
        'kota',
        'kecamatan',
        'kode_pos',
        'is_primary'
    ];
    protected $casts = [
        'is_primary' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}