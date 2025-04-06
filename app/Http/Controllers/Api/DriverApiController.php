<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryHistory;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverApiController extends Controller
{
    /**
     * Update driver location during delivery
     */
    public function updateLocation(Request $request, $id)
    {
        $request->validate([
            'lat' => 'required',
            'lng' => 'required',
        ]);

        $order = Order::findOrFail($id);
        $delivery = DeliveryHistory::where('order_id', $order->id)
            ->where('driver_id', Auth::id())
            ->where('status', 'on_the_way')
            ->latest()
            ->first();

        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Pengiriman tidak ditemukan atau sudah selesai'
            ], 404);
        }

        $delivery->update([
            'location_lat' => $request->lat,
            'location_lng' => $request->lng,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi berhasil diperbarui',
            'data' => $delivery
        ]);
    }

    /**
     * Get list of active deliveries for the driver
     */
    public function activeDeliveries()
    {
        $activeDeliveries = DeliveryHistory::with('order')
            ->where('driver_id', Auth::id())
            ->whereIn('status', ['picked_up', 'on_the_way'])
            ->latest()
            ->get()
            ->unique('order_id');

        return response()->json([
            'success' => true,
            'data' => $activeDeliveries
        ]);
    }
}