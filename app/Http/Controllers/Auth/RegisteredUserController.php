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
use DB;

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
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'alamat_lengkap' => ['required', 'string'],
                'provinsi' => ['required', 'string'],
                'kota' => ['required', 'string'],
                'kecamatan' => ['required', 'string'],
                'kode_pos' => ['required', 'string'],
                'phone_number' => ['required', 'string'],
                'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:1024'],
            ]);

            DB::beginTransaction();

            // Create user with basic info
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'is_driver' => $request->has('is_driver'),
            ]);

            // Handle profile photo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('profile-photos', 'public');
                $user->profile_photo_path = $path;
                $user->save();
            }

            // Create address
            $user->addresses()->create([
                'label' => $request->label ?? 'Rumah',
                'alamat_lengkap' => $request->alamat_lengkap,
                'provinsi' => $request->provinsi,
                'kota' => $request->kota,
                'kecamatan' => $request->kecamatan,
                'kode_pos' => $request->kode_pos,
                'is_primary' => true,
            ]);
            
            // Create wallet
            $user->wallet()->create([
                'balance' => 0,
            ]);
            
            if ($request->has('is_driver')) {
                \DB::table('driver_wallets')->insert([
                    'user_id' => $user->id,
                    'driver_id' => $user->id,
                    'balance' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            
            event(new Registered($user));
            Auth::login($user);
            
            return redirect(RouteServiceProvider::HOME);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Registration error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }
}
