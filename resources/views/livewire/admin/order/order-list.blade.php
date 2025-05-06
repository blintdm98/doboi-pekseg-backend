<div>
    <div class="mb-8 flex justify-between">
        <h2 class="text-gray-800 dark:text-gray-200">{{ __('common.orders') }}</h2>
    </div>

    <x-table>
        <x-slot:head>
            <x-table.th>#</x-table.th>
            <x-table.th>{{ __('common.user') }}</x-table.th>
            <x-table.th>{{ __('common.store') }}</x-table.th>
            <x-table.th>{{ __('common.status') }}</x-table.th>
            <x-table.th>{{ __('common.actions') }}</x-table.th>
        </x-slot:head>

        @foreach($orders as $order)
            <x-table.tr>
                <x-table.td>{{ $order->id }}</x-table.td>
                <x-table.td>{{ $order->user->name ?? 'N/A' }}</x-table.td>
                <x-table.td>{{ $order->store->name ?? 'Törölt bolt' }}</x-table.td>
                <x-table.td>
                @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'partial' => 'bg-orange-100 text-orange-800',
                        ];
                    @endphp

                    <span class="px-2 py-1 rounded text-sm font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </x-table.td>
                <x-table.td>
                    <x-button info label="{{ __('common.details') }}" wire:click="showOrder({{ $order->id }})"/>
                </x-table.td>
            </x-table.tr>
        @endforeach
    </x-table>

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
                        ];
                    @endphp

                    <span class="px-2 py-1 rounded text-sm font-medium {{ $statusColors[$selectedOrder->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($selectedOrder->status) }}
                    </span>
                </p>
                <div class="space-y-2">
                    @foreach($orderDetails as $index => $detail)
                        <div class="flex justify-between items-center gap-4">
                            <span>{{ $detail['product_name'] }} – {{ $detail['quantity'] }} db</span>
                            <div class="w-28">
                                <x-input
                                    type="number"
                                    wire:model.defer="orderDetails.{{ $index }}.dispatched_quantity"
                                    label="{{ __('common.dispatched') }}"
                                />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <x-slot name="footer">
            <x-button flat label="{{ __('common.cancel') }}" wire:click="$set('orderModal', false)"/>
            <x-button primary label="{{ __('common.save') }}" wire:click="save"/>
            @if($selectedOrder)
                <x-button
                    negative
                    label="{{ __('common.delete') }}"
                    wire:click="deleteOrder({{ $selectedOrder->id }})"
                    class="mt-2"
                />
            @endif
        </x-slot>
    </x-modal-card>
</div>
