@extends('layouts.depan')

@section('content')
<section class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Enhanced Product Image Gallery -->
            <div class="space-y-4">
                <div class="bg-gray-100 rounded-lg overflow-hidden shadow-sm" style="height: 480px;">
                    @if($product->productImages->isNotEmpty())
                        <img src="{{ asset('storage/' . $product->productImages->first()->path_gambar) }}"
                             alt="{{ $product->name }}"
                             id="mainProductImage"
                             class="w-full h-full object-cover object-center transition duration-300">
                    @else
                        <img src="/api/placeholder/600/600" 
                             alt="No image"
                             id="mainProductImage"
                             class="w-full h-full object-cover object-center">
                    @endif
                </div>
                
                @if($product->productImages->count() > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($product->productImages as $image)
                            <div class="bg-gray-100 rounded overflow-hidden cursor-pointer product-thumbnail border-2 hover:border-indigo-500 transition">
                                <img src="{{ asset('storage/' . $image->path_gambar) }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-24 object-cover object-center hover:opacity-75 transition">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Improved Product Information -->
            <div class="space-y-6">
                <!-- Product Header -->
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                    
                    <div class="flex items-center mt-2">
                        <div class="flex items-center">
                            <span class="bg-indigo-600 text-white text-xs font-medium px-2.5 py-1 rounded">
                                {{ $product->category->name }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Price Section with improved styling -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    @if($product->discount_price)
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-2xl font-bold text-gray-900">
                                Rp.{{ number_format($product->discount_price, 0, ',', '.') }}
                            </span>
                            <span class="text-gray-500 line-through text-sm">
                                Rp.{{ number_format($product->price, 0, ',', '.') }}
                            </span>
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded ml-auto">
                                {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% OFF
                            </span>
                        </div>
                    @else
                        <span class="text-2xl font-bold text-gray-900">
                            Rp.{{ number_format($product->price, 0, ',', '.') }}
                        </span>
                    @endif
                </div>

                <!-- Description Section -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold mb-2 text-gray-800">Description:</h3>
                    <p class="text-gray-600">{{ $product->description }}</p>
                </div>

                <!-- Stock Status with improved visualization -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    @if($product->stock > 0)
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            <span class="text-sm text-green-600 font-medium">In Stock ({{ $product->stock }} available)</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                            <span class="text-sm text-red-600 font-medium">Out of Stock</span>
                        </div>
                    @endif
                </div>

                <!-- Variants Section with improved styling -->
                @if($product->variants->isNotEmpty())
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold mb-2 text-gray-800">Variants:</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->variants as $variant)
                            <button class="variant-btn border border-gray-300 bg-white text-gray-800 text-sm px-3 py-1.5 rounded-full hover:bg-indigo-50 hover:border-indigo-300 transition">
                                {{ $variant->name }} (+Rp.{{ number_format($variant->price, 0, ',', '.') }})
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Product Packages Section with improved styling -->
                @if($product->packages->isNotEmpty())
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold mb-2 text-gray-800">Packages:</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->packages as $package)
                            <button class="package-btn border border-gray-300 bg-white text-gray-800 text-sm px-3 py-1.5 rounded-full hover:bg-indigo-50 hover:border-indigo-300 transition">
                                {{ $package->name }} (+Rp.{{ number_format($package->price, 0, ',', '.') }})
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Add to Cart Button with improved styling -->
                <div class="pt-2">
                    <button 
                        @auth
                            onclick="openCartModal({{ $product->id }})"
                        @else
                            onclick="redirectToLogin()"
                        @endauth
                        @if($product->stock <= 0) disabled @endif
                        class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed shadow-md hover:shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="font-medium">{{ $product->stock > 0 ? 'Add to Cart' : 'Out of Stock' }}</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- Comment Section -->
        <div class="mt-12 bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-xl font-bold text-gray-900">Comments ({{ $product->comments->count() }})</h2>
            </div>
            
            <!-- Comments List -->
            <div class="divide-y divide-gray-200">
                @foreach($product->comments as $comment)
                    <div class="p-6">
                        <div class="flex items-start space-x-3">
                            <!-- User Avatar -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-700 font-semibold text-lg">{{ substr($comment->user->name, 0, 1) }}</span>
                                </div>
                            </div>
                            
                            <!-- Comment Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-medium text-gray-900">
                                        {{ $comment->user->name }}
                                    </h3>
                                    <p class="text-xs text-gray-500">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                
                                <p class="mt-2 text-sm text-gray-600">
                                    {{ $comment->content }}
                                </p>
                                
                                <!-- Comment Media -->
                                <div class="mt-3 space-y-3">
                                    @if($comment->photo)
                                        <div class="overflow-hidden rounded-lg cursor-pointer comment-photo">
                                            <img src="{{ asset('storage/' . $comment->photo) }}" 
                                                alt="Comment Photo" 
                                                class="w-20 h-20 object-cover">
                                        </div>
                                    @endif
                                                                    
                                    @if($comment->video)
                                        <div class="overflow-hidden rounded-lg cursor-pointer comment-video">
                                            <video class="w-20 h-20 object-cover" muted>
                                                <source src="{{ asset('storage/' . $comment->video) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- No Comments Message -->
            @if($product->comments->count() == 0)
                <div class="p-6 text-center">
                    <p class="text-gray-500">No comments yet. Be the first to share your thoughts!</p>
                </div>
            @endif
            
            <!-- Add Comment Button -->
            <div class="px-6 py-4 bg-gray-50">
                @auth
                    <button type="button" 
                            data-toggle="modal" 
                            data-target="#addCommentModal" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Comment
                    </button>
                @else
                    <a href="{{ route('login') }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Login to Comment
                    </a>
                @endauth
            </div>
        </div>

        <!-- Redesigned Add Comment Modal -->
        <div class="modal fade" id="addCommentModal" tabindex="-1" role="dialog" aria-labelledby="addCommentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form action="{{ route('product.addComment', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-gray-50 border-b border-gray-200">
                            <h5 class="modal-title text-gray-900 text-lg font-semibold" id="addCommentModalLabel">Share Your Thoughts</h5>
                            <button type="button" class="close text-gray-500 hover:text-gray-700" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-6">
                            <div class="mb-4">
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Your Comment</label>
                                <textarea name="content" 
                                        id="content" 
                                        rows="4" 
                                        placeholder="What do you think about this product?" 
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        required></textarea>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Add Photo (Optional)</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="photo" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                                    <span>Upload a photo</span>
                                                    <input id="photo" name="photo" type="file" accept="image/*" class="sr-only">
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="video" class="block text-sm font-medium text-gray-700 mb-1">Add Video (Optional)</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="video" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                                    <span>Upload a video</span>
                                                    <input id="video" name="video" type="file" accept="video/*" class="sr-only">
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500">MP4, MOV up to 10MB</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-gray-50 border-t border-gray-200 px-6 py-3">
                            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="ml-3 px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Post Comment
                            </button>
                        </div>
                    </div>
                </form>

                
            </div>
        </div>
    </div>
    
@include('partials.cart-modal')

<!-- Improved toast notification -->
<div id="toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300 flex items-center gap-2 z-50">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg>
    <span>Item added to cart successfully!</span>
</div>

<!-- Media Preview Modal -->
<div id="mediaModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 hidden z-50">
    <div class="relative bg-white rounded-lg shadow-lg max-w-3xl w-full mx-4">
        <button id="closeMediaModal" class="absolute top-2 right-2 text-gray-700 hover:text-gray-900 p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div id="mediaContent" class="p-4">
        </div>
    </div>
</div>

@endsection

<style>
    .photo-preview, .video-preview {
        position: relative;
        margin-top: 10px;
        border-radius: 0.375rem;
        overflow: hidden;
        max-width: 100%;
    }
    
    .photo-preview img, .video-preview video {
        display: block;
        max-width: 100%;
    }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview untuk upload di modal add comment
    const photoInput = document.getElementById('photo');
    const videoInput = document.getElementById('video');
    
    if (photoInput) {
        photoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    removeExistingPreview(photoInput);
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'mt-3 photo-preview';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'h-32 rounded-md object-cover';
                    const removeBtn = createRemoveButton(photoInput);
                    previewContainer.appendChild(img);
                    previewContainer.appendChild(removeBtn);
                    const uploadContainer = photoInput.closest('.border-dashed');
                    uploadContainer.parentNode.appendChild(previewContainer);
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    if (videoInput) {
        videoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    removeExistingPreview(videoInput);
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'mt-3 video-preview';
                    const video = document.createElement('video');
                    video.src = e.target.result;
                    video.className = 'h-32 w-full rounded-md';
                    video.controls = true;
                    const removeBtn = createRemoveButton(videoInput);
                    previewContainer.appendChild(video);
                    previewContainer.appendChild(removeBtn);
                    const uploadContainer = videoInput.closest('.border-dashed');
                    uploadContainer.parentNode.appendChild(previewContainer);
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    function createRemoveButton(inputElement) {
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 shadow-sm hover:bg-red-600 transition-colors';
        removeBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>';
        removeBtn.addEventListener('click', function() {
            inputElement.value = '';
            removeExistingPreview(inputElement);
        });
        return removeBtn;
    }
    
    function removeExistingPreview(inputElement) {
        const type = inputElement.id === 'photo' ? 'photo' : 'video';
        const previewClass = type + '-preview';
        const existingPreview = inputElement.closest('div').parentNode.querySelector('.' + previewClass);
        if (existingPreview) {
            existingPreview.remove();
        }
    }
    
    // Image Gallery Functionality
    document.querySelectorAll('.product-thumbnail img').forEach(img => {
        img.addEventListener('click', function() {
            const mainImage = document.querySelector('div.bg-gray-100 img');
            mainImage.src = this.src;
        });
    });
    
    function redirectToLogin() {
        window.location.href = '{{ route('login') }}';
    }
    
    window.showToast = function(message) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.classList.remove('translate-y-full', 'opacity-0');
        setTimeout(() => {
            toast.classList.add('translate-y-full', 'opacity-0');
        }, 3000);
    }
    
    function addToCart(productId) {
        fetch(`/cart/add/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const cartCount = document.getElementById('cartCount');
            cartCount.textContent = parseInt(cartCount.textContent) + 1;
            showToast(data.message);
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error adding product to cart');
        });
    }
    
    // Media Modal Functionality untuk komentar
    const mediaModal = document.getElementById('mediaModal');
    const mediaContent = document.getElementById('mediaContent');
    const closeMediaModal = document.getElementById('closeMediaModal');
    
    // Event listener untuk foto di komentar
    document.querySelectorAll('.comment-photo').forEach(elem => {
        elem.addEventListener('click', function() {
            const imgSrc = this.querySelector('img').src;
            openMediaModal('image', imgSrc);
        });
    });
    
    // Event listener untuk video di komentar
    document.querySelectorAll('.comment-video').forEach(elem => {
        elem.addEventListener('click', function() {
            const videoSrc = this.querySelector('video source').src;
            openMediaModal('video', videoSrc);
        });
    });
    
    function openMediaModal(type, src) {
        mediaContent.innerHTML = ''; // Bersihkan konten sebelumnya
        if (type === 'image') {
            const img = document.createElement('img');
            img.src = src;
            img.className = 'w-full h-auto object-contain';
            mediaContent.appendChild(img);
        } else if (type === 'video') {
            const video = document.createElement('video');
            video.src = src;
            video.controls = true;
            video.autoplay = true;
            video.className = 'w-full h-auto';
            mediaContent.appendChild(video);
        }
        mediaModal.classList.remove('hidden');
    }
    
    closeMediaModal.addEventListener('click', function() {
        mediaModal.classList.add('hidden');
        mediaContent.innerHTML = '';
    });
    
    // Klik di luar konten modal untuk menutup modal
    mediaModal.addEventListener('click', function(e) {
        if (e.target === mediaModal) {
            mediaModal.classList.add('hidden');
            mediaContent.innerHTML = '';
        }
    });
});
</script>
@endpush
