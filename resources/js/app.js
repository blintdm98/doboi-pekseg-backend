import './bootstrap';

import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Sortable from 'sortablejs';
window.Alpine = Alpine;

window.livewireInitSortable = (el, callback, options = {}) => {
    if (!Sortable || !el) {
        return;
    }

    const config = Object.assign(
        {
            handle: '.drag-handle',
            animation: 150,
            draggable: '[data-sortable-item]',
            onEnd() {
                if (typeof callback === 'function') {
                    const order = Array.from(el.querySelectorAll('[data-sortable-item]')).map((row, index) => ({
                        value: row.dataset.itemId,
                        order: index + 1,
                    }));

                    callback(order);
                }
            },
        },
        options
    );

    return Sortable.create(el, config);
};

Livewire.start()
