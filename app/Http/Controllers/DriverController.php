<?php 

namespace App\Http\Controllers;

use App\Models\DeliveryHistory;
use App\Models\Order;
use App\Models\LudwigWallet;
use App\Models\DriverWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DeliveryHistoryExport;
use PDF;    

class DriverController extends Controller
{
    public function dashboard()
    {
        $driverId = Auth::id(); // Tambahkan ini untuk mendapatkan ID driver yang login
        $wallet = DriverWallet::where('driver_id', $driverId)->first();
        $walletBalance = $wallet ? $wallet->balance : 0;
        $completeToday = Order::where(function($query) {
            $query->where('status_order', 'delivered')
                  ->orWhere('status_order', 'completed');
          })
          ->whereDate('updated_at', Carbon::today())
          ->count();
        $pendingDelivery = Order::where('status_order', 'on_delivery')
                            ->whereDate('updated_at', Carbon::today())
                            ->count();                   
        $totalDelivery = Order::whereIn('status_order', ['delivered', 'completed'])
                            ->count();    
        $driverId = Auth::id(); // Ini sudah integer ID
        $recentDeliveries = DeliveryHistory::where('driver_id', $driverId) // Tanpa ->id
                                                    ->with(['order', 'order.user', 'order.user.addresses', 'order.store', 'order.store.user', 'order.store.user.addresses'])
                                                    ->latest()
                                                    ->take(3)
                                                    ->get();
        return view('driver.home', compact('completeToday', 'walletBalance','pendingDelivery','totalDelivery','recentDeliveries'));
    }

    public function checkTracking()
    {
        return view('driver.check_tracking');
    }

