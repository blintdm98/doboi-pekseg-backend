<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin - Doboi Pékség</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex">
        {{-- Oldalsó menü (sidebar) --}}
        <aside class="w-64 h-screen bg-white shadow-md">
            <div class="p-6 font-bold text-lg border-b">Doboi Pékség</div>
            <nav class="p-4">
                <ul class="space-y-4">
                    <li>
                        <a href="/admin/orders" class="text-gray-700 hover:text-yellow-600 font-semibold block">Rendelések</a>
                    </li>
                    <li>
                        <a href="/admin/products" class="text-gray-700 hover:text-yellow-600 block">Termékek</a>
                    </li>
                    <li>
                        <a href="/admin/stores" class="text-gray-700 hover:text-yellow-600 block">Üzletek</a>
                    </li>
                </ul>
            </nav>
        </aside>

        {{-- Fő tartalom --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>
