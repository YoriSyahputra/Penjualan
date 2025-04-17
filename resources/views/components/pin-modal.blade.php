<!-- Modal Verifikasi PIN -->
<div id="pinModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-md hidden flex items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-lg p-8 w-96 max-w-[90%]">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Masukkan PIN Anda</h2>
    <p class="text-gray-600 text-center mb-6">Masukkan PIN 6 digit untuk konfirmasi transfer</p>

    <!-- Ringkasan Transfer -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 mb-6 border border-blue-200">
      <div class="flex justify-between">
        <span class="text-gray-600">Jumlah:</span>
        <span class="font-semibold text-gray-800">Rp <span id="summaryAmount">0</span></span>
      </div>
      <div class="flex justify-between mt-3">
        <span class="text-gray-600">Kepada:</span>
        <span class="font-semibold text-gray-800">{{ $recipient->name }}</span>
      </div>
    </div>

    <input type="password" 
          id="pinInput" 
          class="w-full text-center text-2xl tracking-widest mb-6 border border-gray-300 rounded py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
          placeholder="******" 
          readonly
          value=""
          maxlength="6"
    >
    
    <!-- Numpad PIN -->
    <div class="grid grid-cols-3 gap-3 mb-6">
      @foreach(range(1, 9) as $num)
      <button type="button" class="pin-btn h-14 bg-blue-50 rounded-lg text-xl font-bold text-blue-600 hover:bg-blue-100 transition-colors" data-val="{{ $num }}">{{ $num }}</button>
      @endforeach
      <button type="button" 
              class="h-14 bg-red-50 rounded-lg text-xl font-bold text-red-600 hover:bg-red-100 transition-colors" 
              onclick="clearPin()">
              C
      </button>
      <button type="button" class="pin-btn h-14 bg-blue-50 rounded-lg text-xl font-bold text-blue-600 hover:bg-blue-100 transition-colors" 
              data-val="0">
              0
      </button>
      <button type="button"  
              class="h-14 bg-yellow-50 rounded-lg text-xl font-bold text-yellow-600 hover:bg-yellow-100 transition-colors" 
              onclick="deletePin()">
              ⌫
      </button>
    </div>

    <div class="flex gap-4">
      <button type="button" 
              onclick="cancelPin()" 
              class="flex-1 border border-gray-300 bg-gray-200 rounded-lg px-4 py-2 text-gray-800 hover:bg-gray-300 transition duration-200"
              >
              Batal
      </button>
      <button type="button" 
              onclick="confirmPin()" 
              class="flex-1 border border-green-600 bg-green-500 rounded-lg px-4 py-2 text-white hover:bg-green-600 transition duration-200"
              >
              Konfirmasi
            </button>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // PIN state management class
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
            if (this.inputElement) {
                this.inputElement.value = this.value;
            }
        }

        get length() {
            return this.value.length;
        }

        get pin() {
            return this.value;
        }
    }

    // Initialize PIN state
    const pin = new PinState(document.getElementById('pinInput'));

    // Event Listeners for PIN buttons
    document.querySelectorAll('.pin-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            pin.append(btn.getAttribute('data-val'));
        });
    });

    // PIN Management Functions
    window.clearPin = function() {
        if (pin) pin.clear();
    };
    
    window.deletePin = function() {
        if (pin) pin.delete();
    };

    window.cancelPin = function() {
        const pinModal = document.getElementById('pinModal');
        if (pinModal) pinModal.classList.add('hidden');
        if (pin) pin.clear();
    };

    // Rest of your existing pin-modal code...
    // Add showAlert function at the top
    function showAlert(type, title, message) {
      if (window.Swal) {
        Swal.fire({
          icon: type,
          title: title,
          text: message
        });
      } else {
        alert(`${title}: ${message}`);
      }
    }

    // Initialize PIN states
    const pin = new PinState(document.getElementById('pinInput'));
    
    // Elements
    const pinModal = document.getElementById('pinModal');
    const transferForm = document.querySelector('form[action*="transfer"]'); 

    // Event Listeners untuk PIN
    document.querySelectorAll('.pin-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        pin.append(btn.getAttribute('data-val'));
      });
    });

    // PIN Management Functions
    window.clearPin = () => pin.clear();
    window.deletePin = () => pin.delete();

    window.cancelPin = () => {
      if (pinModal) pinModal.classList.add('hidden');
      pin.clear();
    };

    window.confirmPin = function() {
      // Menggunakan pin.length, bukan pinValue.length
      if (pin.length !== 6) {
        showAlert('warning', 'PIN tidak valid', 'Masukkan PIN 6 digit.');
        return;
      }

      if (!transferForm) {
        showAlert('error', 'Error', 'Form transfer tidak ditemukan.');
        return;
      }

      const formData = new FormData(transferForm);
      // Menggunakan pin.pin untuk mendapatkan nilai PIN
      formData.append('pin', pin.pin);
      
      const confirmBtn = document.querySelector('[onclick="confirmPin()"]');
      if (confirmBtn) {
        confirmBtn.textContent = 'Processing...';
        confirmBtn.disabled = true;
      }

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
          clearPin();
          showAlert('success', 'Berhasil', 'Transfer berhasil dilakukan.');
          setTimeout(() => window.location.href = data.redirect || '/dashboard', 1500);
        } else {
          throw new Error(data.message || 'Transfer gagal');
        }
      })
      .catch(error => {
        showAlert('error', 'Error', error.message);
        clearPin();
        if (confirmBtn) {
          confirmBtn.textContent = 'Konfirmasi';
          confirmBtn.disabled = false;
        }
      });
    };

    // Listener untuk event 'pinCreated'
    document.addEventListener('pinCreated', () => {
      // Tampilkan modal PIN setelah PIN berhasil dibuat
      setTimeout(() => {
        pinModal.classList.remove('hidden');
      }, 1000);
    });
});
</script>
@endpush