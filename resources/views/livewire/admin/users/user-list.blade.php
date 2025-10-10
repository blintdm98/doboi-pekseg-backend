<div>
    <div class="mb-8 flex justify-between">
        <h2 class="text-gray-800 dark:text-gray-200">{{ __('common.users') }}</h2>
    </div>
    <div class="mb-8 flex justify-between">
        <x-button secondary icon="plus" label="{{ __('common.add-new') }}" wire:click="openModal" />
        <x-modal-card blur="md" wire:model="userModal">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input label="Név" placeholder="Név" wire:model="form.name" />
                <x-input 
                    label="{{ __('common.user_name') }}" 
                    placeholder="{{ __('common.user_name') }}" 
                    wire:model="form.user_name" 
                />    
                <x-input 
                    label="{{ __('common.email') }} ({{ __('common.optional') }})" 
                    placeholder="{{ __('common.email') }}" 
                    wire:model="form.email" 
                />
                <x-password 
                    label="{{ __('common.password') }}" 
                    placeholder="{{ __('common.password') }}" 
                    wire:model="form.password" 
                    :reveal="false"
                />
                <x-input
                    label="{{ __('common.phone') }}"
                    placeholder="{{ __('common.phone') }}"
                    wire:model="form.phone"
                />
                <x-select
                    label="{{ __('common.role') }}"
                    wire:model="form.role"
                    placeholder="Válassz szerepet!"
                >
                    @foreach($roles as $value => $label)
                        <x-select.option :value="$value">{{ $label }}</x-select.option>
                    @endforeach
                </x-select>
                <div class="flex items-center mt-2">
                    <x-checkbox id="can_add_store" wire:model="form.can_add_store" />
                    <label for="can_add_store" class="ml-2">Üzlet hozzáadás joga</label>
                </div>
            </div>
            <x-slot name="footer">
                    <div class="flex justify-between gap-x-4 w-full">
                        <div class="flex gap-x-2">
                            <x-button flat label="{{ __('common.cancel') }}" x-on:click="close"/>
                            @if($form->user)
                                <x-button negative label="{{__('common.delete')}}" wire:click="delete"/>
                            @endif
                        </div>
                        <x-button primary label="{{ __('common.save') }}" wire:click="save"/>
                    </div>
                </x-slot>
        </x-modal-card>
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
                <x-table.th>{{ __('common.name') }}</x-table.th>
                <x-table.th>{{ __('common.email') }}</x-table.th>
                <x-table.th>{{ __('common.phone') }}</x-table.th>
                <x-table.th>{{ __('common.role') }}</x-table.th>
                <x-table.th>{{ __('common.edit') }}</x-table.th>
            </x-slot:head>
            @foreach ($users as $user)
                <x-table.tr>
                    <x-table.td>{{ $user->name }}</x-table.td>
                    <x-table.td>{{ $user->email }}</x-table.td>
                    <x-table.td>{{ $user->phone }}</x-table.td>
                    <x-table.td>{{ ucfirst($user->role) }}</x-table.td>
                    <x-table.td>
                        <div class="flex gap-2 justify-center items-center">
                            <x-button info label="{{ __('common.edit') }}" wire:click="editUser({{ $user->id }})" />
                            @if($user->role === 'mobil')
                                <x-button secondary label="Üzletek" wire:click="openStoresModal({{ $user->id }})" />
                            @endif
                        </div>
                    </x-table.td>
                </x-table.tr>
            @endforeach
        </x-table>
    </div>
    {{ $users->links() }}

    <x-modal-card width="4xl" blur="md" title="Üzletek hozzárendelése" wire:model="showStoresModal">
        <div class="space-y-4">
            @if(!empty($storeSelection))
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr>
                                <th class="text-left py-2 px-[6px]">Üzlet neve</th>
                                <th class="text-left py-2 px-[6px]">Cím</th>
                                <th class="text-left py-2 px-[6px]">Kiválaszt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($storeSelection as $idx => $store)
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td class="py-2 px-[6px]">{{ $store['name'] }}</td>
                                    <td class="py-2 px-[6px]">
                                        <div class="text-gray-600 dark:text-gray-400">
                                            @if($store['address'])
                                                <div>{{ $store['address'] }}</div>
                                            @endif
                                            @if($store['phone'])
                                                <div class="text-xs">{{ $store['phone'] }}</div>
                                            @endif
                                            @if($store['contact_person'])
                                                <div class="text-xs">Kapcsolat: {{ $store['contact_person'] }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-2 px-[6px]">
                                        <x-checkbox wire:model.live="storeSelection.{{ $idx }}.checked" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <tbody>
                            <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                <td colspan="2" class="py-3 px-[6px] font-semibold">
                                    Összes kiválasztása
                                </td>
                                <td class="py-3 px-[6px]">
                                    <x-checkbox wire:model.live="selectAllStores" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-sm text-gray-500 dark:text-gray-400">Nincsenek elérhető üzletek.</div>
            @endif
        </div>
        <x-slot name="footer">
            <div class="flex justify-between gap-x-4">
                <x-button flat label="{{__('common.cancel')}}" x-on:click="close"/>
                <x-button primary label="{{__('common.save')}}" wire:click="saveStores"/>
            </div>
        </x-slot>
    </x-modal-card>
</div>
