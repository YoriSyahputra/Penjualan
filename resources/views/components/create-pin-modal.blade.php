<!-- Modal Pembuatan PIN -->
<div id="createPinModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-md hidden flex items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-lg p-8 w-96 max-w-[90%]">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Buat PIN Baru</h2>
    <p class="text-gray-600 text-center mb-6">Anda perlu membuat PIN 6 digit untuk bertransaksi</p>
    
    <input type="password" id="newPinInput" class="w-full text-center text-2xl tracking-widest mb-6 border border-gray-300 rounded py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="******" readonly maxlength="6">
    
    <!-- Numpad Pembuatan PIN -->
    <div class="grid grid-cols-3 gap-3 mb-6">
      @foreach(range(1, 9) as $num)
      <button type="button" class="new-pin-btn h-14 bg-blue-50 rounded-lg text-xl font-bold text-blue-600 hover:bg-blue-100 transition-colors" data-val="{{ $num }}">{{ $num }}</button>
      @endforeach
      <button type="button" class="h-14 bg-red-50 rounded-lg text-xl font-bold text-red-600 hover:bg-red-100 transition-colors" onclick="clearNewPin()">C</button>
      <button type="button" class="new-pin-btn h-14 bg-blue-50 rounded-lg text-xl font-bold text-blue-600 hover:bg-blue-100 transition-colors" data-val="0">0</button>
      <button type="button" class="h-14 bg-yellow-50 rounded-lg text-xl font-bold text-yellow-600 hover:bg-yellow-100 transition-colors" onclick="deleteNewPin()">⌫</button>
    </div>
    
    <button 
      type="button" 
      id="savePinBtn" 
      onclick="savePin()" 
      class="w-full border border-green-600 bg-green-500 rounded-lg px-4 py-3 text-white font-medium hover:bg-green-600 transition duration-200" 
      disabled
    >
      Buat PIN
    </button>
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
                this.updateSaveButton();
            }
        }

        updateSaveButton() {
            const savePinBtn = document.getElementById('savePinBtn');
            if (savePinBtn) {
                savePinBtn.disabled = this.length !== 6;
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
    const newPin = new PinState(document.getElementById('newPinInput'));

    // Event Listeners for PIN buttons
    document.querySelectorAll('.new-pin-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            newPin.append(btn.getAttribute('data-val'));
        });
    });

    // PIN Management Functions
    window.clearNewPin = function() {
        if (newPin) newPin.clear();
    };
    
    window.deleteNewPin = function() {
        if (newPin) newPin.delete();
    };

    window.savePin = function() {
        const createPinModal = document.getElementById('createPinModal');
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
                
                // Emit event that PIN was created
                document.dispatchEvent(new CustomEvent('pinCreated'));
            } else {
                throw new Error(data.message || 'Gagal membuat PIN');
            }
        })
        .catch(error => {
            showAlert('error', 'Error', error.message);
            newPin.clear();
        });
    };

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
});
</script>
@endpush