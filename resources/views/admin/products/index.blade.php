@extends('layouts.master')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Termékek</h1>
    <div x-data="productTable()" x-init="loadProducts()">
        <button 
            @click="openModal"
            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700 transition mb-4"
        >
            + Új termék hozzáadása
        </button>
    
        @include('components.product_modal')

        <table class="min-w-full bg-white rounded-lg shadow">
            <thead>
                <tr class="bg-gray-100 text-left text-sm text-gray-700 uppercase tracking-wider">
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Név</th>
                    <th class="px-4 py-2">Ár (lej)</th>
                    <th class="px-4 py-2">Műveletek</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="product in products" :key="product.id">
                    <tr class="border-t">
                        <td class="px-4 py-2" x-text="product.id"></td>
                        <td class="px-4 py-2" x-text="product.name"></td>
                        <td class="px-4 py-2" x-text="Number(product.price).toFixed(2)"></td>
                        <td class="px-6 py-4">
                            <button
                                @click="openModal(product)" 
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
                            >
                                Szerkeszt
                            </button>
                            <button 
                                @click="deleteProduct(product.id)"
                                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition"
                            >
                                Törlés
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
@endsection

<!-- <script>


async submitProduct() {
            try {
                // Ne maradjon benne véletlenül id új létrehozáskor
                if (!this.isEdit && this.newProduct.id) {
                    delete this.newProduct.id;
                }

                let res;

                if (this.isEdit && this.editedProductId) {
                    res = await fetch(`/api/products/${this.editedProductId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.newProduct)
                    });
                } else {
                    res = await fetch('/api/products', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.newProduct)
                    });
                }

                if (!res.ok) throw new Error('Hiba történt a mentés során');

                const data = await res.json();

                if (this.isEdit) {
                    this.products = this.products.map(p => p.id === data.id ? data : p);
                } else {
                    this.products = [...this.products, data]; 
                }

                this.closeModal();

            } catch (e) {
                alert('Hiba a termék mentése közben');
                console.error(e);
            }
        },
</script> -->