<!-- Modal Verifikasi PIN untuk Pembayaran Produk -->
<div id="productPinModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-md hidden flex items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-lg p-8 w-96 max-w-[90%]">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Verifikasi PIN</h2>
    <p class="text-gray-600 text-center mb-6">Masukkan PIN 6 digit untuk konfirmasi pembayaran Anda</p>

    <!-- Ringkasan Pembayaran -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 mb-6 border border-blue-200">
      <div class="flex justify-between">
        <span class="text-gray-600">Jumlah:</span>
        <span class="font-semibold text-gray-800">Rp <span id="paymentSummaryAmount">0</span></span>
      </div>
      <div class="flex justify-between mt-3">
        <span class="text-gray-600">Order:</span>
        <span id="orderNumberSummary" class="font-semibold text-gray-800"></span>
      </div>
    </div>

    <input type="password" 
          id="productPinInput" 
          class="w-full text-center text-2xl tracking-widest mb-6 border border-gray-300 rounded py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
          placeholder="******" 
          readonly 
          maxlength="6">

    <!-- Numpad PIN -->
    <div class="grid grid-cols-3 gap-3 mb-6">
      @foreach(range(1, 9) as $num)
      <button type="button" class="product-pin-btn h-14 bg-blue-50 rounded-lg text-xl font-bold text-blue-600 hover:bg-blue-100 transition-colors" data-val="{{ $num }}">{{ $num }}</button>
      @endforeach
      <button type="button" class="h-14 bg-red-50 rounded-lg text-xl font-bold text-red-600 hover:bg-red-100 transition-colors" onclick="clearProductPin()">C</button>
      <button type="button" class="product-pin-btn h-14 bg-blue-50 rounded-lg text-xl font-bold text-blue-600 hover:bg-blue-100 transition-colors" data-val="0">0</button>
      <button type="button" class="h-14 bg-yellow-50 rounded-lg text-xl font-bold text-yellow-600 hover:bg-yellow-100 transition-colors" onclick="deleteProductPin()">⌫</button>
    </div>

    <div class="flex gap-4">
      <button 
        type="button" 
        onclick="cancelProductPin()" 
        class="flex-1 border border-gray-300 bg-gray-200 rounded-lg px-4 py-2 text-gray-800 hover:bg-gray-300 transition duration-200"
      >
        Batal
      </button>
      <button 
        type="button" 
        onclick="confirmProductPin()" 
        class="flex-1 border border-green-600 bg-green-500 rounded-lg px-4 py-2 text-white hover:bg-green-600 transition duration-200"
      >
        Konfirmasi
      </button>
    </div>
  </div>
</div>

<!-- Modal Pembuatan PIN -->
@if(auth()->check() && !auth()->user()->hasPin())
<div id="createProductPinModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-md hidden flex items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-lg p-8 w-96 max-w-[90%]">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Buat PIN Baru</h2>
    <p class="text-gray-600 text-center mb-6">Anda perlu membuat PIN 6 digit untuk bertransaksi</p>
    
    <input type="password" id="newProductPinInput" class="w-full text-center text-2xl tracking-widest mb-6 border border-gray-300 rounded py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="******" readonly maxlength="6">
    
    <!-- Numpad Pembuatan PIN -->
    <div class="grid grid-cols-3 gap-3 mb-6">
      @foreach(range(1, 9) as $num)
      <button type="button" class="new-product-pin-btn h-14 bg-blue-50 rounded-lg text-xl font-bold text-blue-600 hover:bg-blue-100 transition-colors" data-val="{{ $num }}">{{ $num }}</button>
      @endforeach
      <button type="button" class="h-14 bg-red-50 rounded-lg text-xl font-bold text-red-600 hover:bg-red-100 transition-colors" onclick="clearNewProductPin()">C</button>
      <button type="button" class="new-product-pin-btn h-14 bg-blue-50 rounded-lg text-xl font-bold text-blue-600 hover:bg-blue-100 transition-colors" data-val="0">0</button>
      <button type="button" class="h-14 bg-yellow-50 rounded-lg text-xl font-bold text-yellow-600 hover:bg-yellow-100 transition-colors" onclick="deleteNewProductPin()">⌫</button>
    </div>
    
    <button 
      type="button" 
      id="saveProductPinBtn" 
      onclick="saveProductPin()" 
      class="w-full border border-green-600 bg-green-500 rounded-lg px-4 py-3 text-white font-medium hover:bg-green-600 transition duration-200" 
      disabled
    >
      Buat PIN
    </button>
  </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // State management for PIN
  class PinState {
    constructor(inputElement, maxLength = 6) {
      this.value = '';
      this.inputElement = inputElement;
      this.maxLength = maxLength;
    }

    append(digit) {
      if (this.value.length < this.maxLength) {
        this.value += digit;
        this.updateInput();
      }
    }

    clear() {
      this.value = '';
      this.updateInput();
    }

    delete() {
      this.value = this.value.slice(0, -1);
      this.updateInput();
    }

    updateInput() {
      this.inputElement.value = this.value;
    }

    get length() {
      return this.value.length;
    }

    get pin() {
      return this.value;
    }
  }

  // Initialize PIN states
  const productPin = new PinState(document.getElementById('productPinInput'));
  const newProductPin = new PinState(document.getElementById('newProductPinInput'));
  // Elements
  const productPinModal = document.getElementById('productPinModal');

  const createProductPinModal = document.getElementById('createProductPinModal');
  const saveProductPinBtn = document.getElementById('saveProductPinBtn');
  const paymentForm = document.getElementById('paymentForm');

  // Event Listeners for product payment PIN
  document.querySelectorAll('.product-pin-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      productPin.append(btn.getAttribute('data-val'));
    });
  });

  // Event Listeners for new product PIN
  document.querySelectorAll('.new-product-pin-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      newProductPin.append(btn.getAttribute('data-val'));
      saveProductPinBtn.disabled = newProductPin.length !== 6;
    });
  });

  // Product PIN Management Functions
  window.clearProductPin = () => productPin.clear();
  window.deleteProductPin = () => productPin.delete();
  window.clearNewProductPin = () => {
    newProductPin.clear();
    saveProductPinBtn.disabled = true;
  };
  window.deleteNewProductPin = () => {
    newProductPin.delete();
    saveProductPinBtn.disabled = newProductPin.length !== 6;
  };

  window.cancelProductPin = () => {
    productPinModal.classList.add('hidden');
    productPin.clear();
  };
