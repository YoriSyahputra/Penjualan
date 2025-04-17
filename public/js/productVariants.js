// public/js/productVariants.js
document.addEventListener('DOMContentLoaded', function() {
    const addVariantBtn = document.getElementById('add-variant');
    const variantsContainer = document.getElementById('variants-container');

    // Initial event listeners for existing remove buttons
    document.querySelectorAll('.remove-variant').forEach(button => {
        button.addEventListener('click', function() {
            if (variantsContainer.children.length > 1) {
                this.closest('.variant-group').remove();
            }
        });
    });
    
    addVariantBtn.addEventListener('click', function() {
        const newVariantGroup = `
            <div class="variant-group mb-3">
                <div class="flex gap-2">
                    <div class="w-3/5">
                        <input type="text" name="variants[]" 
                            class="w-full rounded-lg border-gray-300 mb-2" 
                            placeholder="Enter variant (e.g. Color, Size, Storage)">
                    </div>
                    <div class="w-2/5 relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp.</span>
                        <input type="number" name="variant_prices[]" 
                            class="w-full pl-7 rounded-lg border-gray-300 mb-2" 
                            step="0.01" min="0">
                    </div>
                    <button type="button" class="remove-variant px-2 py-1 text-red-600 hover:text-red-800">
                        ×
                    </button>
                </div>
            </div>
        `;
        variantsContainer.insertAdjacentHTML('beforeend', newVariantGroup);
        
        // Add event listener to new remove button
        const newRemoveBtn = variantsContainer.lastElementChild.querySelector('.remove-variant');
        newRemoveBtn.addEventListener('click', function() {
            if (variantsContainer.children.length > 1) {
                this.closest('.variant-group').remove();
            }
        });
    });
});