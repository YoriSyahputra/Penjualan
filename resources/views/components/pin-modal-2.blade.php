<!-- Modal Verifikasi PIN untuk Pembayaran Produk -->
<div id="productPinModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
  <div class="bg-white rounded p-6 w-96 max-w-[90%]">
    <h2 class="text-xl font-bold text-center mb-2">Masukkan PIN Anda</h2>
    <p class="text-gray-600 text-center mb-4">Masukkan PIN 6 digit untuk konfirmasi pembayaran</p>

    <!-- Ringkasan Pembayaran -->
    <div class="bg-gray-50 rounded p-4 mb-4">
      <div class="flex justify-between">
        <span class="text-gray-600">Jumlah:</span>
        <span class="font-semibold">Rp <span id="paymentSummaryAmount">0</span></span>
      </div>
      <div class="flex justify-between mt-2">
        <span class="text-gray-600">Order:</span>
        <span id="orderNumberSummary" class="font-semibold"></span>
      </div>
    </div>

    <input type="password" id="productPinInput" class="w-full text-center text-2xl tracking-widest mb-4" placeholder="******" readonly maxlength="6">

    <!-- Numpad PIN -->
    <div class="grid grid-cols-3 gap-2 mb-4">
      @foreach(range(1, 9) as $num)
      <button type="button" class="product-pin-btn h-12 bg-gray-50 rounded text-xl font-semibold" data-val="{{ $num }}">{{ $num }}</button>
      @endforeach
      <button type="button" class="h-12 bg-gray-50 rounded text-xl font-semibold" onclick="clearProductPin()">C</button>
      <button type="button" class="product-pin-btn h-12 bg-gray-50 rounded text-xl font-semibold" data-val="0">0</button>
      <button type="button" class="h-12 bg-gray-50 rounded text-xl font-semibold" onclick="deleteProductPin()">⌫</button>
    </div>

    <div class="flex gap-3">
      <button type="button" onclick="cancelProductPin()" class="flex-1 border rounded px-4 py-2 hover:bg-gray-50">Batal</button>
      <button type="button" onclick="confirmProductPin()" class="flex-1 bg-indigo-600 text-white rounded px-4 py-2 hover:bg-indigo-700">Konfirmasi</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // State management for product payment PIN
  class ProductPinState {
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

  // Initialize product payment PIN state
  const productPin = new ProductPinState(document.getElementById('productPinInput'));
  
  // Elements
  const productPinModal = document.getElementById('productPinModal');
  const paymentForm = document.getElementById('paymentForm');

  // Event Listeners for product payment PIN
  document.querySelectorAll('.product-pin-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      productPin.append(btn.getAttribute('data-val'));
    });
  });

  // Product PIN Management Functions
  window.clearProductPin = () => productPin.clear();
  window.deleteProductPin = () => productPin.delete();

  window.cancelProductPin = () => {
    productPinModal.classList.add('hidden');
    productPin.clear();
  };

  // Show product payment PIN modal
  window.showProductPinModal = () => {
    const orderNumber = document.getElementById('orderNumber').textContent;
    const amount = document.getElementById('orderTotal').textContent.replace('Rp ', '');
    
    document.getElementById('paymentSummaryAmount').textContent = amount;
    document.getElementById('orderNumberSummary').textContent = orderNumber;
    productPinModal.classList.remove('hidden');
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
});
</script>
@endpush