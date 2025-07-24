@php use Illuminate\Support\Str; @endphp

<div>
    <div class="mb-8 flex justify-between">
        <h2 class="text-gray-800 dark:text-gray-200">{{ __('common.stores') }}</h2>
    </div>
    <div class="mb-8 flex justify-between">
        <div>
            <x-button secondary icon="plus" label="{{__('common.add-new')}}"
                      wire:click="openModal"
            />
            <x-modal-card blur="md" wire:model="storeModal">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        label="{{__('common.store')}}"
                        placeholder="{{__('common.store')}}"
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
                        label="{{ __('common.contact_person') }}"
                        placeholder="{{ __('common.contact_person') }}"
                        wire:model="form.contact_person"
                    />
                    <div class="w-full relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.logo') }}</label>

                        <input
                            type="file"
                            id="logo-upload"
                            wire:model="form.logo"
                            class="w-full border rounded px-4 py-2 pr-20 hidden"
                        >

                        <label for="logo-upload" class="flex items-center gap-2 px-4 py-2 border rounded cursor-pointer w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828L18 9.828M17 5a3 3 0 00-4.243 0l-7.07 7.07a5 5 0 007.07 7.07L19 13" />
                            </svg>
                            <span class="text-sm text-gray-600 truncate w-full">
                                {{ Str::limit(optional($form->logo)?->getClientOriginalName(), 30) ?? __('common.noimg') }}
                            </span>
                        </label>

                        @if ($form->logo)
                            <div class="absolute top-[1.7rem] right-2 w-8 h-8 z-10">
                                <div class="relative w-full h-full">
                                    <img
                                        src="{{ $form->logo->temporaryUrl() }}"
                                        class="w-full h-full object-cover rounded border border-white shadow"
                                    />
                                    <button
                                        type="button"
                                        wire:click="$set('form.logo', null)"
                                        class="absolute -top-2 -right-2 bg-white text-gray-600 rounded-full w-5 h-5 text-xs flex items-center justify-center hover:bg-red-500 hover:text-white"
                                    >
                                        &times;
                                    </button>
                                </div>
                            </div>
                        @elseif ($form->store && $form->store->getFirstMediaUrl('logos') && !$form->pendingLogoDelete)
                            <div class="absolute top-[1.7rem] right-2 w-8 h-8 z-10">
                                <div class="relative w-full h-full">
                                    <img
                                        src="{{ $form->store->getFirstMediaUrl('logos') }}"
                                        class="w-full h-full object-cover rounded border border-white shadow"
                                    />
                                    <button
                                        type="button"
                                        wire:click="$set('form.pendingLogoDelete', true)"
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
                <x-table.th>{{__('common.store')}}</x-table.th>
                <x-table.th>{{__('common.address')}}</x-table.th>
                <x-table.th>{{ __('common.phone') }}</x-table.th>
                <x-table.th>{{ __('common.contact_person') }}</x-table.th>
                <x-table.th>{{__('common.logo')}}</x-table.th>
                <x-table.th>{{__('common.edit')}}</x-table.th>
            </x-slot:head>
            @foreach($stores as $store)
                <x-table.tr>
                    <x-table.td>{{$store->name}}</x-table.td>
                    <x-table.td>{{$store->address}}</x-table.td>
                    <x-table.td>{{$store->phone}}</x-table.td>
                    <x-table.td>{{ $store->contact_person }}</x-table.td>
                    <x-table.td class="flex justify-center items-center">
                    @if($store->getFirstMediaUrl('logos'))
                        <img src="{{ $store->getFirstMediaUrl('logos') }}" class="h-8 w-8 md:h-12 md:w-12 object-cover rounded mx-auto" />
                    @else
                        <span class="text-sm text-gray-400"></span>
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
