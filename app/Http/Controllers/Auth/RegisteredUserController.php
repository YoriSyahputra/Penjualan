<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Address;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse 
{
    \Log::info('Registration data:', $request->all());
    
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'alamat_lengkap' => ['required', 'string'],
        'provinsi' => ['required', 'string'],
        'kota' => ['required', 'string'],
        'kecamatan' => ['required', 'string'],
        'kode_pos' => ['required', 'string'],
        'phone_number' => ['required', 'string'],
    ]);
    
    // Membuat user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'phone_number' => $request->phone_number,
        'is_driver' => $request->has('is_driver'),
    ]);
    
    // Membuat alamat
    $user->addresses()->create([
        'label' => $request->label ?? 'Rumah',
        'alamat_lengkap' => $request->alamat_lengkap,
        'provinsi' => $request->provinsi,
        'kota' => $request->kota,
        'kecamatan' => $request->kecamatan,
        'kode_pos' => $request->kode_pos,
        'is_primary' => true,
    ]);
    
    // Membuat wallet untuk user
    $user->wallet()->create([
        'balance' => 0,
    ]);
    
    // Jika user ingin menjadi driver, buat driver wallet juga
    if ($request->has('is_driver')) {
        // Buat driver wallet
        \DB::table('driver_wallets')->insert([
            'user_id' => $user->id,
            'driver_id' => $user->id,
            'balance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    event(new Registered($user));
    Auth::login($user);
    
    return redirect(RouteServiceProvider::HOME);
}
}
