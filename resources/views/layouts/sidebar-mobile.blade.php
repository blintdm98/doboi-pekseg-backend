<!-- Mobile sidebar -->
<!-- Backdrop -->
<div
    x-show="isSideMenuOpen"
    x-transition:enter="transition ease-in-out duration-150"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in-out duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-10 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
></div>
<aside
    class="fixed inset-y-0 z-20 shrink-0 w-64 mt-16 overflow-y-auto bg-white dark:bg-gray-800 lg:hidden flex flex-col"
    x-show="isSideMenuOpen"
    x-transition:enter="transition ease-in-out duration-150"
    x-transition:enter-start="opacity-0 transform -translate-x-20"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in-out duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0 transform -translate-x-20"
    @click.away="closeSideMenu"
    @keydown.escape="closeSideMenu"
>
    <div class="py-4 text-gray-500 dark:text-gray-400 flex flex-col h-full">
        <div class="flex-1">
            <a
                class="ml-6 text-lg font-bold text-gray-800 dark:text-gray-200 mb-6"
                href="#"
            >
                {{config('app.name')}}
            </a>
            <div class="mt-6">
                <ul>
                    @foreach($navbarItems as $item)
                        @include('layouts.menu-item',$item)
                    @endforeach
                </ul>
            </div>
        </div>
        <!-- Kijelentkezés gomb a mobile sidebar alján -->
        <div class="mt-auto border-t border-gray-200 dark:border-gray-700">
            <a
                href="{{route('logout')}}"
                class="border-l-[3px] ease-in-out duration-200 flex items-center gap-2 border-transparent px-4 py-3 text-gray-500 hover:border-gray-100 hover:bg-gray-50 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-gray-200"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0110.5 3h6a2.25 2.25 0 012.25 2.25v13.5A2.25 2.25 0 0116.5 21h-6a2.25 2.25 0 01-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                </svg>
                <span class="text-sm font-medium">Kijelentkezés</span>
            </a>
        </div>
    </div>
</aside>
