<?php

namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MenuViewComposer
{
    public function compose(View $view)
    {
        $navbarItems = [
            'dashboard' => [
                'name'  => __('menu.dashboard'),
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'url'   => 'dashboard',
                'order' => 20,
            ],
        ];

        $navbarItems['products'] = [
            'name'  => __('menu.products'),
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            'url'   => 'products',
            'order' => 500,
        ];

        $navbarItems['stores'] = [
            'name'  => __('menu.stores'),
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            'url'   => 'stores',
            'order' => 400,
        ];

        $navbarItems['orders'] = [
            'name'          => __('menu.orders'),
            'icon'          => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            'url'           => 'orders',
            'secondary_url' => [
                'orders.create'
            ],
            'order'         => 200,
        ];

        $view->with(compact('navbarItems'));
    }
}
