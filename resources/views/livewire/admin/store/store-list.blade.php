<div>
    <div class="mb-8 flex justify-between">
        <div>
            <x-button secondary icon="plus" label="{{__('common.add-new')}}"
                      wire:click="openModal"
            />
            <x-modal-card blur="md" wire:model="storeModal">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        label="{{__('common.name')}}"
                        placeholder="{{__('common.name')}}"
                        wire:model="form.name"
                    />
                    <x-input
                        label="{{__('common.address')}}"
                        placeholder="{{__('common.address')}}"
                        wire:model="form.address"
                    />
                    <x-input
                        label="{{ __('common.phone') }}"
                        placeholder="{{ __('common.phone') }}"
                        wire:model="form.phone"
                    />
                    <x-input
                        type="file"
                        label="{{ __('common.logo') }}"
                        wire:model="form.logo"
                    />
                </div>
                <x-slot name="footer">
                    <div class="flex justify-between gap-x-4 w-full">
                        <div class="flex gap-x-2">
                            <x-button flat label="{{ __('common.cancel') }}" x-on:click="close"/>
                            @if($form->store)
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
        <x-table>
            <x-slot:head>
                <x-table.th>{{__('common.name')}}</x-table.th>
                <x-table.th>{{__('common.address')}}</x-table.th>
                <x-table.th>{{ __('common.phone') }}</x-table.th>
                <x-table.th>{{__('common.logo')}}</x-table.th>
                <x-table.th>{{__('common.edit')}}</x-table.th>
            </x-slot:head>
            @foreach($stores as $store)
                <x-table.tr>
                    <x-table.td>{{$store->name}}</x-table.td>
                    <x-table.td>{{$store->address}}</x-table.td>
                    <x-table.td>{{$store->phone ?: 'Nincs telefonszám'}}</x-table.td>
                    <x-table.td class="flex justify-center items-center">
                    @if($store->getFirstMediaUrl('logos'))
                        <img src="{{ $store->getFirstMediaUrl('logos') }}" class="h-12 w-12 object-cover rounded" />
                    @else
                        <span class="text-sm text-gray-400">Nincs logó</span>
                    @endif
                    </x-table.td>
                    <x-table.td>
                        <x-button info label="{{__('common.edit')}}" wire:click="editStore({{$store->id}})"/>
                    </x-table.td>
                </x-table.tr>
            @endforeach
        </x-table>
    </div>
    {{$stores->links()}}
</div>
