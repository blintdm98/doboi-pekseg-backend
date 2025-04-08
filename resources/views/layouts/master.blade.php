@extends('layouts.app')

@section('styles')
    @stack('page-styles')
@endsection

@section('page-content')
    <div
        class="flex h-screen bg-gray-200 dark:bg-gray-900"
        :class="{ 'overflow-hidden': isSideMenuOpen}"
    >
        @include('layouts.sidebar')
        <div class="flex flex-col flex-1">
            @include('layouts.header')
            <main class="h-full pb-16 overflow-y-auto overflow-hidden">
                <div class="px-5 mx-auto grid overflow-x-auto">
                    <div class="min-h-100 overflow-x-auto mt-6 pb-2 px-1">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection

@section('scripts')
    @stack('page-scripts')
@endsection
