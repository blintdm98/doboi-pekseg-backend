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
                        <x-button info label="{{ __('common.edit') }}" wire:click="editUser({{ $user->id }})" />
                    </x-table.td>
                </x-table.tr>
            @endforeach
        </x-table>
    </div>
    {{ $users->links() }}
</div>
