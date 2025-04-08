<div>
    <form wire:submit="save">
        @csrf
        <div class="flex pt-20 min-h-screen p-6 bg-gray-50 dark:bg-gray-900">
            <div
                class="flex-1 h-full max-w-4xl mx-auto overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800"
            >
                <div class="flex flex-col overflow-y-auto ">
                    <div class="flex  items-center justify-center p-6 sm:p-12 md:w-1/2 mx-auto ">
                        <div class="w-full">
                            <h1 class="mb-4 text-xl font-semibold text-gray-700 dark:text-gray-200">
                                {{__('common.login')}}
                            </h1>
                            <x-errors only="common"/>
                            <x-input name="user_name" wire:model="user_name" label="{{__('common.user_name')}}"
                                     placeholder="{{__('common.user_name')}}" class="mb-4"/>
                            <x-password label="{{__('common.password')}}" wire:model="password"
                                        placeholder="*******"/>
                            <div class="pt-2">
                                <x-button type="submit" info label="{{__('common.login')}}" class="mt-4 w-full"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
