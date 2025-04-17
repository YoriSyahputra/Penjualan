<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TopUp;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\DriverWallet;
use App\Models\DeliveryHistory;
use App\Models\Wallet;
use App\Models\SellerWallet;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function index()
    {
        $pendingAdmins = User::where('admin_status', 'pending')->get();
        $approvedAdmins = User::where('admin_status', 'approved')->get();
        
        return view('super-admin.dashboard', compact('pendingAdmins', 'approvedAdmins'));
    }

    public function approveAdmin(User $user)
    {
        // Generate admin number
        $lastUser = User::whereNotNull('is_admin')
                        ->where('is_admin', 'like', 'ADM%')
                        ->orderBy('is_admin', 'desc')
                        ->first();
        
        if ($lastUser) {
            // Extract number from last admin number and increment
            $lastNumber = intval(substr($lastUser->is_admin, 3));
            $newNumber = $lastNumber + 1;
        } else {
            // If no existing admin, start from 1001
            $newNumber = 1001;
        }
        
        // Format new admin number
        $adminNumber = 'ADM' . $newNumber;

        // Update user status
        $user->update([
            'is_admin' => $adminNumber, // Menggunakan is_admin untuk menyimpan nomor admin
            'admin_status' => 'approved'
        ]);

        if ($user->store) {
            $user->store->update([
                'status' => 'approved'
            ]);
        }

        return redirect()->route('super-admin.dashboard')
            ->with('success', 'Admin application approved successfully');
    }
    
    public function rejectAdmin(User $user)
    {
        $user->update([
            'is_admin' => false,
            'admin_status' => 'rejected'
        ]);

        return back()->with('success', 'Admin application rejected');
    }

    public function topUpRequests()
    {
        $topUps = TopUp::with('user')
            ->where('status', 'completed')
            ->latest()
            ->paginate(10);
        
        return view('super-admin.top-up-requests', compact('topUps'));
    }

    public function confirmTopUp(TopUp $topUp)
    {
        DB::transaction(function () use ($topUp) {
            // Update top-up status
            $topUp->update(['status' => 'completed']);
            
            // Add balance to user's wallet
            $topUp->user->wallet->increment('balance', $topUp->amount);
        });
        
        return back()->with('success', 'Top up confirmed successfully');
    }

    public function manualTopUp()
    {
        return view('super-admin.manual-top-up');
    }

    public function searchPaymentCode(Request $request)
    {
        $paymentCode = $request->get('payment_code');
        $topUp = TopUp::with('user')
                     ->where('payment_code', $paymentCode)
                     ->where('status', 'pending')
                     ->first();

        return response()->json($topUp);
    }

    public function confirmManualTopUp(Request $request)
    {
        $topUp = TopUp::where('payment_code', $request->payment_code)
                      ->where('status', 'pending')
                      ->firstOrFail();

        DB::transaction(function () use ($topUp) {
            $topUp->update(['status' => 'completed']);
            $topUp->user->wallet->increment('balance', $topUp->amount);
        });

        return view('super-admin.top-up-success', compact('topUp'));
    }

    public function refundHistory(Request $request)
    {
        $query = OrderCancellation::with(['order', 'canceller'])
            ->when($request->search, function($q) use ($request) {
                $q->whereHas('order', function($q) use ($request) {
                    $q->where('order_number', 'like', '%'.$request->search.'%');
                });
            })
            ->latest();
        
        $refunds = $query->paginate(20)->withQueryString();
        
        return view('super-admin.refund-history', compact('refunds'));
    }

    public function orderHistory(Request $request)
    {
        $query = Order::with(['user', 'store'])
            ->when($request->search, function($q) use ($request) {
                $q->where('order_number', 'like', '%'.$request->search.'%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%'.$request->search.'%');
                  });
            })
            ->latest();
        
        $orders = $query->paginate(20)->withQueryString();
        
        return view('super-admin.order-history', compact('orders'));
    }

    public function driverHistory(Request $request)
    {
        $query = User::where('is_driver', true)
            ->withCount(['deliveryHistories'])
            ->with(['driverWallet'])
            ->when($request->search, function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%');
            });
        
        $drivers = $query->paginate(20)->withQueryString();
        
        return view('super-admin.driver-history', compact('drivers'));
    }

    public function sellerHistory(Request $request)
    {
        $query = Store::with(['user', 'sellerWallet'])
            ->withCount('products')
            ->when($request->search, function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%'.$request->search.'%');
                  });
            });
        
        $sellers = $query->paginate(20)->withQueryString();
        
        return view('super-admin.seller-history', compact('sellers'));
    }
}