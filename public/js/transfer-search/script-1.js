document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchUser');
    const searchResults = document.getElementById('searchResults');
    const userResults = document.getElementById('userResults');
    const userResultsContent = document.getElementById('userResultsContent');
    let searchTimeout;

    // Add loading animation
    function showLoadingState() {
        userResultsContent.innerHTML = `
            <div class="flex items-center justify-center py-8 animate-pulse">
                <div class="space-y-4 w-full max-w-md">
                    ${Array(3).fill().map(() => `
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>`;
    }

    function displaySearchResults(users) {
        // Clear previous results
        userResultsContent.innerHTML = '';
        
        if (users.length === 0) {
            userResultsContent.innerHTML = `
                <div class="text-center py-8 opacity-0 transform translate-y-4 transition-all duration-300">
                    <div class="text-gray-400 mb-2">
                        <svg class="w-12 h-12 mx-auto animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500">No users found matching "${searchInput.value}"</p>
                </div>`;
            
            // Trigger animation
            requestAnimationFrame(() => {
                userResultsContent.firstElementChild.classList.remove('opacity-0', 'translate-y-4');
            });
        } else {
            users.forEach((user, index) => {
                const userElement = document.createElement('div');
                userElement.className = 'flex items-center justify-between p-4 bg-white rounded-xl hover:bg-gray-50 transition-all duration-300 shadow-sm hover:shadow-md opacity-0 transform translate-y-4 mb-3';
                userElement.style.transitionDelay = `${index * 100}ms`;
                
                userElement.innerHTML = `
                    <div class="flex items-center">
                        <div class="relative">
                            <img src="${user.profile_photo_url}" 
                                alt="${user.name}" 
                                class="w-12 h-12 rounded-full border-2 border-white shadow-sm transition-transform duration-300 hover:scale-110">
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
                        </div>
                        <div class="ml-4">
                            <p class="font-semibold text-gray-900 hover:text-indigo-600 transition-colors">${user.name}</p>
                            <p class="text-sm text-gray-500">${user.email}</p>
                        </div>
                    </div>
                    <a href="/transfer/amount/${user.id}" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-all duration-300 hover:shadow-lg hover:scale-105 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Transfer Money
                    </a>`;
                
                userResultsContent.appendChild(userElement);
                
                // Trigger animation
                requestAnimationFrame(() => {
                    userElement.classList.remove('opacity-0', 'translate-y-4');
                });
            });
        }

        // Show results container with fade-in
        userResults.classList.remove('hidden');
        userResults.classList.add('opacity-0');
        requestAnimationFrame(() => {
            userResults.classList.remove('opacity-0');
            userResults.classList.add('opacity-100', 'transition-opacity', 'duration-300');
        });
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 3) {
            userResults.classList.add('hidden');
            return;
        }

        showLoadingState();
        userResults.classList.remove('hidden');
        
        searchTimeout = setTimeout(() => {
            fetch(`/api/search-users?q=${query}`)
                .then(response => response.json())
                .then(users => {
                    displaySearchResults(users);
                })
                .catch(error => {
                    console.error('Error:', error);
                    userResultsContent.innerHTML = `
                        <div class="text-center py-8 text-red-500">
                            <p>An error occurred while searching. Please try again.</p>
                        </div>`;
                });
        }, 300);
    });

    // Smooth fade-out when input is cleared
    searchInput.addEventListener('change', function() {
        if (this.value.trim() === '') {
            userResults.classList.add('opacity-0');
            setTimeout(() => {
                userResults.classList.add('hidden');
            }, 300);
        }
    });

    // Tab switching functionality
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
        
        tabContents.forEach(content => {
            content.classList.add('hidden');
        });
        
        const selectedContent = document.getElementById(`${tabName}-section`);
        selectedContent.classList.remove('hidden');
        selectedContent.classList.add('animate-fadeIn');
    }
});