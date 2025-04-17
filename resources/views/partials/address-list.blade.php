@forelse($addresses as $address)
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
                <p class="text-gray-600 mt-2">{{ $address->alamat_lengkap }}</p>
                <p class="text-gray-600">{{ $address->kecamatan }}, {{ $address->kota }}</p>
                <p class="text-gray-600">{{ $address->provinsi }}, {{ $address->kode_pos }}</p>
            </div>
            <div class="flex gap-2">
                @if(!$address->is_primary)
                    <form action="{{ route('profile.address.priup', $address) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-indigo-600 hover:text-indigo-800 transition-colors">
                            Jadikan Utama
                        </button>
                    </form>
                @endif
                <button onclick="deleteAddress('{{ route('profile.address.delup', $address) }}')" 
                        class="text-red-600 hover:text-red-800 transition-colors ml-2">
                    Hapus
                </button>
            </div>
        </div>
    </div>
@empty
    <p class="text-gray-500 text-center py-4">Belum ada alamat yang ditambahkan</p>
@endforelse
