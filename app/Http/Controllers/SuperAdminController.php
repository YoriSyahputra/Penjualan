<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
}