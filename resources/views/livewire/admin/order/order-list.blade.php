@use(App\Enums\OrderStatuses)
@use(App\Helpers\GeneralHelper)
<div>
    <div class="mb-8 flex justify-between">
        <h2 class="text-gray-800 dark:text-gray-200">{{ __('common.orders') }}</h2>
    </div>

    <div class="space-y-4" x-data="{ showFilters: window.innerWidth >= 768 }" x-init="window.addEventListener('resize', () => { showFilters = window.innerWidth >= 768 })">
        <div class="flex flex-col gap-4">
            <x-input
                label="{{ __('common.search_placeholder') }}"
                placeholder="{{ __('common.search_placeholder') }}"
                wire:model.live.debounce.500ms="search"
                class="w-full md:w-1/4"
            />
            <!-- Szűrők lenyitó gomb csak mobilon -->
            <button type="button" class="md:hidden px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-200 text-sm" @click="showFilters = !showFilters">
                <span x-show="!showFilters">Szűrők mutatása</span>
                <span x-show="showFilters">Szűrők elrejtése</span>
            </button>
        </div>
        <!-- Szűrők: csak ha showFilters -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:gap-4" x-show="showFilters" x-transition>
            <x-select
                label="{{ __('common.status') }}"
                placeholder="{{ __('common.status') }}"
                :options="$statuses"
                option-value="value"
                option-label="label"
                wire:model.live="statusFilter"
                class="w-full md:flex-1"
            />
            <x-select
                label="{{ __('common.store') }}"
                placeholder="{{ __('common.store') }}"
                :options="$stores"
                option-value="id"
                option-label="name"
                wire:model.live="storeFilter"
                class="w-full md:flex-1"
            />

            <x-select
                label="{{ __('common.user') }}"
                placeholder="{{ __('common.user') }}"
                wire:model.live="userFilter"
                class="w-full md:flex-1"
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
        </div>
        <div class="flex gap-2 mb-2">
            <x-button secondary label="PDF (HU)" icon="document-text" wire:click="generatePDF(null, 'hu')" />
            <x-button secondary label="PDF (RO)" icon="document-text" wire:click="generatePDF(null, 'ro')" />
            <x-button secondary label="Termékek Összesítés (HU)" icon="document-text" wire:click="generateProductsSummaryPDF('hu')" />
            <x-button secondary label="Termékek Összesítés (RO)" icon="document-text" wire:click="generateProductsSummaryPDF('ro')" />
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
                                $quantity = $detail->dispatched_quantity > 0 ? $detail->dispatched_quantity : $detail->quantity;
                                return $quantity * ($detail->product->price ?? 0);
                            })
                        }} lej
                    </x-table.td>
                    <x-table.td>{{ $order->comment }}</x-table.td>
                    <x-table.td>
                        @if(OrderStatuses::tryFrom($order->status))
                            <span class="px-2 py-1 rounded text-sm font-medium {{ GeneralHelper::getStatusColors()[$order->status] ?? 'bg-gray-100 dark:bg-gray-100 text-gray-800 dark:text-gray-800' }} inline-flex items-center whitespace-nowrap">
                                {{OrderStatuses::tryFrom($order->status)->label()}}
                                @if($order->status === OrderStatuses::RETURNED->value)
                                    @if($order->confirmed_return)
                                        <span class="ml-1 text-green-600">✓</span>
                                    @else
                                        <span class="ml-1 text-red-600">✗</span>
                                    @endif
                                @endif
                            </span>
                        @else
                            {{$order->status}}
                        @endif

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
    <div class="mt-4 flex justify-center">
        {{ $orders->links() }}
    </div>

    {{-- Modal --}}
    <x-modal-card blur="md" wire:model="orderModal">
        @if($selectedOrder)
            <div class="space-y-4">
                <p><strong>{{ __('common.order') }} #{{ $selectedOrder->id }}</strong></p>
                <p><strong>{{ __('common.store') }}:</strong> {{ $selectedOrder->store->name ?? 'Törölt bolt' }}</p>
                <p><strong>{{ __('common.status') }}:</strong>
                    <span class="px-2 py-1 rounded text-sm font-medium {{ GeneralHelper::getStatusColors()[$selectedOrder->status] ?? 'bg-gray-100 dark:bg-gray-100 text-gray-800 dark:text-gray-800' }} whitespace-nowrap">
                        {{ __('common.status_' . $selectedOrder->status) }}
                    </span>
                </p>

                <p><strong>{{ __('common.total') }}:</strong> {{ $this->total }} lej</p>
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
                                    wire:model.live="orderDetails.{{ $index }}.dispatched_quantity"
                                    :disabled="$selectedOrder && ($selectedOrder->status === OrderStatuses::COMPLETED->value || $selectedOrder->status === OrderStatuses::CANCELED->value || $selectedOrder->status === OrderStatuses::RETURNED->value)"
                                />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($selectedOrder && !in_array($selectedOrder->status, [OrderStatuses::COMPLETED->value, OrderStatuses::CANCELED->value, OrderStatuses::RETURNED->value]))
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
            <div class="flex flex-col sm:flex-row flex-wrap gap-2 items-stretch w-full">
                <div class="flex gap-2 flex-1">
                    <x-button flat label="{{ __('common.cancel') }}" wire:click="$set('orderModal', false)"
                        class="bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600"
                    />
                    @if($selectedOrder && !in_array($selectedOrder->status, [OrderStatuses::COMPLETED->value, OrderStatuses::CANCELED->value, OrderStatuses::RETURNED->value]))
                        <x-button primary label="{{ __('common.save') }}" wire:click="save"/>
                        <button
                            type="button"
                            class="px-4 py-2 bg-red-100 text-red-800 hover:bg-red-200 rounded-lg font-medium transition-colors duration-200"
                            wire:click="deleteOrder({{ $selectedOrder->id }})"
                        >
                            Visszamond
                        </button>
                    @endif
                    @if($selectedOrder && $selectedOrder->status === OrderStatuses::RETURNED->value && !$selectedOrder->confirmed_return)
                        <x-button 
                            negative
                            label="{{ __('common.error') }}" 
                            wire:click="markAsPending({{ $selectedOrder->id }})"
                            class="whitespace-nowrap"
                        />
                        <x-button 
                            primary 
                            label="{{ __('common.confirm') }}" 
                            wire:click="confirmReturn({{ $selectedOrder->id }})"
                        />
                    @endif
                </div>
                @if($selectedOrder && $selectedOrder->status !== 'canceled')
                    <div class="flex gap-2 flex-1 sm:justify-end">
                        <x-button
                            secondary
                            label="PDF (HU)"
                            icon="document-text"
                            wire:click="generateOrderProductsSummaryPDF({{ $selectedOrder->id }}, 'hu')"
                        />
                        <x-button
                            secondary
                            label="PDF (RO)"
                            icon="document-text"
                            wire:click="generateOrderProductsSummaryPDF({{ $selectedOrder->id }}, 'ro')"
                        />
                    </div>
                @endif
            </div>
        </x-slot>
    </x-modal-card>

    {{-- Megerősítő popup a hibás visszaküldéshez --}}
    <x-modal-card blur="md" wire:model="confirmationModal">
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Hibás visszaküldés megerősítése
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Biztosan hibásnak jelöli ezt a visszaküldést?
                    </p>
                </div>
            </div>

            @if($orderToConfirm)
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    <strong>Rendelés #{{ $orderToConfirm->id }}</strong><br>
                    <span class="text-gray-500">Üzlet: {{ $orderToConfirm->store->name ?? 'N/A' }}</span><br>
                    <span class="text-gray-500">Rendelő: {{ $orderToConfirm->user->name ?? 'N/A' }}</span>
                </p>
            </div>
            @endif

            <p class="text-sm text-gray-600 dark:text-gray-400">
                Ez a művelet visszaállítja a rendelést "Függőben" státuszra, és újra módosíthatóvá teszi.
            </p>
        </div>

        <x-slot name="footer">
            <div class="flex gap-2 justify-end">
                <x-button
                    flat
                    label="Mégse"
                    wire:click="cancelMarkAsPending"
                    class="bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600"
                />
                <x-button
                    negative
                    label="Megerősítem, hibás visszaküldés"
                    wire:click="confirmMarkAsPending"
                />
            </div>
        </x-slot>
    </x-modal-card>
</div>
