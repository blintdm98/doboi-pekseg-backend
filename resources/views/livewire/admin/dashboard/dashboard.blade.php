<!-- resources/views/livewire/admin/dashboard.blade.php -->
<div class="space-y-6">

    <div class="mb-8 flex justify-between">
        <h2 class="text-gray-800 dark:text-gray-200">{{ __('common.dashboard') }}</h2>
    </div>

    {{-- Statisztikai kártyák --}}
    {{--
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
    --}}

    {{-- Grafikon --}}
    <div class="p-4 rounded shadow">
        <x-card>
            <h3 class="text-xl font-bold mb-2">Rendelések időbeli alakulása</h3>
            <canvas id="ordersChart" height="100"></canvas>
        </x-card>
    </div>

    {{-- Top 5 termék táblázat --}}
    {{--
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
    --}}

    <style>
        @media (max-width: 640px) {
            #ordersChart {
                width: 100% !important;
                display: block;
                height: 220px !important;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function renderOrdersChart() {
            const canvas = document.getElementById('ordersChart');
            if (window.ordersChartInstance) {
                window.ordersChartInstance.destroy();
            }
            let chartOptions = {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            };

            if (window.innerWidth <= 640) {
                canvas.height = 220;
                chartOptions.scales.x = {
                    ticks: {
                        font: {
                            size: 6
                        }
                    }
                };
            } else {
                canvas.height = 100;
            }
            const ctx = canvas.getContext('2d');
            window.ordersChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Rendelések száma',
                        data: @json($chartData),
                        backgroundColor: 'rgba(75, 192, 192, 0.3)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: chartOptions
            });
        }
        document.addEventListener('DOMContentLoaded', function () {
            renderOrdersChart();
            window.addEventListener('resize', function () {
                renderOrdersChart();
            });
        });
    </script>
</div>    

