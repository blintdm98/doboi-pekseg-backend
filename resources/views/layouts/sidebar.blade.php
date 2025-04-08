<div
    class="flex h-screen bg-gray-50 dark:bg-gray-900"
    :class="{ 'overflow-hidden': isSideMenuOpen}"
>
    <!-- Desktop sidebar -->
    <aside
        class="z-20 hidden w-64 overflow-y-auto bg-white dark:bg-gray-800 lg:flex flex-col justify-between shrink-0"
    >
        <div class="py-4 text-gray-500 dark:text-gray-400">
            <a
                class="text-lg font-bold text-gray-800 dark:text-gray-200"
                href="/"
            >
                <div class="ml-6">
                    <div>
                        <span>{{config('app.name')}}</span>
                    </div>
                </div>
            </a>
            <ul class="mt-6">
                @foreach($navbarItems as $item)
                    @include('layouts.menu-item',$item)
                @endforeach
            </ul>
        </div>
    </aside>
    @include('layouts.sidebar-mobile')
</div>
