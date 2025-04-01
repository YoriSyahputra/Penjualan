<!-- Modal Verifikasi PIN -->
<!-- Modal Verifikasi PIN -->
<div id="pinModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
  <div class="bg-white rounded p-6 w-96 max-w-[90%]">
    <h2 class="text-xl font-bold text-center mb-2">Masukkan PIN Anda</h2>
    <p class="text-gray-600 text-center mb-4">Masukkan PIN 6 digit untuk konfirmasi transfer</p>

    <!-- Ringkasan Transfer -->
    <div class="bg-gray-50 rounded p-4 mb-4">
      <div class="flex justify-between">
        <span class="text-gray-600">Jumlah:</span>
        <span class="font-semibold">Rp <span id="summaryAmount">0</span></span>
      </div>
      <div class="flex justify-between mt-2">
        <span class="text-gray-600">Kepada:</span>
        <span class="font-semibold">{{ $recipient->name }}</span>
      </div>
    </div>

    <input type="password"
    id="pinInput" 
    class="w-full text-center text-2xl tracking-widest mb-6 border border-gray-300 rounded py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"  
    readonly 
    maxlength="6">
    <!-- Numpad PIN -->
    <div class="grid grid-cols-3 gap-2 mb-4">
      @foreach(range(1, 9) as $num)
      <button type="button" class="pin-btn h-12 bg-gray-50 rounded text-xl font-semibold" data-val="{{ $num }}">{{ $num }}</button>
      @endforeach
      <button type="button" class="h-14 bg-red-50 rounded-lg text-xl font-bold text-red-600 hover:bg-red-100 transition-colors" onclick="clearPin()">C</button>
      <button type="button" class="pin-btn h-14 bg-blue-50 rounded-lg text-xl font-bold text-blue-600 hover:bg-blue-100 transition-colors" data-val="0">0</button>
      <button type="button" class="h-14 bg-yellow-50 rounded-lg text-xl font-bold text-yellow-600 hover:bg-yellow-100 transition-colors" onclick="deletePin()">⌫</button>
    </div>

    <div class="flex gap-3">
      <button type="button" onclick="cancelPin()" class="flex-1 border rounded px-4 py-2 hover:bg-gray-50">Batal</button>
      <button type="button" onclick="confirmPin()" class="flex-1 bg-indigo-600 text-white rounded px-4 py-2 hover:bg-indigo-700">Konfirmasi</button>
    </div>
  </div>
</div>

<!-- Modal Pembuatan PIN -->
@if(auth()->check() && !auth()->user()->hasPin())
<div id="createPinModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
  <div class="bg-white rounded p-6 w-96 max-w-[90%]">
    <h2 class="text-xl font-bold text-center mb-2">Buat PIN Anda</h2>
    <p class="text-gray-600 text-center mb-4">Anda harus membuat PIN 6 digit untuk transfer</p>
    <input type="password" id="newPinInput" class="w-full text-center text-2xl tracking-widest mb-4 border border-gray-300 rounded py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="******" readonly maxlength="6">
    
    <!-- Numpad Pembuatan PIN -->
    <div class="grid grid-cols-3 gap-2 mb-4">
      @foreach(range(1, 9) as $num)
      <button type="button" class="new-pin-btn h-12 bg-gray-50 rounded text-xl font-semibold" data-val="{{ $num }}">{{ $num }}</button>
      @endforeach
      <button type="button" class="h-12 bg-gray-50 rounded text-xl font-semibold" onclick="clearNewPin()">C</button>
      <button type="button" class="new-pin-btn h-12 bg-gray-50 rounded text-xl font-semibold" data-val="0">0</button>
      <button type="button" class="h-12 bg-gray-50 rounded text-xl font-semibold" onclick="deleteNewPin()">⌫</button>
    </div>
    <button type="button" id="savePinBtn" onclick="savePin()" class="w-full bg-indigo-600 text-white rounded px-4 py-2" disabled>Buat PIN</button>
  </div>
</div>
@endif


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // State management using class
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
  const verificationPin = new PinState(document.getElementById('pinInput'));
  const newPin = new PinState(document.getElementById('newPinInput'));
  
  // Elements
  const pinModal = document.getElementById('pinModal');
  const createPinModal = document.getElementById('createPinModal');
  const savePinBtn = document.getElementById('savePinBtn');
  const transferForm = document.getElementById('transferForm');

  // Event Listeners for verification PIN
  document.querySelectorAll('.pin-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      verificationPin.append(btn.getAttribute('data-val'));
    });
  });

  // Event Listeners for new PIN
  document.querySelectorAll('.new-pin-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      newPin.append(btn.getAttribute('data-val'));
      savePinBtn.disabled = newPin.length !== 6;
    });
  });

  // PIN Management Functions
  window.clearPin = () => verificationPin.clear();
  window.deletePin = () => verificationPin.delete();
  window.clearNewPin = () => {
    newPin.clear();
    savePinBtn.disabled = true;
  };
  window.deleteNewPin = () => {
    newPin.delete();
    savePinBtn.disabled = newPin.length !== 6;
  };

  window.cancelPin = () => {
    pinModal.classList.add('hidden');
    verificationPin.clear();
  };

  // Confirm PIN for transfer
  window.confirmPin = () => {
    if (verificationPin.length !== 6) {
      showAlert('warning', 'PIN tidak valid', 'Masukkan PIN 6 digit.');
      return;
    }

    if (!transferForm) {
      showAlert('error', 'Error', 'Form transfer tidak ditemukan.');
      return;
    }

    const formData = new FormData(transferForm);
    formData.append('pin', verificationPin.pin);

    fetch(transferForm.action, {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => {
      if (!response.ok) {
        return response.json().then(err => { throw new Error(err.message || 'Transfer gagal'); });
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        pinModal.classList.add('hidden');
        verificationPin.clear();
        setTimeout(() => window.location.href = data.redirect || '/dashboard', 1500);
      } else {
        throw new Error(data.message || 'Transfer gagal');
      }
    })
    .catch(error => {
      showAlert('error', 'Error', error.message);
      verificationPin.clear();
    });
  };

  // Save new PIN
  window.savePin = () => {
    if (newPin.length !== 6) {
      showAlert('warning', 'PIN tidak valid', 'PIN harus 6 digit.');
      return;
    }

    const formData = new FormData();
    formData.append('pin', newPin.pin);
    formData.append('pin_confirmation', newPin.pin);
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
        createPinModal.classList.add('hidden');
        newPin.clear();
        showAlert('success', 'Berhasil', 'PIN berhasil dibuat.');
        window.location.reload();
      } else {
        throw new Error(data.message || 'Gagal membuat PIN');
      }
    })
    .catch(error => {
      showAlert('error', 'Error', error.message);
    });
  };

  // Alert function
  window.showAlert = (icon, title, text) => {
    if (typeof Swal !== 'undefined') {
      Swal.fire({ icon, title, text });
    } else {
      alert(text);
    }
  };
});
</script>
@endpush