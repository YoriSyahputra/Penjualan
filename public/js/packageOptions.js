// public/js/packageOptions.js
document.addEventListener('DOMContentLoaded', function() {
    const packagesContainer = document.getElementById('packages-container');
    const addPackageBtn = document.getElementById('add-package');

    // Initial event listeners for existing remove buttons
    document.querySelectorAll('.remove-package').forEach(button => {
        button.addEventListener('click', function() {
            if (packagesContainer.children.length > 1) {
                this.closest('.package-group').remove();
            }
        });
    });
    
    addPackageBtn.addEventListener('click', function() {
        const newPackageGroup = `
            <div class="package-group mb-3">
                <div class="flex gap-2">
                    <input type="text" name="packages[]" 
                           class="w-full rounded-lg border-gray-300 mb-2" 
                           placeholder="Enter package name">
                    <button type="button" class="remove-package px-2 py-1 text-red-600 hover:text-red-800">
                        ×
                    </button>
                </div>
            </div>
        `;
        packagesContainer.insertAdjacentHTML('beforeend', newPackageGroup);
        
        // Add event listener to new remove button
        const newRemoveBtn = packagesContainer.lastElementChild.querySelector('.remove-package');
        newRemoveBtn.addEventListener('click', function() {
            if (packagesContainer.children.length > 1) {
                this.closest('.package-group').remove();
            }
        });
    });
});