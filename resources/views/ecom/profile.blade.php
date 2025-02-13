@extends('layouts.depan')

@section('content')
<div class="min-h-screen pt-16 bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-lg p-6 md:p-8">
                <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6">
                    <div class="relative group">
                        <img src="{{ auth()->user()->profile_photo_url ?? '/api/placeholder/128/128' }}" 
                             alt="Profile photo" 
                             class="w-32 h-32 rounded-full object-cover border-4 border-white">
                        
                        <!-- Photo Upload Overlay -->
                        <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" 
                              class="absolute inset-0 rounded-full bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            @csrf
                            <input type="file" name="photo" id="photo" class="hidden" onchange="this.form.submit()">
                            <label for="photo" class="cursor-pointer text-white p-2 rounded-full hover:bg-white hover:bg-opacity-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </label>
                        </form>
                    </div>
                    <div class="text-center md:text-left">
                        <h1 class="text-2xl md:text-3xl font-bold text-white">{{ auth()->user()->name }}</h1>
                        <p class="text-indigo-100">Member since {{ auth()->user()->created_at->format('F Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Profile Info Card -->
                <div class="bg-white border-l-4 border-indigo-500 p-4 rounded-lg shadow-md transform transition-all duration-300 hover:scale-[1.02] mb-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-gray-800">Informasi Pribadi</h4>
                            </div>
                            <div class="mt-3 space-y-2">
                                <p class="text-gray-600">Nama: <span class="font-medium text-gray-800">{{ auth()->user()->name }}</span></p>
                                <p class="text-gray-600">Email: <span class="font-medium text-gray-800">{{ auth()->user()->email }}</span></p>
                                <p class="text-gray-600">Jenis Kelamin: <span class="font-medium text-gray-800">
                                    @if(auth()->user()->gender == 'male')
                                        Laki-laki
                                    @elseif(auth()->user()->gender == 'female')
                                        Perempuan
                                    @else
                                        Tidak ingin memberitahu
                                    @endif
                                </span></p>
                                <p class="text-gray-600">Nomor Telepon: <span class="font-medium text-gray-800">{{ auth()->user()->phone_number }}</span></p>
                                <p class="text-gray-600">Alamat: <span class="font-medium text-gray-800">{{ auth()->user()->address }}</span></p>
                            </div>
                        </div>
                        <button onclick="openEditProfileModal()" 
                                class="text-indigo-600 hover:text-indigo-800 transition-colors">
                            Edit
                        </button>
                    </div>
                </div>
                <div id="editProfileModal" 
                    class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full transform transition-all duration-300 ease-in-out opacity-0"
                    style="z-index: 1000;">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white transform transition-all duration-300 ease-in-out scale-95">
                        <div class="mt-3">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Edit Informasi Pribadi</h3>
                                <button onclick="closeEditProfileModal()" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                                        <input type="text" name="name" id="name" required
                                            value="{{ old('name', auth()->user()->name) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" name="email" id="email" required
                                            value="{{ old('email', auth()->user()->email) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                                        <select name="gender" id="gender" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="male" {{ auth()->user()->gender == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="female" {{ auth()->user()->gender == 'female' ? 'selected' : '' }}>Perempuan</option>
                                            <option value="prefer_not_to_say" {{ auth()->user()->gender == 'prefer_not_to_say' ? 'selected' : '' }}>Tidak ingin memberitahu</option>
                                        </select>
                                        @error('gender')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                        <input type="tel" name="phone_number" id="phone_number" required
                                            value="{{ old('phone_number', auth()->user()->phone_number) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('phone_number')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                                        <textarea name="address" id="address" rows="3" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', auth()->user()->address) }}</textarea>
                                        @error('address')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mt-5 flex justify-end gap-3">
                                    <button type="button" onclick="closeEditProfileModal()"
                                            class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200 transition-colors">
                                        Batal
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                        <!-- Store Section -->
                        @if(auth()->user()->store)
                            <div class="md:col-span-2 bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Informasi Toko</h3>
                                    <span class="px-3 py-1 text-sm rounded-full 
                                        @if(auth()->user()->admin_status === 'approved') bg-green-100 text-green-800
                                        @elseif(auth()->user()->admin_status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst(auth()->user()->admin_status) }}
                                    </span>
                                </div>
                                <!-- Store Details -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Nama Toko</p>
                                        <p class="font-medium">{{ auth()->user()->store->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Kategori</p>
                                        <p class="font-medium">{{ auth()->user()->store->category }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Telepon Toko</p>
                                        <p class="font-medium">{{ auth()->user()->store->phone_number }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Alamat Toko</p>
                                        <p class="font-medium">{{ auth()->user()->store->address }}</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <p class="text-sm text-gray-600">Deskripsi</p>
                                        <p class="font-medium">{{ auth()->user()->store->description }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="md:col-span-2 mt-4">
                                <a href="{{ route('store.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Buka Toko
                                </a>
                            </div>
                        @endif

                        <!-- Addresses Section -->
                        <div class="md:col-span-2 mt-6">
                            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                                <div class="p-6 bg-gradient-to-r from-indigo-100 to-purple-100 border-b border-gray-200">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold text-gray-800">Alamat Saya</h3>
                                        @if(auth()->user()->addresses()->count() < 5)
                                            <button type="button" onclick="openAddressModal()"
                                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Tambah Alamat
                                            </button>
                                        @endif
                                    </div>

                                    <div id="addressList" class="space-y-4">
                                        @forelse(auth()->user()->addresses as $address)
                                            <div class="bg-white border-l-4 {{ $address->is_primary ? 'border-indigo-500' : 'border-gray-300' }} p-4 rounded-lg shadow-md transform transition-all duration-300 hover:scale-[1.02]">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <h4 class="font-bold text-gray-800">{{ $address->label }}</h4>
                                                            @if($address->is_primary)
                                                                <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-semibold">
                                                                    Utama
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <p class="text-gray-600 mt-1">{{ $address->recipient_name }}</p>
                                                        <p class="text-gray-600">{{ $address->phone_number }}</p>
                                                        <p class="text-gray-600 mt-2">{{ $address->address }}</p>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        @if(!$address->is_primary)
                                                            <form action="{{ route('profile.address.primary', $address) }}" method="POST" class="inline">
                                                                @csrf
                                                                <button type="submit" class="text-indigo-600 hover:text-indigo-800 transition-colors">
                                                                    Jadikan Utama
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <button onclick="deleteAddress('{{ route('profile.address.delete', $address) }}')" 
                                                                class="text-red-600 hover:text-red-800 transition-colors ml-2">
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-gray-500 text-center py-4">Belum ada alamat yang ditambahkan</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div id="addAddressModal" 
     class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full transform transition-all duration-300 ease-in-out opacity-0"
     style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white transform transition-all duration-300 ease-in-out scale-95">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambah Alamat Baru</h3>
                <button onclick="closeAddressModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="addAddressForm" action="{{ route('profile.address.add') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="label" class="block text-sm font-medium text-gray-700">Label Alamat</label>
                        <input type="text" name="label" id="label" required placeholder="contoh: Rumah, Kantor"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="recipient_name" class="block text-sm font-medium text-gray-700">Nama Penerima</label>
                        <input type="text" name="recipient_name" id="recipient_name" required placeholder="Nama lengkap penerima"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <input type="text" name="phone_number" id="phone_number" required placeholder="Format: 08xxxxxxxxxx"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                        <textarea name="address" id="address" rows="3" required placeholder="Masukkan alamat lengkap"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                <div class="mt-5 flex justify-end gap-3">
                    <button type="button" onclick="closeAddressModal()"
                            class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                        Tambah Alamat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Success message fade out
    setTimeout(function() {
        const successMessage = document.getElementById('successMessage');
        if (successMessage) {
            successMessage.style.opacity = '0';
            setTimeout(() => successMessage.remove(), 500);
        }
    }, 3000);

    // Setup form submission
    const addAddressForm = document.getElementById('addAddressForm');
    addAddressForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const result = await response.json();

            if (result.success) {
                // Refresh address list without page reload
                const addressList = document.getElementById('addressList');
                addressList.innerHTML = result.html;
                
                // Close modal with animation
                closeAddressModal();
                
                // Show success message
                showSuccessMessage('Alamat berhasil ditambahkan!');
                
                // Reset form
                this.reset();
            }
        } catch (error) {
            console.error('Error:', error);
            showSuccessMessage('Terjadi kesalahan, silakan coba lagi.', 'error');
        }
    });
});

function openAddressModal() {
    const modal = document.getElementById('addAddressModal');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.querySelector('.relative').classList.add('scale-100');
    }, 10);
}

function closeAddressModal() {
    const modal = document.getElementById('addAddressModal');
    modal.classList.remove('opacity-100');
    modal.querySelector('.relative').classList.remove('scale-100');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
function openEditProfileModal() {
    const modal = document.getElementById('editProfileModal');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.querySelector('.relative').classList.add('scale-100');
    }, 10);
}

function closeEditProfileModal() {
    const modal = document.getElementById('editProfileModal');
    modal.classList.remove('opacity-100');
    modal.querySelector('.relative').classList.remove('scale-100');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
async function deleteAddress(url) {
    if (confirm('Apakah Anda yakin ingin menghapus alamat ini?')) {
        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const result = await response.json();

            if (result.success) {
                // Refresh address list without page reload
                const addressList = document.getElementById('addressList');
                addressList.innerHTML = result.html;
                showSuccessMessage('Alamat berhasil dihapus!');
            }
        } catch (error) {
            console.error('Error:', error);
            showSuccessMessage('Terjadi kesalahan saat menghapus alamat.', 'error');
        }
    }
}

function showSuccessMessage(message, type = 'success') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `fixed top-4 right-4 px-6 py-3 rounded-lg transition-all transform translate-y-0 opacity-100 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white`;
    messageDiv.textContent = message;
    
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.style.opacity = '0';
        messageDiv.style.transform = 'translateY(-100%)';
        setTimeout(() => messageDiv.remove(), 500);
    }, 3000);
}
</script>
@endsection