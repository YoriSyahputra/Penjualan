<?php

namespace App\Http\Controllers;

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
}