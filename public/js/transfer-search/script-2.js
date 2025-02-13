document.addEventListener('DOMContentLoaded', function() {
    window.switchTab = function(tabName) {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.classList.remove('active', 'bg-indigo-500', 'text-white');
            button.classList.add('bg-gray-100', 'text-gray-600');
        });
        
        const selectedTab = document.getElementById(`${tabName}-tab`);
        selectedTab.classList.add('active', 'bg-indigo-500', 'text-white');
        selectedTab.classList.remove('bg-gray-100', 'text-gray-600');
        
        const selectedContent = document.getElementById(`${tabName}-section`);
        selectedContent.classList.remove('hidden');
        selectedContent.classList.add('animate-fadeIn');
    }
});
