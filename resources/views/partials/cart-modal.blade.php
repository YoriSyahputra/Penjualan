<div id="cartModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900" id="modalProductName"></h3>
            <button onclick="closeCartModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Product Image with Navigation -->
        <div class="mb-4 relative">
            <img id="modalProductImage" src="" alt="Product" class="w-full h-48 object-cover rounded-lg">
            <div id="imageNavigation" class="hidden">
                <button onclick="previousImage()" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2">
                    ←
                </button>
                <button onclick="nextImage()" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2">
                    →
                </button>
            </div>
        </div>

        <!-- Product Price -->
        <div class="mb-4">
            <p class="text-lg font-semibold text-gray-900" id="modalProductPrice"></p>
        </div>

        <!-- Product Variants Dropdown -->
        <div id="variantsContainer" class="mb-4 hidden">
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Variant</label>
            <select id="variantSelect" class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Choose a variant</option>
                @if(isset($product->variants) && count($product->variants) > 0)
                    @foreach($product->variants as $variant)
                        <option value="{{ $variant->id }}">{{ $variant->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <!-- Product Packages Dropdown -->
        <div id="packagesContainer" class="mb-4 hidden">
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Package</label>
            <select id="packageSelect" class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Choose a package</option>
                @if(isset($product->packages) && count($product->packages) > 0)
                    @foreach($product->packages as $package)
                        <option value="{{ $package->id }}">{{ $package->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <!-- Quantity Selector -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
            <div class="flex items-center">
                <button onclick="decrementQuantity()" class="p-2 border rounded-l bg-gray-100 hover:bg-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </button>
                <input type="number" id="quantityInput" value="1" min="1" 
                       class="p-2 w-20 border-t border-b text-center focus:outline-none focus:border-indigo-500" readonly/>
                <button onclick="incrementQuantity()" class="p-2 border rounded-r bg-gray-100 hover:bg-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Add to Cart Button -->
        <div class="mt-6">
            <button onclick="addToCartWithOptions()" 
                    class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Add to Cart
            </button>
        </div>
    </div>
</div>
<script>
// Global state variables
let currentProductId = null;
let currentProduct = null;
let currentImageIndex = 0;
let currentProductImages = [];

// Open cart modal
function openCartModal(productId) {
    currentProductId = productId;
    const modal = document.getElementById('cartModal');
    modal.classList.remove('hidden');

    // Reset form
    document.getElementById('quantityInput').value = 1;
    
    // Fetch product details
    fetch(`/product-details/${productId}`)
        .then(response => response.json())
        .then(data => {
            currentProduct = data.product;
            
            // Handle product images
            currentProductImages = data.product.productImages || [];
            const imageElement = document.getElementById('modalProductImage');
            const imageNavigation = document.getElementById('imageNavigation');

            if (currentProductImages.length > 0) {
                imageElement.src = currentProductImages[0].path_gambar 
                    ? `/storage/${currentProductImages[0].path_gambar}`
                    : '/placeholder-image.jpg';
                
                // Show/hide navigation based on image count
                imageNavigation.classList.toggle('hidden', currentProductImages.length <= 1);
            } else {
                imageElement.src = '/placeholder-image.jpg';
                imageNavigation.classList.add('hidden');
            }
            // Update product name and price
            document.getElementById('modalProductName').textContent = data.product.name;
            
            const priceElement = document.getElementById('modalProductPrice');
            const price = data.product.discount_price || data.product.price;
            priceElement.textContent = `Rp ${numberFormat(price)}`;

            // Handle product variants
            const variantSelect = document.getElementById('variantSelect');
            const variantsContainer = document.getElementById('variantsContainer');

            variantSelect.innerHTML = '<option value="">Choose a variant</option>';
            
            if (data.variants && data.variants.length > 0) {
                variantsContainer.classList.remove('hidden');
                data.variants.forEach(variant => {
                    const option = document.createElement('option');
                    option.value = variant.id;
                    option.textContent = variant.name;
                    variantSelect.appendChild(option);
                });
            } else {
                variantsContainer.classList.add('hidden');
            }

            // Handle product packages
            const packageSelect = document.getElementById('packageSelect');
            const packagesContainer = document.getElementById('packagesContainer');

            packageSelect.innerHTML = '<option value="">Choose a package</option>';

            if (data.packages && data.packages.length > 0) {
                packagesContainer.classList.remove('hidden');
                data.packages.forEach(pkg => {
                    const option = document.createElement('option');
                    option.value = pkg.id;
                    option.textContent = pkg.name;
                    packageSelect.appendChild(option);
                });
            } else {
                packagesContainer.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error fetching product details:', error);
            showToast('Error loading product details');
        });
}

// Image navigation functions
function nextImage() {
    if (currentProductImages.length > 1) {
        currentImageIndex = (currentImageIndex + 1) % currentProductImages.length;
        document.getElementById('modalProductImage').src = 
            `/storage/${currentProductImages[currentImageIndex].path_gambar}`;
    }
}

function previousImage() {
    if (currentProductImages.length > 1) {
        currentImageIndex = (currentImageIndex - 1 + currentProductImages.length) % currentProductImages.length;
        document.getElementById('modalProductImage').src = 
            `/storage/${currentProductImages[currentImageIndex].path_gambar}`;
    }
}

// Close modal function
function closeCartModal() {
    const modal = document.getElementById('cartModal');
    modal.classList.add('hidden');
    currentProductId = null;
    currentProduct = null;
    currentProductImages = [];
    currentImageIndex = 0;
}

// Quantity manipulation functions
function incrementQuantity() {
    const input = document.getElementById('quantityInput');
    const currentValue = parseInt(input.value);
    
    if (currentProduct && currentValue < currentProduct.stock) {
        input.value = currentValue + 1;
    }
}

function decrementQuantity() {
    const input = document.getElementById('quantityInput');
    const currentValue = parseInt(input.value);
    
    if (currentValue > 1) {
        input.value = currentValue - 1;
    }
}

// Number formatting function
function numberFormat(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

// Add to cart function
function addToCartWithOptions() {
    if (!currentProductId) {
        showToast('Error: Product not selected');
        return;
    }

    const quantity = parseInt(document.getElementById('quantityInput').value);
    const variantId = document.getElementById('variantSelect').value;
    const packageId = document.getElementById('packageSelect').value;

    const variantsContainer = document.getElementById('variantsContainer');
    const packagesContainer = document.getElementById('packagesContainer');

    if (!variantsContainer.classList.contains('hidden') && !variantId) {
        showToast('Please select a variant');
        return;
    }

    if (!packagesContainer.classList.contains('hidden') && !packageId) {
        showToast('Please select a package');
        return;
    }

    fetch(`/cart/add/${currentProductId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            quantity: quantity,
            variant_id: variantId || null,
            package_id: packageId || null
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                throw new Error(errorData.message || 'Error adding product to cart');
            });
        }
        return response.json();
    })
    .then(data => {
        showToast(data.message);
        closeCartModal();
        
        // Update cart count
        const cartCount = document.getElementById('cartCount');
        if (cartCount) {
            cartCount.textContent = data.cartCount;
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        showToast(error.message || 'Error adding product to cart');
    });
}

// Show toast notification
function showToast(message) {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.textContent = message;
        toast.classList.remove('translate-y-full', 'opacity-0');
        
        setTimeout(() => {
            toast.classList.add('translate-y-full', 'opacity-0');
        }, 3000);
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCartModal();
        }
    });

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('cartModal');
        const modalContent = modal.querySelector('.relative');
        
        if (event.target === modal) {
            closeCartModal();
        }
    });
});
</script>