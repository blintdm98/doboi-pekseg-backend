<!-- resources/views/livewire/admin/dashboard.blade.php -->
<div class="space-y-6">

    <div class="mb-8 flex justify-between">
        <h2 class="text-gray-800 dark:text-gray-200">{{ __('common.dashboard') }}</h2>
    </div>

    {{-- Szűrők --}}
    <div class="space-y-4" x-data="{ showFilters: window.innerWidth >= 768 }" x-init="window.addEventListener('resize', () => { showFilters = window.innerWidth >= 768 })">
        <div class="flex flex-col gap-4">
            <!-- Szűrők lenyitó gomb csak mobilon -->
            <button type="button" class="md:hidden px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-200 text-sm" @click="showFilters = !showFilters">
                <span x-show="!showFilters">Szűrők mutatása</span>
                <span x-show="showFilters">Szűrők elrejtése</span>
            </button>
        </div>
        <!-- Szűrők: csak ha showFilters -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:gap-4" x-show="showFilters" x-transition>
            <x-datetime-picker
                label="{{ __('common.date_from') }}"
                placeholder="{{ __('common.select_date') }}"
                without-time
                icon="calendar"
                wire:model.live="dateStart"
                class="w-full md:flex-1"
            />

            <x-datetime-picker
                label="{{ __('common.date_to') }}"
                placeholder="{{ __('common.select_date') }}"
                without-time
                icon="calendar"
                wire:model.live="dateEnd"
                class="w-full md:flex-1"
            />

            <x-select
                label="{{ __('common.store') }}"
                placeholder="{{ __('common.store') }}"
                wire:model.live="storeFilter"
                class="w-full md:flex-1"
            >
                <x-select.option value="">{{ __('common.nofilter') }}</x-select.option>
                @foreach($stores as $store)
                    <x-select.option value="{{ $store->id }}">{{ $store->name }}</x-select.option>
                @endforeach
            </x-select>

            <x-select
                label="{{ __('common.user') }}"
                placeholder="{{ __('common.user') }}"
                wire:model.live="userFilter"
                class="w-full md:flex-1"
            >
                <x-select.option value="">{{ __('common.nofilter') }}</x-select.option>
                @foreach($users as $user)
                    <x-select.option value="{{ $user->id }}">{{ $user->name }}</x-select.option>
                @endforeach
            </x-select>

            <x-select
                label="{{ __('common.product') }}"
                placeholder="{{ __('common.product') }}"
                wire:model.live="productFilter"
                class="w-full md:flex-1"
            >
                <x-select.option value="">{{ __('common.nofilter') }}</x-select.option>
                @foreach($products as $product)
                    <x-select.option value="{{ $product->id }}">{{ $product->name }}</x-select.option>
                @endforeach
            </x-select>
        </div>
    </div>

    {{-- Grafikon --}}
    <div class="p-4 rounded shadow">
        <x-card>
            <h3 class="text-xl font-bold mb-2">{{ $this->getChartTitle() }}</h3>
            <canvas id="ordersChart" height="100"></canvas>
        </x-card>
    </div>

    <style>
        @media (max-width: 640px) {
            #ordersChart {
                width: 100% !important;
                display: block;
                height: 220px !important;
            }
        }

        .flatpickr-calendar {
            z-index: 99999 !important;
            position: fixed !important;
        }

        @media (max-width: 768px) {
            .flatpickr-calendar {
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                max-width: 90vw !important;
                max-height: 90vh !important;
            }
        }

        @media (min-width: 769px) {
            .flatpickr-calendar {
                position: fixed !important;
                z-index: 99999 !important;
                right: 20px !important;
                left: auto !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
            }
        }

        .flatpickr-calendar.open {
            z-index: 99999 !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let ordersChartInstance = null;

        function renderOrdersChart(labels = null, data = null) {
            const canvas = document.getElementById('ordersChart');
            if (!canvas) return;

            if (ordersChartInstance) {
                ordersChartInstance.destroy();
            }

            const chartLabels = labels || @json($chartLabels);
            const chartData = data || @json($chartData);

            console.log('renderOrdersChart called with:', { labels, data });
            console.log('Final chart data:', { chartLabels, chartData });

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
            ordersChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Rendelések száma',
                        data: chartData,
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

            Livewire.hook('message.processed', () => {
                console.log('Livewire message processed - re-rendering chart');
                setTimeout(() => {
                    renderOrdersChart();
                }, 100);
            });

            document.addEventListener('chartDataUpdated', (event) => {
                console.log('Chart data updated event received:', event.detail);
                const chartData = event.detail[0];
                console.log('Using labels:', chartData.labels);
                console.log('Using data:', chartData.data);
                renderOrdersChart(chartData.labels, chartData.data);
            });

            // Debug üzenetek
            console.log('Dashboard component loaded');
            console.log('Initial chart data:', @json($chartData));
        });
    </script>

</div>    

