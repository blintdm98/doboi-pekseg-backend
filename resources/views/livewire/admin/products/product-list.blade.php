<div>
    <div class="mb-8 flex justify-between">
        <div>
            <x-button secondary icon="plus" label="{{__('common.add-new')}}"
                      wire:click="openModal"
            />
            <x-modal-card blur="md" wire:model="productModal">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        label="{{__('common.name')}}"
                        placeholder="{{__('common.name')}}"
                        wire:model="form.name"
                    />
                    <x-input
                        type="number"
                        label="{{__('common.price')}}"
                        placeholder="{{__('common.price')}}"
                        wire:model="form.price"
                    />

                </div>
                <x-slot name="footer">
                    <div class="flex justify-between gap-x-4 w-full">
                        <div class="flex gap-x-2">
                            <x-button flat label="{{ __('common.cancel') }}" x-on:click="close"/>
                            @if($form->product)
                                <x-button danger label="{{__('common.delete')}}" wire:click="delete"/>
                            @endif
                        </div>
                        <x-button primary label="{{ __('common.save') }}" wire:click="save"/>
                    </div>
                </x-slot>
            </x-modal-card>
        </div>
    </div>
    <x-table>
        <x-slot:head>
            <x-table.th>{{__('common.name')}}</x-table.th>
            <x-table.th>{{__('common.price')}}</x-table.th>
            <x-table.th>{{__('common.edit')}}</x-table.th>
        </x-slot:head>
        @foreach($products as $product)
            <x-table.tr>
                <x-table.td>{{$product->name}}</x-table.td>
                <x-table.td>{{$product->price}}</x-table.td>
                <x-table.td>
                    <x-button info label="{{__('common.edit')}}" wire:click="editProduct({{$product->id}})"/>
                </x-table.td>
            </x-table.tr>
        @endforeach
    </x-table>
    {{$products->links()}}
</div>
