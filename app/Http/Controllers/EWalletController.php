<?php

namespace App\Http\Controllers;

use App\Models\LudwigPayment;
use App\Models\Wallet;
use App\Models\WalletTransfer;
use App\Models\User;
use App\Models\Pin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EWalletController extends Controller
{
    public function createPin(Request $request)
    {
        // Validasi input, pastikan ada field "pin" dan "pin_confirmation"
        $request->validate([
            'pin' => 'required|digits:6|confirmed'
        ]);
    
        try {
            $user = auth()->user();
    
            // Jika user sudah punya PIN, bisa gunakan updateOrCreate agar tidak terjadi duplikasi
            Pin::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'pin'             => bcrypt($request->pin),
                    'last_changed_at' => now(),
                    'attempts'        => 0,
                    'is_locked'       => false,
                    'locked_until'    => null
                ]
            );
    
            // Pastikan kolom "has_pin" ada di tabel users.
            // Jika tidak ada, kamu bisa:
            //   a. Membuat migration untuk menambahkan kolom tersebut
            //   b. Atau hapus/update logika ini jika tidak diperlukan
            $user->update(['has_pin' => true]);
    
            return response()->json([
                'success' => true,
                'message' => 'PIN created successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('PIN creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to create PIN: ' . $e->getMessage()
            ], 500);
        }
    }
        public function showPayment()
    {
        $total_amount = session('cart_total', 0);
        return view('payment.ewallet', compact('total_amount'));
    }
    public function searchUsers(Request $request)
    {
        $query = $request->get('q');
        
        $users = User::where('id', '!=', auth()->id())
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get(['id', 'name', 'email', 'profile_photo']);

        return response()->json($users);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'amount'       => 'required|numeric|min:1000',
            'notes'        => 'nullable|string|max:255',
            'pin'          => 'required|digits:6'
        ]);
    
        $sender    = auth()->user();
        $recipient = User::find($request->recipient_id);
        $amount    = $request->amount;
    
        // Periksa apakah user sudah memiliki record PIN
        $pinRecord = Pin::where('user_id', $sender->id)->first();
        if (!$pinRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Please set up your PIN first'
            ], 400);
        }
    
        // Periksa apakah PIN sedang terkunci
        if ($pinRecord->is_locked && now()->lessThan($pinRecord->locked_until)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN has been locked due to too many attempts. Please try again later.'
            ], 400);
        }
    
        // Verifikasi PIN
        if (!Hash::check($request->pin, $pinRecord->pin)) {
            // Tambah jumlah percobaan gagal
            $pinRecord->increment('attempts');
    
            // Kunci PIN jika sudah melebihi batas percobaan
            if ($pinRecord->attempts >= 3) {
                $pinRecord->update([
                    'is_locked'    => true,
                    'locked_until' => now()->addMinutes(30)
                ]);
    
                return response()->json([
                    'success' => false,
                    'message' => 'PIN has been locked due to too many attempts. Please try again in 30 minutes.'
                ], 400);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'Invalid PIN. Please try again.'
            ], 400);
        }
    
        // Reset jumlah percobaan setelah verifikasi berhasil
        $pinRecord->update(['attempts' => 0]);
    
        // Periksa saldo
        if ($sender->wallet->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }
    
        try {
            $transfer = DB::transaction(function () use ($sender, $recipient, $amount, $request) {
                // Process sender and recipient wallet balance updates
                $sender->wallet->decrement('balance', $amount);
                $recipient->wallet->increment('balance', $amount);
        
                // Record the transfer and return the created instance
                return WalletTransfer::create([
                    'sender_id'    => $sender->id,
                    'recipient_id' => $recipient->id,
                    'amount'       => $amount,
                    'notes'        => $request->notes
                ]);
            });        
            return response()->json([
                'success'  => true,
                'message'  => 'Transfer completed successfully',
                'redirect' => route('ewallet.transfer.success', ['transfer' => $transfer->id])
            ]);        
        } catch (\Exception $e) {
            \Log::error('Transfer failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Transfer failed: ' . $e->getMessage()
            ], 500);
        }        
    }
    public function transferSuccess(WalletTransfer $transfer)
{
    // Ensure the user can only view their own transfers
    if ($transfer->sender_id !== auth()->id()) {
        abort(403);
    }
    
    return view('payment.transfer-successfully', compact('transfer'));
}
    public function showSearch()
    {
        $recentTransfers = WalletTransfer::where('sender_id', auth()->id())
            ->with('recipient')
            ->latest()
            ->take(5)
            ->get();
        
        return view('payment.transfer-search', compact('recentTransfers'));
    }

    public function showTransferAmount(User $recipient)
    {
        return view('payment.transfer-amount', compact('recipient'));
    }
    public function processPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:1000',
            'pin' => 'required|digits:6'
        ]);

        $user = auth()->user();
        $order = Order::findOrFail($request->order_id);
        $amount = $request->amount;

        // Verify PIN
        $pinRecord = Pin::where('user_id', $user->id)->first();
        if (!$pinRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Please set up your PIN first'
            ], 400);
        }

        // Check if PIN is locked
        if ($pinRecord->is_locked && now()->lessThan($pinRecord->locked_until)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN has been locked. Please try again later.'
            ], 400);
        }

        // Verify PIN
        if (!Hash::check($request->pin, $pinRecord->pin)) {
            $pinRecord->increment('attempts');

            if ($pinRecord->attempts >= 3) {
                $pinRecord->update([
                    'is_locked' => true,
                    'locked_until' => now()->addMinutes(30)
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'PIN has been locked. Please try again in 30 minutes.'
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid PIN'
            ], 400);
        }

        // Reset attempts after successful verification
        $pinRecord->update(['attempts' => 0]);

        // Check balance
        if ($user->wallet->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        try {
            DB::transaction(function () use ($user, $order, $amount) {
                // Update wallet balance
                $user->wallet->decrement('balance', $amount);
                
                // Update order status
                $order->update([
                    'status' => 'paid',
                    'payment_method' => 'ewallet',
                    'paid_at' => now()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment successful',
                'redirect' => route('order.confirmation', ['order' => $order->id])
            ]);
        } catch (\Exception $e) {
            \Log::error('Payment failed', [ 
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage()
            ], 500);
        }
    }

}
