// public/js/imagePreview.js
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('images');
    const preview = document.getElementById('image-preview');
    
    imageInput.addEventListener('change', function(event) {
        const files = Array.from(event.target.files);
        
        files.forEach(file => {
            if (!file.type.startsWith('image/')) return;
            
            const reader = new FileReader();
            const div = document.createElement('div');
            div.className = 'relative';
            
            reader.onload = function(e) {
                div.innerHTML = `
                    <div class="relative group">
                        <img src="${e.target.result}" class="w-full h-32 object-cover rounded">
                        <button type="button" class="remove-image absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            ×
                        </button>
                    </div>
                `;
                preview.appendChild(div);
                
                // Add click handler for remove button
                div.querySelector('.remove-image').addEventListener('click', function() {
                    div.remove();
                    
                    // Update FileList
                    const dt = new DataTransfer();
                    const currentFiles = Array.from(imageInput.files);
                    currentFiles.forEach((file, i) => {
                        if (i !== Array.from(preview.children).indexOf(div)) {
                            dt.items.add(file);
                        }
                    });
                    imageInput.files = dt.files;
                });
            };
            
            reader.readAsDataURL(file);
        });
    });
});