    public function deliveryProcess($id)
    {
        $order = Order::findOrFail($id);
        $alamatLengkap = $order->alamat_lengkap . ', ' . $order->kecamatan . ', ' . $order->kota . ', ' . $order->provinsi . ' ' . $order->kode_pos;
        
        // Cek apakah sudah ada entri delivery untuk order ini
        $delivery = DeliveryHistory::where('order_id', $id)
            ->where('driver_id', Auth::id())
            ->first();
            
        return view('driver.delivery_process', compact('order', 'delivery'));
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

    public function startDelivery(Request $request, $id)
{
    $order = Order::findOrFail($id);
    $alamatLengkap = $order->alamat_lengkap . ', ' . $order->kecamatan . ', ' . $order->kota . ', ' . $order->provinsi . ' ' . $order->kode_pos;
    
    // Cek dulu apakah sudah ada yang ngambil paket ini
    $existingPickup = DeliveryHistory::where('order_id', $id)
        ->where('status', 'picked_up')
        ->first();
        
    if ($existingPickup && $existingPickup->driver_id != Auth::id()) {
        return redirect()->back()->with('error', 'Paket ini sudah diambil oleh kurir lain');
    }
    
    // Cek apakah sudah ada record untuk driver ini
    $existingDelivery = DeliveryHistory::where('order_id', $id)
        ->where('driver_id', Auth::id())
        ->first();
        
    if ($existingDelivery) {
        // Update record yang sudah ada
        $existingDelivery->update([
            'status' => 'picked_up',
            'notes' => 'Paket telah diambil oleh kurir',
            'status_history' => DB::raw("JSON_ARRAY_APPEND(COALESCE(status_history, JSON_ARRAY()), '$', JSON_OBJECT('status', 'picked_up', 'timestamp', '".now()."', 'notes', 'Paket telah diambil oleh kurir'))")
        ]);
    } else {
        // Buat record baru dengan status history
        DeliveryHistory::create([
            'order_id' => $order->id,
            'driver_id' => Auth::id(),
            'status' => 'picked_up',
            'notes' => 'Paket telah diambil oleh kurir',
            'status_history' => json_encode([
                [
                    'status' => 'picked_up',
                    'timestamp' => now(),
                    'notes' => 'Paket telah diambil oleh kurir'
                ]
            ])
        ]);
    }
    
    // UPDATE LUDWIG WALLET - TAMBAH INI YA! 🚀🔥
    $ludwigWallet = \App\Models\LudwigWallet::where('order_id', $order->id)->first();
    if ($ludwigWallet) {
        $ludwigWallet->update([
            'driver_id' => Auth::id()
        ]);
        
        \Log::info('Driver ID updated in LudwigWallet', [
            'order_id' => $order->id,
            'driver_id' => Auth::id()
        ]);
    }
    
    // Update order status
    $order->update(['status_order' => 'on_delivery']);

    return redirect()->route('driver.delivery.process', $id)
        ->with('success', 'Paket telah diambil dan siap dikirim');
}

    public function updateDeliveryStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:sedang_diantar,menuju_alamat,tiba_di_tujuan',
            'notes' => 'nullable|string'
        ]);
        
        $order = Order::findOrFail($id);
        $alamatLengkap = $order->alamat_lengkap . ', ' . $order->kecamatan . ', ' . $order->kota . ', ' . $order->provinsi . ' ' . $order->kode_pos;
        
        // Cari delivery record yang sudah ada
        $delivery = DeliveryHistory::where('order_id', $id)
            ->where('driver_id', Auth::id())
            ->first();
            
        if (!$delivery) {
            // Buat record baru kalau belum ada
            $delivery = DeliveryHistory::create([
                'order_id' => $order->id,
                'driver_id' => Auth::id(),
                'status' => $request->status,
                'notes' => $request->notes ?? 'Kurir sedang dalam perjalanan',
                'status_history' => json_encode([
                    [
                        'status' => $request->status,
                        'timestamp' => now(),
                        'notes' => $request->notes ?? 'Kurir sedang dalam perjalanan'
                    ]
                ])
            ]);
        } else {
            // Update record yang sudah ada
            $delivery->update([
                'status' => $request->status,
                'notes' => $request->notes ?? 'Kurir sedang dalam perjalanan',
                'status_history' => DB::raw("JSON_ARRAY_APPEND(COALESCE(status_history, JSON_ARRAY()), '$', JSON_OBJECT('status', '".$request->status."', 'timestamp', '".now()."', 'notes', '".$request->notes ?? 'Kurir sedang dalam perjalanan'."'))")
            ]);
        }
        
        // Update status order sesuai dengan status delivery yang baru
        $statusOrder = 'on_delivery'; // Default
        
        if ($request->status == 'tiba_di_tujuan') {
            $statusOrder = 'arrived';
        }
        
        $order->update(['status_order' => $statusOrder]);
        
        return redirect()->route('driver.delivery.process', $id)
            ->with('success', 'Status pengiriman telah diperbarui');
    }

    public function completeDelivery(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:tiba_di_tujuan,gagal',
            'notes' => 'nullable|string',
            'photo_proof' => 'required|image|max:2048'
        ]);
        
        $order = Order::findOrFail($id);
        $alamatLengkap = $order->alamat_lengkap . ', ' . $order->kecamatan . ', ' . $order->kota . ', ' . $order->provinsi . ' ' . $order->kode_pos;
        
        // Upload bukti foto
        $photoPath = null;
        if ($request->hasFile('photo_proof')) {
            $photoPath = $request->file('photo_proof')->store('delivery_proofs', 'public');
        }
        
        // Cari delivery record yang sudah ada
        $delivery = DeliveryHistory::where('order_id', $id)
            ->where('driver_id', Auth::id())
            ->first();
            
        $finalStatus = $request->status == 'tiba_di_tujuan' ? 'delivered' : 'delivery_failed';
        
        if (!$delivery) {
            // Buat record baru kalau belum ada
            $delivery = DeliveryHistory::create([
                'order_id' => $order->id,
                'driver_id' => Auth::id(),
                'status' => $finalStatus,
                'notes' => $request->notes,
                'photo_proof' => $photoPath,
                'delivered_at' => now(),
                'status_history' => json_encode([
                    [
                        'status' => $finalStatus,
                        'timestamp' => now(),
                        'notes' => $request->notes,
                        'photo' => $photoPath
                    ]
                ])
            ]);
        } else {
            // Update record yang sudah ada
            $delivery->update([
                'status' => $finalStatus,
                'notes' => $request->notes,
                'photo_proof' => $photoPath,
                'delivered_at' => now(),
                'status_history' => DB::raw("JSON_ARRAY_APPEND(COALESCE(status_history, JSON_ARRAY()), '$', JSON_OBJECT('status', '".$finalStatus."', 'timestamp', '".now()."', 'notes', '".$request->notes."', 'photo', '".$photoPath."'))")
            ]);
        }
        
        // Update order status
        $order->update(['status_order' => $finalStatus]);
        
        \Log::info('Complete delivery dipanggil', $request->all());
        
        return redirect()->route('driver.dashboard')
            ->with('success', 'Pengiriman telah ' . ($request->status == 'tiba_di_tujuan' ? 'selesai' : 'gagal') . ' dan dicatat');
    }           

    public function deliveryHistory(Request $request)
    {
        $query = DeliveryHistory::with('order')
            ->where('driver_id', Auth::id())
            ->orderBy('updated_at', 'desc');
        
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('order', function($q) use ($search) {
                $q->where('nomor_resi', 'like', "%{$search}%");
            })->orWhere('notes', 'like', "%{$search}%");
        }
        
        // Apply date filters
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('updated_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('updated_at', '<=', $request->end_date);
        }
        
        // Apply courier filter
        if ($request->has('courier') && !empty($request->courier)) {
            $query->whereHas('order', function($q) use ($request) {
                $q->where('shipping_kurir', $request->courier);
            });
        }
        
        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        $histories = $query->paginate(10);
        
        return view('driver.delivery_history', compact('histories'));
    }
    
    public function deliveryHistoryDetail($id)
    {
        $history = DeliveryHistory::with(['order', 'order.user', 'order.user.addresses'])
            ->findOrFail($id);

        // Check if the logged-in driver is the owner of this delivery history
        if ($history->driver_id !== Auth::id()) {
            return redirect()->route('driver.delivery.history')
                ->with('error', 'Anda tidak memiliki akses ke data pengiriman ini');
        }

        return view('driver.history.index', compact('history'));
    }
    
    public function exportDeliveryHistory(Request $request)
    {
        $type = $request->type ?? 'excel';
        $fileName = 'delivery_history_' . date('Y-m-d') . ($type == 'excel' ? '.xlsx' : '.pdf');
        
        $query = DeliveryHistory::with('order')
            ->where('driver_id', Auth::id())
            ->orderBy('updated_at', 'desc');
            
        // Apply the same filters as in the view
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('order', function($q) use ($search) {
                $q->where('nomor_resi', 'like', "%{$search}%");
            })->orWhere('notes', 'like', "%{$search}%");
        }
        
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('updated_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('updated_at', '<=', $request->end_date);
        }
        
        if ($request->has('courier') && !empty($request->courier)) {
            $query->whereHas('order', function($q) use ($request) {
                $q->where('shipping_kurir', $request->courier);
            });
        }
        
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        $histories = $query->get();
        
        if ($type == 'excel') {
            return Excel::download(new DeliveryHistoryExport($histories), $fileName);
        } else {
            $pdf = PDF::loadView('exports.delivery_history_pdf', compact('histories'));
            return $pdf->download($fileName);
        }
    }

    public function profile()
    {
        $driver = Auth::user();
        return view('driver.profile', compact('driver'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $driver = Auth::user();

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $driver->updateProfilePhoto($request->file('profile_photo'));
        }

        // Update other profile information
        $driver->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ]);

        return redirect()
            ->route('driver.profile')
            ->with('success', 'Profile updated successfully');
    }

    public function customers(Request $request)
    {
        $deliveries = DeliveryHistory::with(['order', 'order.user'])
            ->where('driver_id', Auth::id())
            ->whereHas('order', function($query) {
                $query->whereIn('status_order', ['on_delivery', 'completed']);
            })
            ->latest()
            ->paginate(10);

        return view('driver.delivery_costumers', compact('deliveries'));
    }
}