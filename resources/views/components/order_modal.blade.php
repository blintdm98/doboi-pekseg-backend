<div
    x-show="open"
    @click.self="close"
    x-transition
    x-cloak
    class="fixed inset-0 z-50 flex items-start justify-center bg-black/20"
>
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-xl mt-[4rem]">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Rendelés #{{ $order->id }}</h2>
            <button @click="close" class="text-gray-500 text-xl">&times;</button>
        </div>

        <div class="text-gray-700">
            <p><strong>Üzlet:</strong> {{ $order->store->name }}</p>
            <p><strong>Státusz:</strong> {{ $order->status }}</p>

            <template x-if="loading">
                <p class="mt-4 text-gray-500">Betöltés...</p>
            </template>

            <ul class="mt-4 space-y-2" x-show="!loading">
                <template x-for="item in details" :key="item.id">
                    <li x-text="`${item.product.name} – ${item.quantity} db`"></li>
                </template>
            </ul>
        </div>

        <div class="mt-6 text-right">
            <button @click="close" class="bg-gray-200 px-4 py-2 rounded">Bezárás</button>
        </div>
    </div>
</div>


<script>
function orderModal(orderId) {
    return {
        open: false,
        loading: false,
        details: [],

        async openModal() {
            this.open = true;
            this.loading = true;
            try {
                const res = await fetch(`/api/orders/${orderId}`);
                const data = await res.json();
                this.details = data.order_details;
            } catch (e) {
                console.error('Hiba a lekérés közben:', e);
            } finally {
                this.loading = false;
            }
        },

        close() {
            this.open = false;
        }
    }
}
</script>