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
        'is_driver',
        'admin_status',
        'phone_number',
        'gender',
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
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function sentWalletTransfers()
    {
        return $this->hasMany(WalletTransfer::class, 'sender_id');
    }
    public function receivedWalletTransfers()
    {
        return $this->hasMany(WalletTransfer::class, 'recipient_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function store()
    {
    return $this->hasOne(Store::class);
    }
    public function isPendingAdmin()
    {
        return $this->admin_status === 'pending';
    }

    public function pin()
    {
        return $this->hasOne(Pin::class);
    }

    public function hasPin()
    {
        return $this->pin()->exists();
    }

    public function isApprovedAdmin()
    {
        return $this->is_admin && $this->admin_status === 'approved';
    }

    public function isSuperAdmin()
    {
        return $this->is_super_admin;
    }
    
    public function driverWallet()
    {
        return $this->hasOne(DriverWallet::class, 'driver_id');
    }

    public function cancellation()
    {
        return $this->hasOne(OrderCancellation::class);
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

        $baseUrl = 'https://api.dicebear.com/7.x/avataaars/svg';
        $seed = urlencode($this->name);
        
        $commonOptions = [
            'seed=' . $seed,
            'backgroundColor=b6e3f4',
            'mouth=smile'
        ];

        switch ($this->gender) {
            case 'male':
                $options = array_merge($commonOptions, [
                    'gender=male',
                    'hairColor=brown',
                    'clothes=shirt',
                    'clothesColor=blue'
                ]);
                break;
            case 'female':
                $options = array_merge($commonOptions, [
                    'gender=female',
                    'hairColor=black',
                    'clothes=blazer',
                    'clothesColor=red'
                ]);
                break;
            default:
                $options = array_merge($commonOptions, [
                    'clothes=hoodie',
                    'clothesColor=gray'
                ]);
                break;
        }

        return $baseUrl . '?' . implode('&', $options);
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
// Di User.php, tambahkan:
public function getRoleNames()
{
    // Contoh implementasi sederhana berdasarkan property yang ada
    $roles = [];
    if ($this->is_admin) $roles[] = 'admin';
    if ($this->is_super_admin) $roles[] = 'super_admin';
    if ($this->is_driver) $roles[] = 'driver';
    
    return collect($roles);
}
}