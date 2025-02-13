<?php

namespace App\Http\Controllers;

use App\Models\Address;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('ecom.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'gender' => ['required', 'in:male,female,prefer_not_to_say'],
        ]);

        Auth::user()->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:1024'],
        ]);

        // Hapus foto lama jika ada
        if (Auth::user()->profile_photo_path) {
            Storage::delete(Auth::user()->profile_photo_path);
        }

        $path = $request->file('photo')->store('profile-photos');
        Auth::user()->update(['profile_photo_path' => $path]);

        return back()->with('success', 'Profile photo updated successfully!');
    }
    public function addAddress(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'address' => 'required|string',
            'recipient_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);
    
        if (Auth::user()->addresses()->count() >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya dapat menambahkan maksimal 5 alamat.'
            ]);
        }
    
        try {
            $address = new Address($validated);
            $address->user_id = Auth::id();
            
            if (Auth::user()->addresses()->count() === 0) {
                $address->is_primary = true;
            }
            
            $address->save();
    
            if ($request->ajax()) {
                $addresses = Auth::user()->addresses()->get();
                $html = view('partials.address-list', compact('addresses'))->render();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Alamat berhasil ditambahkan!',
                    'html' => $html
                ]);
            }
    
            return back()->with('success', 'Alamat berhasil ditambahkan!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan alamat.'
                ]);
            }
            return back()->with('error', 'Terjadi kesalahan saat menyimpan alamat.');
        }
    }

    public function setPrimaryAddress(Request $request, Address $address)
    {
        // Ensure the address belongs to the user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        Auth::user()->addresses()->update(['is_primary' => false]);
        $address->update(['is_primary' => true]);

        return back()->with('success', 'Primary address updated successfully!');
    }

    public function deleteAddress(Address $address)
    {
        // Ensure the address belongs to the user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $was_primary = $address->is_primary;
        $address->delete();

        // If we deleted the primary address, make another one primary
        if ($was_primary) {
            $new_primary = Auth::user()->addresses()->first();
            if ($new_primary) {
                $new_primary->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Address deleted successfully!');
    }

}