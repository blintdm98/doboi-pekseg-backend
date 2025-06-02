<div>
    <div class="mb-8 flex justify-between">
        <x-button secondary icon="plus" label="{{ __('common.add-new') }}" wire:click="openModal" />
        <x-modal-card blur="md" wire:model="userModal">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input label="{{ __('common.name') }}" placeholder="{{ __('common.name') }}" wire:model="form.name" />
                <x-input label="{{ __('common.email') }}" placeholder="{{ __('common.email') }}" wire:model="form.email" />
                <x-input type="password" label="{{ __('common.password') }}" placeholder="{{ __('common.password') }}" wire:model="form.password" />
            </div>
            <x-slot name="footer">
                    <div class="flex justify-between gap-x-4 w-full">
                        <div class="flex gap-x-2">
                            <x-button flat label="{{ __('common.cancel') }}" x-on:click="close"/>
                            @if($form->user)
                                <x-button danger label="{{__('common.delete')}}" wire:click="delete"/>
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
                <x-table.th>{{ __('common.edit') }}</x-table.th>
            </x-slot:head>
            @foreach ($users as $user)
                <x-table.tr>
                    <x-table.td>{{ $user->name }}</x-table.td>
                    <x-table.td>{{ $user->email }}</x-table.td>
                    <x-table.td>
                        <x-button info label="{{ __('common.edit') }}" wire:click="editUser({{ $user->id }})" />
                    </x-table.td>
                </x-table.tr>
            @endforeach
        </x-table>
    </div>
    {{ $users->links() }}
</div>
