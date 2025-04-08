<div>
    <x-card title="{{is_null($order)?__('common.new-offer'):__('common.edit-offer', ['name' => $offer->name])}}">
        <div class="grid grid-cols-1 gap-4">
            <form wire:submit.prevent="save">
                <x-card>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
{{--                        <x-select--}}
{{--                            label="{{ __('common.offer-type') }}"--}}
{{--                            placeholder="{{ __('common.offer-type') }}"--}}
{{--                            :options="$categoryTypes"--}}
{{--                            option-label="name"--}}
{{--                            :clearable="false"--}}
{{--                            option-value="id"--}}
{{--                            wire:model.live="form.category_type_id"--}}
{{--                        />--}}

{{--                        <x-select--}}
{{--                            label="{{ __('common.offer_status') }}"--}}
{{--                            placeholder="{{ __('common.offer_status') }}"--}}
{{--                            :options="$orderStatuses"--}}
{{--                            option-label="label"--}}
{{--                            :clearable="false"--}}
{{--                            option-value="value"--}}
{{--                            wire:model.live="form.status"--}}
{{--                        />--}}
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <x-input
                            label="{{__('common.name')}}"
                            placeholder="{{__('common.name')}}"
                            wire:model="form.name"
                        />
                        <x-input
                            label="{{__('common.client')}}"
                            placeholder="{{__('common.client')}}"
                            wire:model="form.client"
                        />
                    </div>
                </x-card>
            </form>
        </div>
    </x-card>
</div>