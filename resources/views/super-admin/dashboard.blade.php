@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <h2 class="text-2xl font-semibold mb-4">Admin Management</h2>

    <!-- Pending Admins Section -->
    <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-indigo-50 to-blue-50">
            <h3 class="text-lg font-semibold mb-4">Pending Admin Approvals</h3>
            @if($pendingAdmins->isEmpty())
                <p class="text-gray-500 text-center py-4">No pending admin approvals</p>
            @else
                <div class="space-y-4">
                    @foreach($pendingAdmins as $admin)
                        <div 
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform -translate-x-full"
                            class="bg-white border-l-4 border-blue-500 p-4 rounded-lg shadow-md flex justify-between items-center"
                        >
                            <div>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="font-bold text-gray-800">{{ $admin->name }}</p>
                                    <p class="text-gray-600">{{ $admin->email }}</p>
                                    @if($admin->store)
                                        <p class="text-sm text-gray-500">
                                            Store: {{ $admin->store->name }}
                                            <span class="ml-2 text-xs text-gray-400">
                                                Category: {{ $admin->store->category }}
                                            </span>
                                        </p>
                                    @else
                                        <span class="text-gray-500">No store information</span>
                                    @endif
                                </td>
                            </div>
                            <div class="flex space-x-2">
                                <form 
                                    action="{{ route('super-admin.approve', $admin) }}" 
                                    method="POST" 
                                    class="transition transform hover:scale-105"
                                >
                                    @csrf
                                    <button 
                                        type="submit" 
                                        class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600"
                                    >
                                        Approve
                                    </button>
                                </form>
                                <form 
                                    action="{{ route('super-admin.reject', $admin) }}" 
                                    method="POST"
                                    class="transition transform hover:scale-105"
                                >
                                    @csrf
                                    <button 
                                        type="submit" 
                                        class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600"
                                    >
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Approved Admins Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50">
            <h3 class="text-lg font-semibold mb-4">Approved Admins</h3>
            @if($approvedAdmins->isEmpty())
                <p class="text-gray-500 text-center py-4">No approved admins</p>
            @else
                <div class="space-y-4">
                    @foreach($approvedAdmins as $admin)
                        <div 
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform translate-x-full"
                            x-transition:enter-end="opacity-100 transform translate-x-0"
                            class="bg-white border-l-4 border-green-500 p-4 rounded-lg shadow-md flex justify-between items-center"
                        >
                            <div>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="font-bold text-gray-800">{{ $admin->name }}</p>
                                    <p class="text-gray-600">{{ $admin->email }}</p>
                                    @if($admin->store)
                                        <p class="text-sm text-gray-500">
                                            Store: {{ $admin->store->name }}
                                            <span class="ml-2 text-xs text-gray-400">
                                                Category: {{ $admin->store->category }}
                                            </span>
                                        </p>
                                    @else
                                        <span class="text-gray-500">No store information</span>
                                    @endif
                                </td>
                            </div>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                Approved
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection