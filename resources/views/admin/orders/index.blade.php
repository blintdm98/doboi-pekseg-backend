@extends('layouts.master')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Rendelések</h1>
    <table class="min-w-full bg-white rounded-lg shadow">
        <thead>
            <tr class="bg-gray-100 text-left text-sm text-gray-700 uppercase tracking-wider">
                <th class="px-4 py-2">#</th>
                <th class="px-4 py-2">Üzlet</th>
                <th class="px-4 py-2">Felhasználó</th>
                <th class="px-4 py-2">Státusz</th>
                <th class="px-4 py-2">Dátum</th>
                <th class="px-4 py-2">Részletek</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $order->id }}</td>
                    <td class="px-4 py-2">{{ $order->store->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $order->user->name ?? '-' }}</td>
                    <td class="px-4 py-2">
                        <span class="inline-block px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-2">
                        <div x-data="{ open: false }">
                            <button 
                                @click="open = true" 
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
                            >
                                Megnéz
                            </button>

                            @include('components.modal', ['order' => $order])
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
