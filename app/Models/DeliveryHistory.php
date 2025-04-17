<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'driver_id',
        'status',
        'notes',
        'photo_proof',
        'delivered_at',
        'status_history'
    ];

    // Cast kolom JSON ke array biar enak dipakainya
    protected $casts = [
        'status_history' => 'array',
        'delivered_at' => 'datetime'
    ];

    /**
     * Get the order that owns the delivery.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the driver that made the delivery.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
    
    /**
     * Dapetin history status dalam format yang rapi.
     * Kayak storyline delivery gitu, biar nggak scrolling-scrolling amat pas liat history.
     */
    public function getStatusTimelineAttribute()
    {
        if (empty($this->status_history)) {
            return [];
        }
        
        return collect($this->status_history)->sortByDesc(function ($item) {
            return $item['timestamp'] ?? now()->subYear();
        })->values()->all();
    }
}