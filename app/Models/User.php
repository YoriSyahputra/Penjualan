<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_super_admin',
        'admin_status',
        'phone_number',
        'address',
        'profile_photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'is_super_admin' => 'boolean',
    ];

    public function store()
    {
    return $this->hasOne(Store::class);
    }
    public function isPendingAdmin()
    {
        return $this->admin_status === 'pending';
    }

    public function isApprovedAdmin()
    {
        return $this->is_admin && $this->admin_status === 'approved';
    }

    public function isSuperAdmin()
    {
        return $this->is_super_admin;
    }


    /**
     * Get the URL of the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return Storage::url($this->profile_photo);
        }

        return null;
    }

    /**
     * Update the user's profile photo.
     *
     * @param  \Illuminate\Http\UploadedFile  $photo
     * @return void
     */
    public function updateProfilePhoto($photo)
    {
        // Delete old photo if exists
        if ($this->profile_photo) {
            Storage::delete($this->profile_photo);
        }

        // Store new photo
        $path = $photo->store('profile-photos', 'public');
        $this->update(['profile_photo' => $path]);
    }
}