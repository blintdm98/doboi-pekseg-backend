<div>
    <div class="mb-8 flex justify-between">
        <h2 class="text-gray-800 dark:text-gray-200">{{ __('common.orders') }}</h2>
    </div>

    <div class="space-y-4">
        <div class="flex items-center gap-4">
                <x-input
                    label="{{ __('common.search_placeholder') }}"
                    placeholder="{{ __('common.search_placeholder') }}"
                    wire:model.live.debounce.500ms="search"
                    class="w-full md:w-1/3"
                />
                <x-select
                    label="{{ __('common.status') }}"
                    placeholder="{{ __('common.status') }}"
                    wire:model.live="statusFilter"
                    class="w-full md:w-1/3"
                >
                    <x-select.option value="">{{ __('common.nofilter') }}</x-select.option>
                    <x-select.option value="pending">{{ __('common.status_pending') }}</x-select.option>
                    <x-select.option value="partial">{{ __('common.status_partial') }}</x-select.option>
                    <x-select.option value="completed">{{ __('common.status_completed') }}</x-select.option>
                    <x-select.option value="canceled">{{ __('common.status_canceled') }}</x-select.option>
                </x-select>

                <x-select
                    label="{{ __('common.store') }}"
                    placeholder="{{ __('common.store') }}"
                    wire:model.live="storeFilter"
                    class="w-full md:w-1/3"
                >
                    <x-select.option value="">{{ __('common.nofilter') }}</x-select.option>
                    @foreach(\App\Models\Store::orderBy('name')->get() as $store)
                        <x-select.option value="{{ $store->id }}">{{ $store->name }}</x-select.option>
                    @endforeach
                </x-select>

                <x-select
                    label="{{ __('common.user') }}"
                    placeholder="{{ __('common.user') }}"
                    wire:model.live="userFilter"
                    class="w-full md:w-1/3"
                >
                    <x-select.option value="">{{ __('common.nofilter') }}</x-select.option>
                    @foreach(\App\Models\User::orderBy('name')->get() as $user)
                        <x-select.option value="{{ $user->id }}">{{ $user->name }}</x-select.option>
                    @endforeach
                </x-select>

                <x-datetime-picker
                    label="{{ __('common.date_from') }}"
                    placeholder="{{ __('common.select_date') }}"
                    without-time
                    icon="calendar"
                    wire:model.live="dateStart"
                    class="w-full md:w-1/4"
                />

                <x-datetime-picker
                    label="{{ __('common.date_to') }}"
                    placeholder="{{ __('common.select_date') }}"
                    without-time
                    icon="calendar"
                    wire:model.live="dateEnd"
                    class="w-full md:w-1/4"
                />


        </div>
        <x-table>
            <x-slot:head>
                <x-table.th>#</x-table.th>
                <x-table.th>{{ __('common.store') }}</x-table.th>
                <x-table.th>{{ __('common.total') }}</x-table.th>
                <x-table.th>{{ __('common.comment') }}</x-table.th>
                <x-table.th>{{ __('common.status') }}</x-table.th>
                <x-table.th>{{ __('common.user') }}</x-table.th>
                <x-table.th>{{ __('common.date') }}</x-table.th>
                <x-table.th>{{ __('common.actions') }}</x-table.th>
            </x-slot:head>

            @forelse($orders as $order)
                <x-table.tr>
                    <x-table.td>{{ $order->id }}</x-table.td>
                    <x-table.td>{{ $order->store->name ?? 'Törölt bolt' }}</x-table.td>
                    <x-table.td>
                        {{
                            $order->orderDetails->sum(function($detail) {
                                return $detail->quantity * ($detail->product->price ?? 0);
                            })
                        }} lej
                    </x-table.td>
                    <x-table.td>{{ $order->comment }}</x-table.td>
                    <x-table.td>
                    @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'partial' => 'bg-orange-100 text-orange-800',
                                'canceled' => 'bg-red-100 text-red-800',
                            ];
                        @endphp

                        <span class="px-2 py-1 rounded text-sm font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ __('common.status_' . $order->status) }}
                        </span>
                    </x-table.td>
                    <x-table.td>{{ $order->user->name ?? 'N/A' }}</x-table.td>
                    <x-table.td>{{ $order->created_at->format('Y-m-d H:i') }}</x-table.td>
                    <x-table.td>
                        <div class="flex gap-2">
                            <x-button info label="{{ __('common.details') }}" wire:click="showOrder({{ $order->id }})"/>
                            <x-button
                                negative
                                icon="trash"
                                wire:click="permanentlyDeleteOrder({{ $order->id }})"
                                wire:confirm="Biztosan törölni szeretnéd ezt a rendelést? Ez a művelet nem vonható vissza!"
                            />
                        </div>
                    </x-table.td>
                </x-table.tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-sm text-gray-500 py-6">
                        {{ __('common.no_results_found') }}
                    </td>
                </tr>
            @endforelse
        </x-table>
    </div>                        
    {{ $orders->links() }}

    {{-- Modal --}}
    <x-modal-card blur="md" wire:model="orderModal">
        @if($selectedOrder)
            <div class="space-y-4">
                <p><strong>{{ __('common.order') }} #{{ $selectedOrder->id }}</strong></p>
                <p><strong>{{ __('common.store') }}:</strong> {{ $selectedOrder->store->name ?? 'Törölt bolt' }}</p>
                <p><strong>{{ __('common.status') }}:</strong>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'partial' => 'bg-orange-100 text-orange-800',
                            'canceled' => 'bg-red-100 text-red-800',
                        ];
                    @endphp

                    @php
                        $total = $selectedOrder->orderDetails->sum(function($detail) {
                            return $detail->quantity * ($detail->product->price ?? 0);
                        });
                    @endphp
                    <span class="px-2 py-1 rounded text-sm font-medium {{ $statusColors[$selectedOrder->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ __('common.status_' . $selectedOrder->status) }}
                    </span>
                </p>
                <p><strong>{{ __('common.total') }}:</strong> {{ $total }} lej</p>
                @if($selectedOrder->comment)
                    <p><strong>{{ __('common.comment') }}:</strong> {{ $selectedOrder->comment }}</p>
                @endif
                <div class="space-y-2 max-h-96 overflow-y-auto pr-2 bg-transparent">
                    <div class="flex justify-between items-center gap-4 font-semibold text-sm text-gray-600">
                        <span>Termék</span>
                        <span>Kiküldött</span>
                    </div>
                    @foreach($orderDetails as $index => $detail)
                        <div class="flex justify-between items-center gap-4">
                            <span>{{ $detail['product_name'] }} – {{ $detail['quantity'] }} db</span>
                            <div class="w-28">
                                <x-input
                                    type="number"
                                    wire:model.defer="orderDetails.{{ $index }}.dispatched_quantity"
                                    :disabled="$selectedOrder && ($selectedOrder->status === 'completed' || $selectedOrder->status === 'canceled')"
                                />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($selectedOrder && !in_array($selectedOrder->status, ['completed', 'canceled']))
            @if (!$showAddProduct)
                <x-button
                    primary
                    label="Termék hozzáadása"
                    wire:click="$set('showAddProduct', true)"
                />
            @else
                <div class="flex gap-2 items-end">
                    <x-select
                        label="Termék"
                        wire:model="newProductId"
                        placeholder="Válassz terméket"
                        class="w-full"
                    >
                        <x-select.option value="">--</x-select.option>
                        @foreach($availableProducts as $product)
                            <x-select.option value="{{ $product->id }}">{{ $product->name }}</x-select.option>
                        @endforeach
                    </x-select>

                    <x-input
                        type="number"
                        label="Darab"
                        wire:model="newProductQuantity"
                        min="0"
                        class="w-24"
                    />

                    <x-button
                        primary
                        label="Hozzáadás"
                        wire:click="addProductToOrder"
                    />

                    <x-button
                        flat
                        label="Mégse"
                        wire:click="$set('showAddProduct', false)"
                    />
                </div>
            @endif
        @endif

        <x-slot name="footer">
            <div class="flex justify-between items-center w-full">
                <div class="flex gap-2">
                    <x-button flat label="{{ __('common.cancel') }}" wire:click="$set('orderModal', false)"
                        class="bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600"
                    />
                    @if($selectedOrder && !in_array($selectedOrder->status, ['completed', 'canceled']))
                        <x-button primary label="{{ __('common.save') }}" wire:click="save"/>
                        <button
                            type="button"
                            class="px-4 py-2 bg-red-100 text-red-800 hover:bg-red-200 rounded-lg font-medium transition-colors duration-200"
                            wire:click="deleteOrder({{ $selectedOrder->id }})"
                        >
                            Visszamond
                        </button>
                    @endif
                </div>
                @if($selectedOrder && $selectedOrder->status !== 'canceled')
                    <div class="flex gap-2">
                        <x-button
                            secondary
                            label="PDF (HU)"
                            icon="document-text"
                            wire:click="generatePDF({{ $selectedOrder->id }}, 'hu')"
                        />
                        <x-button
                            secondary
                            label="PDF (RO)"
                            icon="document-text"
                            wire:click="generatePDF({{ $selectedOrder->id }}, 'ro')"
                        />
                    </div>
                @endif
            </div>
        </x-slot>
    </x-modal-card>
</div>
