<div 
    x-show="open" 
    @click.self="open = false"
    x-transition 
    class="fixed inset-0 z-50 flex items-start justify-center bg-black/75"
    >
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-xl mt-[4rem]">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Rendelés #{{ $order->id }}</h2>
            <button @click="open = false" class="text-gray-500 text-xl">&times;</button>
        </div>

        <div class="text-gray-700">
            <p><strong>Üzlet:</strong> {{ $order->store->name }}</p>
            <p><strong>Státusz:</strong> {{ $order->status }}</p>

            <ul class="mt-4 space-y-2">
                @foreach ($order->orderDetails as $detail)
                    <li>{{ $detail->product->name }} - {{ $detail->quantity }} db</li>
                @endforeach
            </ul>
        </div>

        <div class="mt-6 text-right">
            <button @click="open = false" class="bg-gray-200 px-4 py-2 rounded">Bezárás</button>
        </div>
    </div>
    </div>
</div>