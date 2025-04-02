@extends('layouts.driver')

@section('content')
<div class="container mx-auto py-6 px-4 sm:px-6">
    <div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header dengan gradient yang lebih modern -->
        <div class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white py-6 px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('driver.dashboard') }}" class="text-white hover:text-blue-200 transition-colors">
                        <svg class="h-6 w-6 transform hover:scale-110 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h2 class="text-2xl font-bold text-white">CEK RESI</h2>
                </div>
                <button type="button" id="faqButton" class="bg-white bg-opacity-20 text-white text-sm font-medium py-1.5 px-4 rounded-full hover:bg-opacity-30 transition-all flex items-center">
                    FAQ
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10c0 4.418-3.582 8-8 8s-8-3.582-8-8 3.582-8 8-8 8 3.582 8 8zm-9 3a1 1 0 112 0 1 1 0 01-2 0zm1-5a1 1 0 00-1 1v1a1 1 0 102 0V9a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            
            <!-- Ilustrasi Package Tracking -->
            <div class="mt-4 flex justify-center">
                <svg class="h-24 w-24 text-white opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6">
            <p class="text-center text-gray-600 mb-6">
                Masukkan Nomor Resi yang tertera di Surat yang diberikan
            </p>

            <!-- FAQ Panel (Hidden by Default) -->
            <div id="faqPanel" class="hidden bg-gray-50 rounded-xl p-5 mb-6 border border-gray-200">
                <h3 class="font-bold text-lg mb-3 text-indigo-800">Pertanyaan Umum</h3>
                <div class="space-y-4">
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        <p class="font-medium text-indigo-800">Apa itu nomor resi?</p>
                        <p class="text-sm text-gray-600 mt-1">Nomor resi adalah kode unik yang diberikan untuk setiap paket yang dikirim.</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        <p class="font-medium text-indigo-800">Format nomor resi seperti apa?</p>
                        <p class="text-sm text-gray-600 mt-1">Format: LW-[KURIR]-[KODE UNIK]</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        <p class="font-medium text-indigo-800">Tidak bisa menemukan resi?</p>
                        <p class="text-sm text-gray-600 mt-1">Pastikan nomor resi diketik dengan benar termasuk tanda hubung.</p>
                    </div>
                </div>
            </div>

            <div id="trackingForm" class="space-y-5">
                @csrf
                <!-- Input Section dengan desain yang lebih baik -->
                <div class="tracking-input-container mb-5">
                    <div class="flex flex-col space-y-2">
                        <label for="tracking_number" class="text-sm font-medium text-gray-700">Nomor Resi</label>
                        <div class="flex rounded-xl overflow-hidden border-2 border-gray-200 focus-within:border-blue-500 transition-colors duration-200 shadow-sm">
                            <div class="bg-gray-100 text-gray-700 py-3 px-4 flex items-center font-medium">
                                LW-
                            </div>
                            
                            <select id="courier_select" class="bg-gray-100 text-gray-700 py-3 px-3 border-l border-r border-gray-200 font-medium appearance-none">
                                <option value="JNE">JNE</option>
                                <option value="J&T">J&T</option>
                                <option value="SiCepat">SiCepat</option>
                                <option value="LWEXPRESS">LWExpress</option>
                            </select>
                            
                            <div class="flex-1 relative">
                                <input type="text" 
                                    name="tracking_number" 
                                    id="tracking_number" 
                                    class="w-full px-4 py-3 uppercase text-lg tracking-wider focus:outline-none" 
                                    placeholder="Nomor Resi" 
                                    maxlength="16" 
                                    required>
                                <div id="validationIcon" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                                    <!-- Validation icons -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="complete_tracking_number">

                <!-- Loading Spinner dengan animasi yang lebih smooth -->
                <div id="loadingSpinner" class="hidden w-full flex justify-center py-2">
                    <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-700"></div>
                </div>

                <!-- Action Button dengan style yang lebih menarik -->
                <div class="flex space-x-3">
                    <button type="button" id="checkButton" class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white py-4 rounded-xl font-medium transition-all duration-300 transform hover:scale-[1.02] flex items-center justify-center shadow-md">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        CEK RESI
                    </button>
                </div>

                <!-- Debug Info -->
                <div id="debugInfo" class="mt-4 p-3 bg-gray-100 rounded-lg text-xs text-gray-600 hidden">
                    <div class="font-medium mb-1">Debug Info:</div>
                    <div id="debugContent"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Modal dengan animasi yang lebih mulus -->
    <div id="resultModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 backdrop-blur-sm flex items-center justify-center z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md m-4 transform scale-95 transition-transform duration-300 shadow-2xl">
            <div id="resultContent" class="text-center"></div>
            <div class="mt-6 text-center">
                <button type="button" class="bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-md" id="closeModal">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Not Found Modal Template dengan desain yang lebih eye-catching -->
    <template id="notFoundTemplate">
        <div class="mb-6">
            <div class="bg-yellow-100 p-5 rounded-full inline-block">
                <svg class="h-14 w-14 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold mt-5 mb-3">Paket Tidak Ditemukan</h3>
            <p class="text-gray-600">Nomor resi yang Anda masukkan tidak terdaftar dalam sistem kami. Silakan periksa kembali dan coba lagi.</p>
        </div>
    </template>
