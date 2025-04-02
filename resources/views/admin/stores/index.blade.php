@extends('layouts.master')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Üzletek</h1>
    <div x-data="storeTable()" x-init="loadStores()">
        <button 
            @click="openModal()"
            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700 transition mb-4"
        >
                + Új üzlet hozzáadása
        </button>

        @include('components.store_modal')

        <table class="min-w-full bg-white rounded-lg shadow">
            <thead>
                <tr class="bg-gray-100 text-left text-sm text-gray-700 uppercase tracking-wider">
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Név</th>
                    <th class="px-4 py-2">Cím</th>
                    <th class="px-4 py-2">Logo</th>
                    <th class="px-4 py-2">Hozzáadva</th>
                    <th class="px-4 py-2">Műveletek</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="store in stores" :key="store.id">
                    <tr class="border-t">
                        <td class="px-4 py-2" x-text="store.id"></td>
                        <td class="px-4 py-2" x-text="store.name"></td>
                        <td class="px-4 py-2" x-text="store.address"></td>
                        <td class="px-4 py-2">
                            <template x-if="store.logo">
                                <img :src="store.logo" alt="Logo" class="w-12 h-12 rounded-full object-cover">
                            </template>
                            <template x-if="!store.logo">
                                <span class="text-gray-400 text-sm italic">Nincs kép</span>
                            </template>
                        </td>
                        <td class="px-4 py-2" x-text="new Date(store.created_at).toLocaleDateString()"></td>
                        <td class="px-4 py-2">
                            <button @click="openModal(store)" class="bg-blue-600 text-white px-2 py-1 rounded mr-2">Szerkeszt</button>
                            <button @click="deleteStore(store.id)" class="bg-red-600 text-white px-2 py-1 rounded">Törlés</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
@endsection
