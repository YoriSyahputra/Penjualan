<!-- resources/views/auth/register.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Register</title>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/bootstrap.js'])
</head>
<body class="bg-gray-100">
    
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8"
         x-data="{ showContent: false }"
         x-init="setTimeout(() => showContent = true, 100)">
        <div class="max-w-md w-full space-y-8 bg-white p-4 sm:p-6 lg:p-8 rounded-2xl shadow-2xl"
             x-show="showContent"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100">
            
            <!-- Logo -->
            <div class="flex justify-center">
                <div class="animate-bounce">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>  

            <!-- Title -->
            <div class="text-center">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">
                    Create your account
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Already have an account?
                    <a href="/login" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-200">
                        Sign in here
                    </a>
                </p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </h3>
                    </div>
                </div>
            </div>
            @endif

            <!-- Registration Form -->
            <form class="mt-8 space-y-4 sm:space-y-6" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">                
                @csrf
                <div class="rounded-md shadow-sm space-y-4">
                    <!-- First Name -->
                    <div>
                        <label for="name" class="sr-only">Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                                class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 text-sm sm:text-base transition-all duration-200"
                                placeholder="Name">
                        </div>
                    </div>
                    <!-- Email -->
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 text-sm sm:text-base transition-all duration-200"
                                placeholder="Email address">
                        </div>
                    </div>
                    <div>
                        <label for="phone_number" class="sr-only">Phone Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input id="phone_number" name="phone_number" type="phone_number" value="{{ old('phone_number') }}" required
                                class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 text-sm sm:text-base transition-all duration-200"
                                placeholder="Phone Number" minlength="8" maxlength="13">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" required
                                class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 text-sm sm:text-base transition-all duration-200"
                                placeholder="Password">
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="sr-only">Confirm Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 text-sm sm:text-base transition-all duration-200"
                                placeholder="Confirm Password">
                        </div>
                    </div>
                    <div class="flex items-center">
                        <input id="is_driver" name="is_driver" type="checkbox" value="1" 
                            class="h-6 w-6 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                        <label for="is_driver" class="ml-3 block text-base text-gray-900">
                            Driver
                        </label>
                    </div>

                    <!-- Profile Photo -->
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Profile Photo (Required)</label>
                        <div class="mt-1 flex items-center">
                            <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100">
                                <img id="preview" src="" alt="" class="w-full h-full object-cover hidden">
                                <div id="placeholder" class="w-full h-full flex items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <input type="file" id="photo" name="photo" accept="image/*" class="hidden" required onchange="previewImage(this)">
                                <button type="button" onclick="document.getElementById('photo').click()" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Upload Photo
                                </button>
                                <p class="mt-2 text-xs text-gray-500">JPG, PNG, GIF up to 1MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Address Steps -->
                    <div x-data="{ 
                        step: 1,
                        label: '',
                        alamat_lengkap: '',
                        provinsi: '',
                        kota: '',
                        kecamatan: '',
                        kode_pos: ''
                    }">
                        <div class="text-gray-500 px-4 mb-2">
                            Alamat <span x-text="step"></span>/6
                        </div>
                        
                        <!-- Label -->
                        <div x-show="step === 1" class="flex items-center space-x-2">
                            <div class="flex-grow">
                                <input x-model="label" type="text" name="label" placeholder="Masukan Label Sesukamu" value="{{ old('label') }}" class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 text-sm sm:text-base transition-all duration-200">
                            </div>
                            <button type="button" @click="step = 2" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Next
                            </button>
                        </div>

                        <!-- Alamat Lengkap -->
                        <div x-show="step === 2" class="flex items-center space-x-2">
                            <div class="flex-grow">
                                <input x-model="alamat_lengkap" type="text" name="alamat_lengkap" placeholder="Alamat Lengkap" value="{{ old('alamat_lengkap') }}" class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 text-sm sm:text-base transition-all duration-200">
                            </div>
                            <button type="button" @click="step = 3" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Next
                            </button>
                        </div>

                        <!-- Provinsi -->
                        <div x-show="step === 3" class="flex items-center space-x-2">
                            <div class="flex-grow">
                                <input x-model="provinsi" type="text" name="provinsi" placeholder="Provinsi" value="{{ old('provinsi') }}" class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 text-sm sm:text-base transition-all duration-200">
                            </div>
                            <button type="button" @click="step = 4" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Next
                            </button>
                        </div>

                        <!-- Kota -->
                        <div x-show="step === 4" class="flex items-center space-x-2">
                            <div class="flex-grow">
                                <input x-model="kota" type="text" name="kota" placeholder="Kota" value="{{ old('kota') }}"class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 text-sm sm:text-base transition-all duration-200">
                            </div>
                            <button type="button" @click="step = 5" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Next
                            </button>
                        </div>

                        <!-- Kecamatan -->
                        <div x-show="step === 5" class="flex items-center space-x-2">
                            <div class="flex-grow">
                                <input x-model="kecamatan" type="text" name="kecamatan" placeholder="Kecamatan" value="{{ old('kecamatan') }}"class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 text-sm sm:text-base transition-all duration-200">
                            </div>
                            <button type="button" @click="step = 6" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Next
                            </button>
                        </div>

                        <!-- Kode Pos -->
                        <div x-show="step === 6" class="flex items-center space-x-2">
                            <div class="flex-grow">
                                <input x-model="kode_pos" type="number" name="kode_pos" placeholder="Kode Pos" value="{{ old('kode_pos') }}"class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 text-sm sm:text-base transition-all duration-200">
                            </div>
                            <button type="button" @click="step = 1" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                Done
                            </button>
                        </div>
                    </div>

                    <!-- Add this script at the end of your body tag -->
                    <script>
                        function previewImage(input) {
                            if (input.files && input.files[0]) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    document.getElementById('preview').src = e.target.result;
                                    document.getElementById('preview').classList.remove('hidden');
                                    document.getElementById('placeholder').classList.add('hidden');
                                }
                                reader.readAsDataURL(input.files[0]);
                            }
                        }
                    </script>

                </div>

                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" required
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                    <label for="terms" class="ml-2 block text-sm text-gray-900">
                        I agree to the 
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">Terms of Service</a>
                        and
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">Privacy Policy</a>
                    </label>
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400 transition-colors duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        Create Account
                    </button>
                </div>

                <a href="/"
                        class="group relative w-full flex justify-center py-2 px-4 border border-indigo-600 text-sm font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-indigo-600 group-hover:text-indigo-500 transition-colors duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 12H6m8 0h4m-2 2v-4m-6 8h8a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2h4" />
                            </svg>
                        </span>
                        Go to Landing
                    </a>
            </form>
        </div>
    </div>
</body>
</html>