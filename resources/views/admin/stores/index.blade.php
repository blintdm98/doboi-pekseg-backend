@extends('layouts.master')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Üzletek</h1>

    <table class="min-w-full bg-white rounded-lg shadow">
        <thead>
            <tr class="bg-gray-100 text-left text-sm text-gray-700 uppercase tracking-wider">
                <th class="px-4 py-2">#</th>
                <th class="px-4 py-2">Név</th>
                <th class="px-4 py-2">Cím</th>
                <th class="px-4 py-2">Logo</th>
                <th class="px-4 py-2">Hozzáadva</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stores as $store)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $store->id }}</td>
                    <td class="px-4 py-2">{{ $store->name }}</td>
                    <td class="px-4 py-2">{{ $store->address }}</td>
                    <td class="px-4 py-2">
                        @if($store->logo)
                            <img src="{{ $store->logo }}" alt="Logo" class="w-12 h-12 rounded-full object-cover">
                        @else
                            <span class="text-gray-400 text-sm italic">Nincs kép</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $store->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
