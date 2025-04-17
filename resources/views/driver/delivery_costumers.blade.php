@extends('layouts.driver')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-semibold mb-6">Customer List</h2>

    <div class="grid gap-6">
        @foreach($deliveries as $delivery)
            <div class="border rounded-lg p-4 flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-lg">{{ $delivery->order->user->name }}</h3>
                    <p class="text-gray-600">Order #{{ $delivery->order->order_number }}</p>
                    <p class="text-gray-600">{{ $delivery->order->user->phone_number }}</p>
                </div>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $delivery->order->user->phone_number) }}" 
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    </svg>
                    WhatsApp
                </a>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $deliveries->links() }}
    </div>
</div>
@endsection
