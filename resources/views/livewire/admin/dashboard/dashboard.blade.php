<!-- resources/views/livewire/admin/dashboard.blade.php -->
<div class="space-y-6">
    {{-- Statisztikai kártyák --}}
    <div class="p-4 rounded shadow">
        <x-card title="Top 3 bolt" icon="store">
            <ul class="space-y-1 text-xl">
            @php
            $emojis = ['🥇', '🥈', '🥉'];
            @endphp

            @forelse($topStores as $index => $store)
                <li class="flex justify-between items-center">
                    <span class="text-xl">{{ $emojis[$index] ?? '🏅' }} {{ $store->name }}</span>
                    <span class="text-xl text-gray-500">{{ $store->total }} rendelés</span>
                </li>
            @empty
                <li class="text-gray-400">Nincs adat</li>
            @endforelse
            </ul>
        </x-card>
    </div>

    {{-- Grafikon --}}
    <div class="p-4 rounded shadow">
        <x-card>
            <h3 class="text-xl font-bold mb-2">Rendelések időbeli alakulása</h3>
            <canvas id="ordersChart" height="100"></canvas>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('ordersChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels), // pl. ['2024-04-27', '2024-04-28']
                datasets: [{
                    label: 'Rendelések száma',
                    data: @json($chartData), // pl. [4, 9]
                    backgroundColor: 'rgba(75, 192, 192, 0.3)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>

