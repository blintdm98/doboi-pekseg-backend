<div>
    <div class="mb-8 flex justify-between">
        <h2 class="text-gray-800 dark:text-gray-200">Rendelések</h2>
    </div>

    <x-table>
        <x-slot:head>
            <x-table.th>#</x-table.th>
            <x-table.th>Üzlet</x-table.th>
            <x-table.th>Státusz</x-table.th>
            <x-table.th>Műveletek</x-table.th>
        </x-slot:head>

        @foreach($orders as $order)
            <x-table.tr>
                <x-table.td>{{ $order->id }}</x-table.td>
                <x-table.td>{{ $order->store->name }}</x-table.td>
                <x-table.td>{{ $order->status }}</x-table.td>
                <x-table.td>
                    <x-button info label="Részletek" wire:click="showOrder({{ $order->id }})"/>
                </x-table.td>
            </x-table.tr>
        @endforeach
    </x-table>

    {{ $orders->links() }}

    {{-- Modal --}}
    <x-modal-card blur="md" wire:model="orderModal">
        @if($selectedOrder)
            <div class="space-y-4">
                <p><strong>Rendelés #{{ $selectedOrder->id }}</strong></p>
                <p><strong>Üzlet:</strong> {{ $selectedOrder->store->name }}</p>
                <p><strong>Státusz:</strong> {{ $selectedOrder->status }}</p>

                <div class="space-y-2">
                    @foreach($orderDetails as $index => $detail)
                        <div class="flex justify-between items-center gap-4">
                            <span>{{ $detail['product_name'] }} – {{ $detail['quantity'] }} db</span>
                            <x-input
                                type="number"
                                class="w-24"
                                wire:model.defer="orderDetails.{{ $index }}.dispatched_quantity"
                                label="Kiküldött"
                            />
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <x-slot name="footer">
            <x-button flat label="Mégsem" wire:click="$set('orderModal', false)"/>
            <x-button primary label="Mentés" wire:click="save"/>
            @if($selectedOrder)
                <x-button
                    negative
                    label="Törlés"
                    wire:click="deleteOrder({{ $selectedOrder->id }})"
                    class="mt-2"
                />
            @endif
        </x-slot>
    </x-modal-card>
</div>
