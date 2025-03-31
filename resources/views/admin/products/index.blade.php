@extends('layouts.master')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Termékek</h1>

    <table class="min-w-full bg-white rounded-lg shadow">
        <thead>
            <tr class="bg-gray-100 text-left text-sm text-gray-700 uppercase tracking-wider">
                <th class="px-4 py-2">#</th>
                <th class="px-4 py-2">Név</th>
                <th class="px-4 py-2">Ár (lej)</th>
                <th class="px-4 py-2">Műveletek</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $product->id }}</td>
                    <td class="px-4 py-2">{{ $product->name }}</td>
                    <td class="px-4 py-2">{{ number_format($product->price, 2) }}</td>
                    <td class="px-6 py-4">
                            <button class="text-blue-500 hover:underline">Szerkeszt</button>
                            <button class="text-red-500 hover:underline ml-3">Törlés</button>
                        </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
