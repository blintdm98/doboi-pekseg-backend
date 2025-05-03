<!-- resources/views/livewire/admin/dashboard.blade.php -->
<div class="space-y-6">
    {{-- Statisztikai kártyák --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-card title="Összes rendelés" :value="$totalOrders" icon="shopping-cart" />
        <x-card title="Összes termék" :value="$totalProductsOrdered" icon="package" />
        <x-card title="Összes bevétel" :value="$totalRevenue . ' lej'" icon="credit-card" />
        <x-card title="Top bolt" :value="$topStoreName" icon="store" />
    </div>

    {{-- Grafikon --}}
    <div class="p-4 rounded shadow">
        <x-card>
            <h3 class="text-xl font-bold mb-2">Rendelések időbeli alakulása</h3>
            <div id="ordersChart"></div>
        </x-card>
    </div>

    {{-- Top 5 termék táblázat --}}
    <div class="p-4 rounded shadow">
        <x-card>
            <h3 class="text-xl font-bold mb-2">Legnépszerűbb termékek</h3>
            <table class="w-full table-auto">
                <thead>
                    <tr class="text-left">
                        <th>#</th>
                        <th>Termék</th>
                        <th>Rendelt mennyiség</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $i => $product)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>
    </div>
</div>
