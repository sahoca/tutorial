<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Yönetim Paneli') — Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-gray-200 flex flex-col">
        <div class="px-6 py-5 text-lg font-semibold border-b border-gray-700">
            {{ \App\Models\Setting::get('site_name', 'Analiz Değerleme') }}
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            @php
                $nav = [
                    ['admin.dashboard', 'Gösterge Paneli', []],
                    ['admin.pages.index', 'Sayfalar', ['admin/pages*']],
                    ['admin.posts.index', 'Yazılar', ['admin/posts*']],
                    ['admin.categories.index', 'Kategoriler', ['admin/categories*']],
                    ['admin.media.index', 'Medya', ['admin/media*']],
                    ['admin.menu.index', 'Menüler', ['admin/menu*']],
                    ['admin.messages.index', 'Mesajlar', ['admin/messages*']],
                    ['admin.settings.edit', 'Ayarlar', ['admin/settings*']],
                ];
            @endphp
            @foreach ($nav as [$route, $label, $patterns])
                <a href="{{ route($route) }}"
                   class="block px-3 py-2 rounded-md hover:bg-gray-800 {{ (request()->routeIs($route) || (count($patterns) && request()->is($patterns))) ? 'bg-gray-800 text-white font-medium' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
        <div class="px-3 py-4 border-t border-gray-700 space-y-1 text-sm">
            <a href="{{ route('home') }}" target="_blank" class="block px-3 py-2 rounded-md hover:bg-gray-800">Siteyi Görüntüle ↗</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-md hover:bg-gray-800">Çıkış Yap</button>
            </form>
        </div>
    </aside>

    {{-- Content --}}
    <main class="flex-1">
        <header class="bg-white shadow-sm px-8 py-4 flex items-center justify-between">
            <h1 class="text-xl font-semibold">@yield('title', 'Gösterge Paneli')</h1>
            <div class="text-sm text-gray-500">{{ auth()->user()?->name }}</div>
        </header>

        <div class="p-8">
            @if (session('status'))
                <div class="mb-6 rounded-md bg-green-100 border border-green-300 text-green-800 px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-6 rounded-md bg-red-100 border border-red-300 text-red-800 px-4 py-3">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>
</body>
</html>
