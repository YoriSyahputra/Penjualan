<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;

class StoreController extends Controller
{

    public function index()
    {
        return view('ecom.home');
    }

    public function create()
    {
        return view('ecom.buka_toko');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
    
        $store = new Store($validated);
        $store->user_id = auth()->id();
        $store->status = 'pending';
    
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('store-logos', 'public');
            $store->logo = $path;
        }
    
        $store->save();
    
        // Move the logging after store creation
        \Log::info('Store created debug', [
            'store' => $store,
            'user_store' => auth()->user()->store,
            'user_id' => auth()->id()
        ]);
    
        auth()->user()->update([
            'admin_status' => 'pending'
        ]);
    
        return redirect()->route('profile.edit')
            ->with('success', 'Your store has been created and is pending approval.');
    }       
}