<div>
    <div class="mb-8 flex justify-between">
        <h2 class="text-gray-800 dark:text-gray-200">{{ __('common.products') }}</h2>
    </div>
    <div class="mb-8 flex justify-between">
        <div>
            <x-button secondary icon="plus" label="{{__('common.add-new')}}"
                      wire:click="openModal"
            />
            <x-modal-card blur="md" wire:model="productModal">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        label="{{__('common.product')}}"
                        placeholder="{{__('common.product')}}"
                        wire:model="form.name"
                    />
                    <x-input
                        type="number"
                        step="0.01"
                        min="0"
                        label="{{__('common.price')}}"
                        placeholder="{{__('common.price')}}"
                        wire:model="form.price"
                    />
                    <x-select
                        label="{{__('common.tva')}}"
                        wire:model="form.tva"
                        placeholder="11%"
                    >
                        <x-select.option value="11">11%</x-select.option>
                        <x-select.option value="21">21%</x-select.option>
                    </x-select>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <x-select
                                label="{{__('common.unit')}}"
                                wire:model.live="form.unit"
                            >
                                <x-select.option value="db">{{__('common.unit_db')}}</x-select.option>
                                <x-select.option value="kg">{{__('common.unit_kg')}}</x-select.option>
                            </x-select>
                        </div>
                        @if($form->unit === 'kg')
                        <div class="flex-1">
                            <x-input
                                type="number"
                                step="0.01"
                                min="0"
                                label="{{__('common.unit_value')}}"
                                placeholder="1.00"
                                wire:model="form.unit_value"
                            />
                        </div>
                        @endif
                    </div>
                    <x-input
                        label="{{ __('common.accounting_code') }}"
                        placeholder="{{ __('common.accounting_code') }} (opcionális)"
                        wire:model="form.accounting_code"
                    />
                    <x-select
                        label="{{__('common.category')}}"
                        placeholder="{{__('common.select_category')}}"
                        :options="$categories"
                        option-value="value"
                        option-label="label"
                        wire:model="form.category_ids"
                        multiselect
                        class="w-full"
                    />
                    <div class="w-full relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.image') }}</label>

                        <input
                            type="file"
                            id="file-upload"
                            wire:model="form.image"
                            class="w-full border rounded px-4 py-2 pr-20 hidden"
                        >

                        <label for="file-upload" class="flex items-center gap-2 px-4 py-2 border rounded cursor-pointer w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="hover:text-gray-100 h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828L18 9.828M17 5a3 3 0 00-4.243 0l-7.07 7.07a5 5 0 007.07 7.07L19 13" />
                            </svg>
                            <span class="text-sm text-gray-600">
                                {{ $form->image ? $form->image->getClientOriginalName() : 'Nincs kiválasztva kép' }}
                            </span>
                        </label>

                        @if ($form->image)
                            <div class="absolute top-[1.7rem] right-2 w-8 h-8 z-10">
                                <div class="relative w-full h-full">
                                    <img
                                        src="{{ $form->image->temporaryUrl() }}"
                                        class="w-full h-full object-cover rounded border border-white shadow"
                                    />
                                    <button
                                        type="button"
                                        wire:click="$set('form.image', null)"
                                        class="absolute -top-2 -right-2 bg-white text-gray-600 rounded-full w-5 h-5 text-xs flex items-center justify-center hover:bg-red-500 hover:text-white"
                                    >
                                        &times;
                                    </button>
                                </div>
                            </div>
                        @elseif ($form->product && $form->product->getFirstMediaUrl('images') && !$form->pendingImageDelete)
                            <div class="absolute top-[1.7rem] right-2 w-8 h-8 z-10">
                                <div class="relative w-full h-full">
                                    <img
                                        src="{{ $form->product->getFirstMediaUrl('images') }}"
                                        class="w-full h-full object-cover rounded border border-white shadow"
                                    />
                                    <button
                                        type="button"
                                        wire:click="$set('form.pendingImageDelete', true)"
                                        class="absolute -top-2 -right-2 bg-white text-gray-600 rounded-full w-5 h-5 text-xs flex items-center justify-center hover:bg-red-500 hover:text-white"
                                    >
                                        &times;
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <x-slot name="footer">
                    <div class="flex justify-between gap-x-4 w-full">
                        <div class="flex gap-x-2">
                            <x-button flat label="{{ __('common.cancel') }}" x-on:click="close"/>
                            @if($form->product)
                                <x-button negative label="{{__('common.delete')}}" wire:click="delete"/>
                            @endif
                        </div>
                        <x-button primary label="{{ __('common.save') }}" wire:click="save"/>
                    </div>
                </x-slot>
            </x-modal-card>
        </div>
    </div>

    <div class="space-y-4">
        <div class="flex items-center gap-4">
            <x-input 
                placeholder="{{__('common.search_placeholder')}}" 
                wire:model.live.debounce.500ms="search"
                class="w-full md:w-1/3"
            />
        </div>
        <x-table :tbody-attributes="[
            'x-data' => '{}',
            'x-init' => 'window.livewireInitSortable($el, function (order) { $wire.updateProductOrder(order); });'
        ]">
            <x-slot:head>
                <x-table.th class="w-12"></x-table.th>
                <x-table.th>{{__('common.product')}}</x-table.th>
                <x-table.th>{{__('common.category')}}</x-table.th>
                <x-table.th>{{__('common.price')}}</x-table.th>
                <x-table.th>{{__('common.tva')}}</x-table.th>
                <x-table.th>{{__('common.unit')}}</x-table.th>
                <x-table.th>{{__('common.image')}}</x-table.th>
                <x-table.th>{{__('common.edit')}}</x-table.th>
            </x-slot:head>
            @foreach($products as $product)
                <x-table.tr wire:key="product-{{ $product->id }}" data-sortable-item data-item-id="{{ $product->id }}">
                    <x-table.td class="w-12">
                        <button
                            type="button"
                            class="drag-handle mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 cursor-grab"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6h.01M9 12h.01M9 18h.01M15 6h.01M15 12h.01M15 18h.01" />
                            </svg>
                        </button>
                    </x-table.td>
                    <x-table.td>
                        {{$product->name}}
                        @if($product->accounting_code)
                            <span class="text-sm text-gray-500 ml-2">({{$product->accounting_code}})</span>
                        @endif
                    </x-table.td>
                    <x-table.td>
                        @if($product->categories->isNotEmpty())
                            {{ $product->categories->pluck('name')->join(', ') }}
                        @else
                            <span></span>
                        @endif
                    </x-table.td>
                    <x-table.td>{{$product->price}}</x-table.td>
                    <x-table.td>{{$product->tva}}%</x-table.td>
                    <x-table.td>
                        {{$product->unit}}
                        @if($product->unit === 'kg' && $product->unit_value)
                            <span class="text-sm text-gray-500">({{$product->unit_value}})</span>
                        @endif
                    </x-table.td>
                    <x-table.td class="flex justify-center items-center">
                    @if($product->getFirstMediaUrl('images'))
                        <img src="{{ $product->getFirstMediaUrl('images') }}" class="h-8 w-8 md:h-12 md:w-12 object-cover rounded mx-auto" />
                    @else
                        <span class="text-sm text-gray-400"></span>
                    @endif
                    </x-table.td>
                    <x-table.td>
                        <x-button info label="{{__('common.edit')}}" wire:click="editProduct({{$product->id}})"/>
                    </x-table.td>
                </x-table.tr>
            @endforeach
        </x-table>
    </div>
</div>
