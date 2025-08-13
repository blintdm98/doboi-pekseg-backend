<?php

namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class MenuViewComposer
{
    public function compose(View $view)
    {
        // Explicit módon beállítjuk a magyar nyelvet
        App::setLocale('hu');
        
        $navbarItems = [
            'dashboard' => [
                'name'  => __('menu.dashboard'),
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9 9V9m4 4h4m-4 4h4"/></svg>',
                'url'   => 'dashboard',
                'order' => 20,
            ],
        ];

        $navbarItems['products'] = [
            'name'  => __('menu.products'),
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.25v10.5a.75.75 0 01-.75.75h-15a.75.75 0 01-.75-.75V8.25m16.5 0l-8.25-5.25L3.75 8.25m16.5 0H3.75"/></svg>',
            'url'   => 'products',
            'order' => 500,
        ];

        $navbarItems['stores'] = [
            'name'  => __('menu.stores'),
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5V6.75A2.25 2.25 0 015.25 4.5h13.5A2.25 2.25 0 0121 6.75v3.75M3 10.5h18M3 10.5v7.5A1.5 1.5 0 004.5 19.5h15a1.5 1.5 0 001.5-1.5v-7.5"/></svg>',
            'url'   => 'stores',
            'order' => 400,
        ];

        $navbarItems['orders'] = [
            'name'          => __('menu.orders'),
            'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18v18H3V3zm6 4.5h6M9 9h6m-6 3h3"/></svg>',
            'url'           => 'orders',
            'secondary_url' => [
                'orders.create'
            ],
            'order'         => 200,
        ];

        $navbarItems['users'] = [
            'name'  => __('menu.users'),
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 19.5a7.5 7.5 0 1115 0v.75H4.5v-.75z" /></svg>',
            'url'   => 'users',
            'order' => 600,
        ];        

        $view->with(compact('navbarItems'));
    }
}
