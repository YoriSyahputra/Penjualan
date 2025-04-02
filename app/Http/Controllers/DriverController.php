<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function dashboard()
    {
        return view('driver.dashboard');
    }

    public function checkTracking()
    {
        return view('driver.check_tracking');
    }

    public function deliveryProcess($id)
    {
        // Delivery process logic here
        $order = Order::findOrFail($id);
        return view('driver.delivery_process', compact('order'));
    }

    // API Endpoint untuk validasi nomor resi
    public function checkTrackingNumber(Request $request)
    {
        try {
            $number = $request->input('number');
            
            if (!$number) {
                return response()->json([
                    'exists' => false,
                    'message' => 'Nomor resi tidak boleh kosong'
                ]);
            }
            
            // FIX: Pastikan field yang digunakan konsisten (nomor_resi)
            $order = Order::where('nomor_resi', $number)->first();
            
            return response()->json([
                'exists' => $order ? true : false,
                'message' => $order ? 'Nomor resi ditemukan' : 'Nomor resi tidak ditemukan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exists' => false,
                'message' => 'Terjadi kesalahan saat memproses permintaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // API Endpoint untuk mendapatkan detail resi
    public function checkTrackingDetails(Request $request)
    {
        try {
            $trackingNumber = $request->input('tracking_number');
            
            if (!$trackingNumber) {
                return response()->json([
                    'exists' => false,
                    'message' => 'Nomor resi tidak boleh kosong'
                ]);
            }
            
            // FIX: Pastikan field yang digunakan konsisten (nomor_resi)
            $order = Order::where('nomor_resi', $trackingNumber)->first();
            
            if ($order) {
                return response()->json([
                    'exists' => true,
                    'order' => $order
                ]);
            } else {
                return response()->json([
                    'exists' => false,
                    'message' => 'Paket tidak ditemukan'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'exists' => false,
                'message' => 'Terjadi kesalahan saat memproses permintaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}