</div>  

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Debug Mode
    const DEBUG_MODE = false;
    
    // Debug Logger
    const logger = {
        log: function(message, data = null) {
            if (DEBUG_MODE) {
                console.log(`%c[INFO] ${message}`, "color: #3498db", data || "");
                this.updateDebugPanel(`[INFO] ${message} ${data ? JSON.stringify(data) : ""}`);
            }
        },
        error: function(message, error = null) {
            console.error(`%c[ERROR] ${message}`, "color: #e74c3c", error || "");
            this.updateDebugPanel(`[ERROR] ${message} ${error ? error.toString() : ""}`);
        },
        warn: function(message, data = null) {
            if (DEBUG_MODE) {
                console.warn(`%c[WARN] ${message}`, "color: #f39c12", data || "");
                this.updateDebugPanel(`[WARN] ${message} ${data ? JSON.stringify(data) : ""}`);
            }
        },
        updateDebugPanel: function(message) {
            if (DEBUG_MODE) {
                const debugContent = document.getElementById('debugContent');
                if (debugContent) {
                    // Kode update panel tetap ada tapi nggak perlu visible
                }
            }
        }
    };
    
    // Elements
    const elements = {
        trackingInput: document.getElementById('tracking_number'),
        courierSelect: document.getElementById('courier_select'),
        completeTracking: document.getElementById('complete_tracking_number'),
        validationIcon: document.getElementById('validationIcon'),
        checkButton: document.getElementById('checkButton'),
        resultModal: document.getElementById('resultModal'),
        resultContent: document.getElementById('resultContent'),
        faqButton: document.getElementById('faqButton'),
        faqPanel: document.getElementById('faqPanel'),
        loadingSpinner: document.getElementById('loadingSpinner'),
        debugInfo: document.getElementById('debugInfo').style.display = 'none',
        notFoundTemplate: document.getElementById('notFoundTemplate')
    };

    // Show Debug Panel if DEBUG_MODE is true
    if (DEBUG_MODE) {
        elements.debugInfo.classList.remove('hidden');
    }
    
    // State
    let lastTrackingRequest = null;
    let isValidating = false;

    // Core Functions
    const updateTrackingNumber = () => {
        const fullNumber = `LW-${elements.courierSelect.value}-${elements.trackingInput.value}`;
        elements.completeTracking.value = fullNumber;
        logger.log("Generated tracking number", fullNumber);
        
        if(elements.trackingInput.value.length > 0) {
            validateTrackingNumber(fullNumber);
        } else {
            elements.validationIcon.classList.add('hidden');
        }
    };

    const validateTrackingNumber = async (trackingNumber) => {
        try {
            if (isValidating) return;
            isValidating = true;
            
            logger.log("Validating tracking number", trackingNumber);
            
            if(lastTrackingRequest) {
                lastTrackingRequest.abort();
                logger.warn("Aborted previous validation request");
            }
            
            const controller = new AbortController();
            lastTrackingRequest = controller;

            // Check if CSRF token exists
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) {
                logger.error("CSRF token not found!");
                showErrorModal("Error: CSRF token missing. Please refresh the page.");
                isValidating = false;
                return;
            }

            // Show loading state
            setLoadingState(true);

            // FIX: Make sure we're using the API route, not web route
            const response = await fetch(`/api/check-tracking?number=${encodeURIComponent(trackingNumber)}`, {
                signal: controller.signal,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if(!response.ok) {
                logger.error(`HTTP Error: ${response.status}`);
                throw new Error(`HTTP Error: ${response.status}`);
            }
            
            const data = await response.json();
            logger.log("Validation response received", data);

            elements.validationIcon.classList.remove('hidden');
            elements.validationIcon.innerHTML = data.exists ? 
                `<svg class="h-6 w-6 text-green-500 animate-bounce" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>` :
                `<svg class="h-6 w-6 text-red-500 animate-pulse" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>`;

        } catch (error) {
            logger.error("Validation failed", error);
            elements.validationIcon.classList.add('hidden');
            if(error.name !== 'AbortError') {
                showErrorModal('Gagal memvalidasi nomor resi. Coba lagi atau periksa koneksi internet Anda.');
            }
        } finally {
            isValidating = false;
            setLoadingState(false);
        }
    };

    const fetchTrackingDetails = async (trackingNumber) => {
        try {
            logger.log("Fetching tracking details", trackingNumber);
            
            // Show loading
            setLoadingState(true);
            
            // Check if CSRF token exists
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) {
                logger.error("CSRF token not found!");
                showErrorModal("Error: CSRF token missing. Please refresh the page.");
                return;
            }
            
            // FIX: Make sure we're using the API route, not web route
            const response = await fetch(`/api/check-tracking-details?tracking_number=${encodeURIComponent(trackingNumber)}`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if(!response.ok) {
                const statusText = response.statusText;
                logger.error(`HTTP Error: ${response.status} ${statusText}`);
                throw new Error(`Server error: ${response.status} ${statusText}`);
            }

            const responseData = await response.json();
            logger.log("Details response received", responseData);

            if(responseData.exists) {
                showSuccessModal(responseData.order);
            } else {
                showNotFoundModal();
            }

        } catch (error) {
            logger.error("Details fetch failed", error);
            showErrorModal(`Gagal memuat detail: ${error.message}`);
        } finally {
            setLoadingState(false);
        }
    };

    // UI Helper Functions
    const setLoadingState = (isLoading) => {
        elements.loadingSpinner.classList.toggle('hidden', !isLoading);
        elements.checkButton.disabled = isLoading;
        if(isLoading) {
            elements.checkButton.classList.add('opacity-75');
        } else {
            elements.checkButton.classList.remove('opacity-75');
        }
    };
    
    const toggleFAQ = () => {
        elements.faqPanel.classList.toggle('hidden');
        logger.log("FAQ panel toggled", !elements.faqPanel.classList.contains('hidden'));
    };

    // Modal Handlers
    const showSuccessModal = (orderData) => {
        elements.resultContent.innerHTML = `
            <div class="mb-6">
                <div class="bg-green-100 p-4 rounded-full inline-block">
                    <svg class="h-14 w-14 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mt-4 mb-3">Paket Ditemukan!</h3>
                <div class="text-left bg-gray-50 p-5 rounded-lg mt-4 shadow-inner">
                    <p class="font-semibold mb-2">No. Order: <span class="font-normal">${orderData.order_number}</span></p>
                    <p class="font-semibold mb-2">Status: 
                        <span class="font-normal bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">${orderData.status_order}</span>
                    </p>
                    <p class="font-semibold mb-2">Nomor Resi: <span class="font-normal">${orderData.nomor_resi || '-'}</span></p>
                    <div class="mt-4 border-t border-gray-200 pt-3">
                        <p class="font-semibold text-indigo-800">Alamat Pengiriman:</p>
                        <p class="font-normal text-sm mt-1">${orderData.alamat_lengkap || '-'}</p>
                        <p class="font-normal text-sm">${orderData.kecamatan || ''}, ${orderData.kota || ''}</p>
                        <p class="font-normal text-sm">${orderData.provinsi || ''} ${orderData.kode_pos || ''}</p>
                    </div>
                </div>
                <a href="/driver/delivery/${orderData.id}" class="inline-block mt-5 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white px-6 py-3 rounded-lg shadow-md transform hover:scale-[1.03] transition-all duration-300">
                    Proses Pengiriman
                </a>
            </div>`;
        toggleModal(true);
        logger.log("Success modal shown for order", orderData.order_number);
    };

    const showNotFoundModal = () => {
        elements.resultContent.innerHTML = elements.notFoundTemplate.innerHTML;
        toggleModal(true);
        logger.warn("Not found modal shown");
    };

    const showErrorModal = (message) => {
        elements.resultContent.innerHTML = `
            <div class="mb-6">
                <div class="bg-red-100 p-4 rounded-full inline-block">
                    <svg class="h-14 w-14 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mt-4 mb-3">Terjadi Kesalahan</h3>
                <p class="text-gray-600">${message}</p>
            </div>`;
        toggleModal(true);
        logger.error("Error modal shown", message);
    };

    const toggleModal = (show) => {
        elements.resultModal.classList.toggle('hidden', !show);
        
        // Use setTimeout to ensure transitions work properly
        if (show) {
            setTimeout(() => {
                elements.resultModal.classList.add('opacity-100');
                elements.resultModal.querySelector('.bg-white').classList.add('scale-100');
                elements.resultModal.querySelector('.bg-white').classList.remove('scale-95');
            }, 10);
        } else {
            elements.resultModal.classList.remove('opacity-100');
            elements.resultModal.querySelector('.bg-white').classList.remove('scale-100');
            elements.resultModal.querySelector('.bg-white').classList.add('scale-95');
            setTimeout(() => {
                elements.resultModal.classList.add('hidden');
            }, 300);
        }
    };

    // Event Listeners
    elements.trackingInput.addEventListener('input', () => {
        elements.trackingInput.value = elements.trackingInput.value.toUpperCase();
        updateTrackingNumber();
    });

    elements.courierSelect.addEventListener('change', updateTrackingNumber);

    elements.checkButton.addEventListener('click', () => {
        if(elements.trackingInput.value.length > 0) {
            fetchTrackingDetails(elements.completeTracking.value);
        } else {
            logger.warn("No tracking number entered");
            // Shake input field to indicate error
            elements.trackingInput.classList.add('border-red-500');
            setTimeout(() => {
                elements.trackingInput.classList.remove('border-red-500');
            }, 1000);
        }
    });

    elements.faqButton.addEventListener('click', toggleFAQ);

    document.getElementById('closeModal').addEventListener('click', () => toggleModal(false));
    
    elements.resultModal.addEventListener('click', (e) => {
        if(e.target === elements.resultModal) toggleModal(false);
    });

    // Keyboard shortcut for submit
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && elements.trackingInput.value.length > 0) {
            fetchTrackingDetails(elements.completeTracking.value);
        }
    });

    // Error Handling untuk DOM Elements
    for (const [key, element] of Object.entries(elements)) {
        if (!element && key !== 'notFoundTemplate') {  // NotFoundTemplate adalah optional
            console.error(`%c[CRITICAL] Element tidak ditemukan: ${key}`, "color: #e74c3c; font-weight: bold");
        }
    }

    // Check CSRF token
    if (!document.querySelector('meta[name="csrf-token"]')) {
        console.error("%c[CRITICAL] CSRF token tidak ditemukan di halaman!", "color: #e74c3c; font-weight: bold");
    }

    // Initial Setup
    logger.log("Tracking System Initialized");
    elements.trackingInput.focus();
});
</script>
@endsection