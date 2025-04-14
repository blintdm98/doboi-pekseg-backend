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
                <div class="flex justify-between gap-x-4">
                    <x-button flat label="{{ __('common.cancel') }}" x-on:click="close" />
                    <x-button primary label="{{ __('common.save') }}" wire:click="save" />
                </div>
            </x-slot>
        </x-modal-card>
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

    {{ $users->links() }}
</div>
