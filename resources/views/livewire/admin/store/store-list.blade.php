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
                        label="{{__('common.logo')}}"
                        placeholder="{{__('common.logo')}}"
                        wire:model="form.logo"
                    />
                </div>
                <x-slot name="footer">
                    <div class="flex justify-between gap-x-4">
                        <x-button flat label="{{__('common.cancel')}}" x-on:click="close"/>
                        <x-button primary label="{{__('common.save')}}" wire:click="save"/>
                    </div>
                </x-slot>
            </x-modal-card>
        </div>
    </div>
    <x-table>
        <x-slot:head>
            <x-table.th>{{__('common.name')}}</x-table.th>
            <x-table.th>{{__('common.address')}}</x-table.th>
            <x-table.th>{{__('common.logo')}}</x-table.th>
            <x-table.th>{{__('common.edit')}}</x-table.th>
        </x-slot:head>
        @foreach($stores as $store)
            <x-table.tr>
                <x-table.td>{{$store->name}}</x-table.td>
                <x-table.td>{{$store->address}}</x-table.td>
                <x-table.td>{{$store->logo}}</x-table.td>
                <x-table.td>
                    <x-button info label="{{__('common.edit')}}" wire:click="editStore({{$store->id}})"/>
                </x-table.td>
            </x-table.tr>
        @endforeach
    </x-table>
    {{$stores->links()}}
</div>
