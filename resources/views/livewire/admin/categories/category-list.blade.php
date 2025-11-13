<div>
    <div class="mb-8 flex justify-between">
        <h2 class="text-gray-800 dark:text-gray-200">{{ __('common.categories') }}</h2>
    </div>
    <div class="mb-8 flex justify-between">
        <div>
            <x-button secondary icon="plus" label="{{__('common.add-new')}}"
                      wire:click="openModal"
            />
            <x-modal-card blur="md" wire:model="categoryModal">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        label="{{__('common.category')}}"
                        placeholder="{{__('common.category')}}"
                        wire:model="form.name"
                    />
                </div>
                <x-slot name="footer">
                    <div class="flex justify-between gap-x-4 w-full">
                        <div class="flex gap-x-2">
                            <x-button flat label="{{ __('common.cancel') }}" x-on:click="close"/>
                            @if($form->category)
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
            'x-init' => 'window.livewireInitSortable($el, function (order) { $wire.updateCategoryOrder(order); });'
        ]">
            <x-slot:head>
                <x-table.th class="w-12"></x-table.th>
                <x-table.th>{{__('common.category')}}</x-table.th>
                <x-table.th>{{__('common.edit')}}</x-table.th>
            </x-slot:head>
            @foreach($categories as $category)
                <x-table.tr wire:key="category-{{ $category->id }}" data-sortable-item data-item-id="{{ $category->id }}">
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
                    <x-table.td>{{$category->name}}</x-table.td>
                    <x-table.td>
                        <x-button info label="{{__('common.edit')}}" wire:click="editCategory({{$category->id}})"/>
                    </x-table.td>
                </x-table.tr>
            @endforeach
        </x-table>
    </div>
</div>
