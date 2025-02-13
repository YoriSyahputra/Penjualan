<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pin',
        'last_changed_at',
        'attempts',
        'is_locked',
        'locked_until'
    ];

    protected $casts = [
        'last_changed_at' => 'datetime',
        'locked_until' => 'datetime',
        'is_locked' => 'boolean'
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Cek apakah PIN terkunci
    public function isLocked()
    {
        if (!$this->is_locked) {
            return false;
        }

        if ($this->locked_until && now()->gt($this->locked_until)) {
            $this->update([
                'is_locked' => false,
                'attempts' => 0,
                'locked_until' => null
            ]);
            return false;
        }

        return true;
    }

    // Tambah attempt gagal dan kunci PIN jika sudah 3 kali salah
    public function incrementAttempts()
    {
        $this->attempts++;
        if ($this->attempts >= 3) {
            $this->is_locked = true;
            $this->locked_until = now()->addMinutes(30);
        }
        $this->save();
    }

    // Reset attempt
    public function resetAttempts()
    {
        $this->update([
            'attempts' => 0,
            'is_locked' => false,
            'locked_until' => null
        ]);
    }
}