<div class="overflow-x-auto relative shadow-md sm:rounded-lg mt-4">
    <table {{$attributes->merge(['class' => 'w-full text-sm text-center text-gray-500 dark:text-gray-400'])}}>
        @isset($head)
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                {{$head}}
            </tr>
            </thead>
        @endif
        <tbody>
        {{$slot}}
    </table>
    @isset($footer)
        {{$footer}}
    @endisset
</div>