//--------------------------------||pisah||-----------------------------------
  // Show product payment PIN modal
  window.showProductPinModal = () => {
    // Cek apakah user sudah punya PIN
    fetch('/user/has-pin', {
      method: 'GET',
      headers: { 
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      const orderNumber = document.getElementById('orderNumber').textContent;
      const amount = document.getElementById('orderTotal').textContent.replace('Rp ', '');
      
      document.getElementById('paymentSummaryAmount').textContent = amount;
      document.getElementById('orderNumberSummary').textContent = orderNumber;
      
      if (data.hasPin) {
        // Jika sudah punya PIN, tampilkan modal verifikasi PIN
        productPinModal.classList.remove('hidden');
      } else {
        // Jika belum punya PIN, tampilkan modal pembuatan PIN
        createProductPinModal.classList.remove('hidden');
      }
    })
    .catch(error => {
      console.error('Error checking PIN status:', error);
      showAlert('error', 'Error', 'Gagal memeriksa status PIN');
    });
  };

  window.confirmProductPin = () => {
    if (productPin.length !== 6) {
      showAlert('warning', 'PIN tidak valid', 'Masukkan PIN 6 digit.');
      return;
    }

    // Ambil order_id dari form
    const orderId = document.getElementById('orderId').value;
    
    // Create FormData object
    const formData = new FormData();
    formData.append('order_id', orderId);
    formData.append('pin', productPin.pin);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Kirim ke endpoint yang benar
    fetch('/order/payment/process', {
      method: 'POST',
      body: formData,
      headers: { 
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        productPinModal.classList.add('hidden');
        productPin.clear();
        showAlert('success', 'Berhasil', 'Pembayaran berhasil diproses.');
        setTimeout(() => window.location.href = data.redirect || '/dashboard', 1500);
      } else {
        throw new Error(data.message || 'Pembayaran gagal');
      }
    })
    .catch(error => {
      console.error('Payment error:', error);
      showAlert('error', 'Error', error.message);
      productPin.clear();
    });
  };

  // Save new PIN function
  window.saveProductPin = () => {
    if (newProductPin.length !== 6) {
      showAlert('warning', 'PIN tidak valid', 'PIN harus 6 digit.');
      return;
    }

    const formData = new FormData();
    formData.append('pin', newProductPin.pin);
    formData.append('pin_confirmation', newProductPin.pin);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    fetch('/pin/create', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => {
      if (!response.ok) {
        return response.json().then(err => { throw new Error(err.message || 'Gagal membuat PIN'); });
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        createProductPinModal.classList.add('hidden');
        newProductPin.clear();
        showAlert('success', 'Berhasil', 'PIN berhasil dibuat.');
        
        // Setelah PIN berhasil dibuat, tampilkan modal verifikasi PIN
        setTimeout(() => {
          productPinModal.classList.remove('hidden');
        }, 1000);
      } else {
        throw new Error(data.message || 'Gagal membuat PIN');
      }
    })
    .catch(error => {
      showAlert('error', 'Error', error.message);
    });
  };
});
</script>
@endpush