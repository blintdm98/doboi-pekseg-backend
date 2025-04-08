@php
    $active = (request()->route()->getName() == $item['url']) ||
              (isset($item['secondary_url']) && in_array(request()->route()->getName(),$item['secondary_url']));
@endphp

<a href="{{route($item['url'])}}"
   class="border-l-[3px] ease-in-out duration-200 flex items-center gap-2 {{ $active ? 'border-blue-500 bg-blue-50 dark:bg-blue-500/20 dark:text-blue-50 text-blue-700' : 'border-transparent px-4 py-3 text-gray-500 hover:border-gray-100 hover:bg-gray-50 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-gray-200' }}  px-4 py-3  ">
    {!! $item['icon'] !!}
    <span class="text-sm font-medium">{{$item['name']}}</span>
</a>

