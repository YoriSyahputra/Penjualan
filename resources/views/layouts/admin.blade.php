<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/bootstrap.js'])
    <!-- Pastikan Chart.js hanya dimuat sekali -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<div class="fixed top-4 left-4 z-50 max-w-sm" id="notification-container" 
     x-data="{ 
        notifications: [],
        stockAlerts: {{ json_encode($stockAlerts ?? []) }},
        newOrders: {{ json_encode($newOrders ?? []) }},
        
        init() {
            // Initialize notifications array
            this.notifications = [];
            
            // Add stock alerts
            if (this.stockAlerts && this.stockAlerts.length > 0) {
                const alert = this.stockAlerts[0];
                this.notifications.push({
                    id: alert.id,
                    type: 'warning',
                    message: `Stock produk '${alert.name}' tersisa ${alert.stock} (di bawah batas minimum ${alert.stock_alert})${alert.count > 0 ? ' & ' + alert.count + ' produk lainnya' : ''}`
                });
            }
            
            // Add new orders
            if (this.newOrders && this.newOrders.length > 0) {
                const order = this.newOrders[0];
                const formattedTotal = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(order.total);
                this.notifications.push({
                    id: order.id,
                    type: 'success',
                    message: `Order baru #${order.order_number} dengan total ${formattedTotal}${order.count > 0 ? ' & ' + order.count + ' orderan lainnya' : ''}`
                });
            }
        },
        
        removeNotification(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
     }">
    
    <template x-for="notification in notifications" :key="notification.id">
        <div class="bg-white border-l-4 mb-3 p-4 shadow-md rounded-r-lg transition-all duration-300 transform"
             :class="{
                'border-yellow-500': notification.type === 'warning',
                'border-green-500': notification.type === 'success'
             }">
            <div class="flex justify-between items-start">
                <div class="flex">
                    <div class="flex-shrink-0 mr-3" :class="{
                        'text-yellow-500': notification.type === 'warning',
                        'text-green-500': notification.type === 'success'
                    }">
                        <svg x-show="notification.type === 'warning'" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <svg x-show="notification.type === 'success'" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-1">
                        <p class="text-sm">
                            <template x-if="notification.type === 'warning'">
                                <span x-text="notification.message"></span>
                            </template>
                            <template x-if="notification.type === 'success'">
                                <span x-text="notification.message"></span>
                            </template>
                        </p>
                    </div>
                </div>
                <button @click="removeNotification(notification.id)" class="ml-4 text-gray-400 hover:text-gray-600">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>
<body class="antialiased">
@include('partials.notification')
    <div x-data="{ sidebarOpen: false }">
        <!-- Backdrop with blur effect -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/20 backdrop-blur-sm z-30"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>

        <aside class="fixed top-0 left-0 z-40 h-screen w-64 bg-white shadow-lg transform transition-transform duration-200 ease-in-out"
               :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
            <div class="h-full flex flex-col">
                <div class="flex items-center justify-center h-16 bg-indigo-600">
                    <a href="/" class="text-2xl font-bold text-white">{{ auth()->user()->store->name }}</a>
                </div>
                <nav class="flex-1 overflow-y-auto py-4 px-3">
                    <ul class="space-y-2">
                        <li>
                            <a href="/dashboard" 
                               class="flex items-center px-4 py-2 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <a href="/dashboard/list-sale" 
                            class="flex items-center px-4 py-2 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors {{ request()->routeIs('dashboard.list_sale') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Products
                        </a>
                        <a href="{{ route('dashboard.orders.index') }}" 
                            class="flex items-center px-4 py-2 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors {{ request()->routeIs('dashboard.orders.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                Orders
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.customers') }}" 
                               class="flex items-center px-4 py-2 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors {{ request()->routeIs('dashboard.customers') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Customers
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.profile') }}" 
                               class="flex items-center px-4 py-2 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors {{ request()->routeIs('store.profile') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Store Profile
                            </a>
                        </li>
                        <li>
                            <a href="/" 
                            class="flex items-center px-4 py-2 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors {{ request()->routeIs('admin.customers') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back To Shop
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content - removed pl-64 class -->
        <div class="flex flex-col min-h-screen">
            <!-- Header -->
            <header class="bg-white shadow-sm z-30">
                <div class="flex items-center justify-between px-4 py-3">
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="text-gray-500 hover:text-indigo-600 focus:outline-none transform transition-transform duration-200"
                            :class="{'translate-x-64': sidebarOpen}">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                    </button>

                    <div class="flex items-center space-x-4">
                        <div class="relative" x-data="{ isOpen: false }">
                            <button @click="isOpen = !isOpen" class="flex items-center space-x-2 focus:outline-none">
                                @if(Auth::user()->profile_photo_url)
                                    <img src="{{ Auth::user()->profile_photo_url }}" 
                                         alt="Profile" 
                                         class="h-8 w-8 rounded-full object-cover">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                                <span class="text-gray-700">{{ Auth::user()->name }}</span>
                            </button>

                            <div x-show="isOpen" 
                                 @click.away="isOpen = false"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5">
                                <a href="profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @vite('resources/js/app.js')
    @stack('scripts')
    
    <script>
        // Inisialisasi Alpine.js dan pastikan sidebar sudah siap
        document.addEventListener('alpine:init', () => {
            // Semua komponen Alpine.js sudah siap
            console.log('Alpine.js initialized');
        });
        document.addEventListener('alpine:init', () => {
    // Auto dismiss notifications after 10 seconds
    Alpine.effect(() => {
        const notificationContainer = document.getElementById('notification-container');
        if (notificationContainer && notificationContainer.__x) {
            const instance = notificationContainer.__x;
            
            // Set timeout untuk setiap notifikasi yang ada
            if (instance.notifications && instance.notifications.length > 0) {
                instance.notifications.forEach(notification => {
                    if (!notification.timeoutSet) {
                        notification.timeoutSet = true;
                        setTimeout(() => {
                            instance.removeNotification(notification.id);
                            instance.$nextTick(() => {
                                instance.$el.dispatchEvent(new CustomEvent('notification-removed', { 
                                    detail: { id: notification.id }
                                }));
                            });
                        }, 10000); // 10 detik
                    }
                });
            }
        }
    });
});
    </script>
</body>
</html>