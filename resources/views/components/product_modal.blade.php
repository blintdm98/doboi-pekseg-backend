<div 
    x-show="modalOpen" x-transition 
    @click.self="closeModal" 
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/30"
>
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4" x-text="isEdit ? 'Termék szerkesztése' : 'Új termék hozzáadása'"></h2>

        <form @submit.prevent="submitProduct">
            <div class="mb-4">
                <label class="block mb-1">Név</label>
                <input type="text" x-model="newProduct.name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1">Ár (lej)</label>
                <input type="number" step="0.01" x-model="newProduct.price" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="flex justify-end">
                <button type="button" @click="closeModal" class="mr-2 bg-gray-200 px-4 py-2 rounded">Mégsem</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Mentés</button>
            </div>
        </form>
    </div>
</div>

<script>

function productTable() {
    return {
        products: [],
        modalOpen: false,
        isEdit: false,
        editedProductId: null,
        newProduct: {
            name: '',
            price: ''
        },

        loadProducts() {
            fetch('/api/products')
                .then(res => res.json())
                .then(data => this.products = data);
        },

        openModal(product = null) {
            this.modalOpen = true;
            this.isEdit = !!product;
            if (product) {
                this.editedProductId = product.id;
                this.newProduct = { ...product };
            } else {
                this.editedProductId = null;
                this.newProduct = { name: '', price: '' };
            }
        },

        closeModal() {
            this.modalOpen = false;
            this.newProduct = { name: '', price: '' };
        },

        async submitProduct() {
            try {
                let res;
                if (this.isEdit) {
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

                if (!res.ok) throw new Error('Hiba történt');

                const data = await res.json();

                if (this.isEdit) {
                    this.products = this.products.map(p => p.id === data.id ? data : p);
                } else {
                    this.products.push(data);
                }

                this.closeModal();
            } catch (e) {
                alert('Hiba mentés közben');
                console.error(e);
            }
        },

        async deleteProduct(id) {
            if (!confirm('Biztosan törlöd ezt a terméket?')) return;

            try {
                const res = await fetch(`/api/products/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                if (!res.ok) throw new Error('Törlés sikertelen');

                // Frissítjük a listát frontend oldalon
                this.products = this.products.filter(p => p.id !== id);
            } catch (e) {
                alert('Hiba a törlés közben');
                console.error(e);
            }
        }
    }
}
</script>