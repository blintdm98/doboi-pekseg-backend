<div class="flex flex-col flex-1">
    <header class="z-10 py-4 {{ session()->has('t_session')?'bg-red-500':'bg-white dark:bg-gray-800'}}  shadow-md ">
        <div
            class="container flex items-center justify-between h-full px-6 mx-auto text-blue-600 dark:text-blue-300"
        >
            <!-- Mobile hamburger -->
            <button
                class="p-1 -ml-1 mr-5 rounded-md lg:hidden focus:outline-hidden focus:shadow-outline-blue"
                @click="toggleSideMenu"
                aria-label="Menu"
            >
                <svg
                    class="w-6 h-6"
                    aria-hidden="true"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    <path
                        fill-rule="evenodd"
                        d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                        clip-rule="evenodd"
                    ></path>
                </svg>
            </button>
            <div class="flex justify-center flex-1 lg:mr-32">
            </div>
            <ul class="flex items-center shrink-0 space-x-6">
                <li>
                    <button
                        class="align-middle rounded-full focus:shadow-outline-purple focus:outline-hidden"
                        @click="toggleProfileMenu"
                        @keydown.escape="closeProfileMenu"
                        aria-label="Account"
                        aria-haspopup="true"
                    >
                        <div class="flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{auth()->user()->user_name}}
                        </div>
                    </button>
                    <div x-show="isProfileMenuOpen">
                        <ul
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            @click.away="closeProfileMenu"
                            @keydown.escape="closeProfileMenu"
                            class="absolute right-0 w-56 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md dark:border-gray-700 dark:text-gray-300 dark:bg-gray-700"
                            aria-label="submenu"
                        >
                            <li class="flex">
                                {{--                                <a--}}
                                {{--                                    class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200"--}}
                                {{--                                    --}}{{--                                    href="{{route('profile')}}"--}}
                                {{--                                >--}}
                                {{--                                    <svg--}}
                                {{--                                        class="w-4 h-4 mr-3"--}}
                                {{--                                        aria-hidden="true"--}}
                                {{--                                        fill="none"--}}
                                {{--                                        stroke-linecap="round"--}}
                                {{--                                        stroke-linejoin="round"--}}
                                {{--                                        stroke-width="2"--}}
                                {{--                                        viewBox="0 0 24 24"--}}
                                {{--                                        stroke="currentColor"--}}
                                {{--                                    >--}}
                                {{--                                        <path--}}
                                {{--                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"--}}
                                {{--                                        ></path>--}}
                                {{--                                    </svg>--}}
                                {{--                                    <span>{{__('menu.profile')}}</span>--}}
                                {{--                                </a>--}}
                            </li>
                            <li class="flex">
                                <a
                                    class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                                    href="{{route('logout')}}"
                                >
                                    <svg
                                        class="w-4 h-4 mr-3"
                                        aria-hidden="true"
                                        fill="none"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                                        ></path>
                                    </svg>
                                    <span>Log out</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                </li>
                <!-- Theme toggler -->
                <li class="flex">
                    <button
                        class="rounded-md focus:outline-hidden focus:shadow-outline-blue"
                        @click="toggleTheme"
                        aria-label="Toggle color mode"
                    >
                        <template x-if="!dark">
                            <svg
                                class="w-5 h-5"
                                aria-hidden="true"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"
                                ></path>
                            </svg>
                        </template>
                        <template x-if="dark">
                            <svg
                                class="w-5 h-5"
                                aria-hidden="true"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                        </template>
                    </button>
                </li>
                <!-- Notifications menu -->
            </ul>
        </div>
    </header>
</div>
