<div
    x-show="modalOpen" x-transition @click.self="closeModal" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/30"
>
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4" x-text="isEdit ? 'Üzlet szerkesztése' : 'Új üzlet hozzáadása'"></h2>

        <form @submit.prevent="submitStore">
            <div class="mb-4">
                <label class="block mb-1">Név</label>
                <input type="text" x-model="storeData.name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1">Cím</label>
                <input type="text" x-model="storeData.address" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1">Logo URL</label>
                <input type="text" x-model="storeData.logo" class="w-full border rounded px-3 py-2">
            </div>

            <div class="flex justify-end">
                <button type="button" @click="closeModal" class="mr-2 bg-gray-200 px-4 py-2 rounded">Mégsem</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Mentés
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function storeTable() {
    return {
        stores: [],
        modalOpen: false,
        isEdit: false,
        editedStoreId: null,
        storeData: { name: '', address: '', logo: '' },

        loadStores() {
            fetch('/api/stores')
                .then(res => res.json())
                .then(data => this.stores = data);
        },

        openModal(store) {
            if (store instanceof Event) store = null;
            this.modalOpen = true;
            const isRealStore = store && typeof store === 'object' && 'id' in store;

            this.isEdit = isRealStore;

            if (isRealStore) {
                this.editedStoreId = store.id;
                this.storeData = {
                    name: store.name,
                    address: store.address,
                    logo: store.logo ?? ''
                };
            } else {
                this.editedStoreId = null;
                this.storeData = { name: '', address: '', logo: '' };
            }
        },

        closeModal() {
            this.modalOpen = false;
            this.storeData = { name: '', address: '', logo: '' };
            this.isEdit = false;
            this.editedStoreId = null;
        },

        async submitStore() {
            try {
                const url = (this.isEdit && this.editedStoreId)  ? `/api/stores/${this.editedStoreId}` : '/api/stores';
                const method = this.isEdit ? 'PUT' : 'POST';

                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.storeData)
                });

                if (!res.ok) throw new Error('Mentés sikertelen');

                const data = await res.json();

                if (this.isEdit) {
                    this.stores = this.stores.map(s => s.id === data.id ? data : s);
                } else {
                    this.stores.push(data);
                }

                this.closeModal();
            } catch (e) {
                alert('Hiba mentés közben');
                console.error(e);
            }
        },

        async deleteStore(id) {
            if (!confirm('Biztosan törlöd ezt az üzletet?')) return;

            try {
                const res = await fetch(`/api/stores/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });

                if (!res.ok) throw new Error('Törlés sikertelen');

                this.stores = this.stores.filter(s => s.id !== id);
            } catch (e) {
                alert('Hiba törlés közben');
                console.error(e);
            }
        }
    }
}
</script>

