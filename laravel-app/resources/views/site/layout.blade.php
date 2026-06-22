<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('meta_title', ($siteSettings['site_name'] ?? 'Analiz Değerleme'))</title>
    <meta name="description" content="@yield('meta_description', ($siteSettings['site_description'] ?? ''))">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-800 antialiased flex flex-col min-h-screen">
    {{-- Header --}}
    <header class="border-b sticky top-0 bg-white/95 backdrop-blur z-20">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-xl font-bold tracking-tight">
                {{ $siteSettings['site_name'] ?? 'Analiz Değerleme' }}
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('home') }}" class="hover:text-blue-600">Anasayfa</a>
                @foreach ($headerMenu as $item)
                    <a href="{{ $item->url }}" class="hover:text-blue-600">{{ $item->label }}</a>
                @endforeach
                <a href="{{ route('assignments') }}" class="hover:text-blue-600">Görevlendirmeler</a>
                <a href="{{ route('blog') }}" class="hover:text-blue-600">Blog</a>
                <a href="{{ route('contact') }}" class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-800">İletişim</a>
            </nav>
        </div>
    </header>

    <main class="flex-1">
        @if (session('status'))
            <div class="max-w-6xl mx-auto px-4 mt-4">
                <div class="rounded-md bg-green-100 border border-green-300 text-green-800 px-4 py-3">{{ session('status') }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-300 mt-16">
        <div class="max-w-6xl mx-auto px-4 py-10 grid md:grid-cols-3 gap-8 text-sm">
            <div>
                <div class="text-white font-bold text-lg mb-2">{{ $siteSettings['site_name'] ?? 'Analiz Değerleme' }}</div>
                <p class="text-gray-400">{{ $siteSettings['site_tagline'] ?? '' }}</p>
            </div>
            <div>
                <div class="text-white font-semibold mb-2">Menü</div>
                <ul class="space-y-1">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Anasayfa</a></li>
                    <li><a href="{{ route('blog') }}" class="hover:text-white">Blog</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white">İletişim</a></li>
                    @foreach ($footerMenu as $item)
                        <li><a href="{{ $item->url }}" class="hover:text-white">{{ $item->label }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <div class="text-white font-semibold mb-2">İletişim</div>
                @if (!empty($siteSettings['contact_phone']))<p>{{ $siteSettings['contact_phone'] }}</p>@endif
                @if (!empty($siteSettings['contact_email']))<p>{{ $siteSettings['contact_email'] }}</p>@endif
                @if (!empty($siteSettings['contact_address']))<p class="text-gray-400 mt-1">{{ $siteSettings['contact_address'] }}</p>@endif
            </div>
        </div>
        <div class="border-t border-gray-800 py-4 text-center text-xs text-gray-500">
            {{ $siteSettings['footer_text'] ?? '© ' . date('Y') . ' ' . ($siteSettings['site_name'] ?? 'Analiz Değerleme') . '. Tüm hakları saklıdır.' }}
        </div>
    </footer>
    {{-- Eksik gorseller icin placeholder (uploads henuz aktarilmadiysa) --}}
    <script>
        document.addEventListener('error', function (e) {
            var t = e.target;
            if (t && t.tagName === 'IMG' && !t.dataset.fallback) {
                t.dataset.fallback = '1';
                t.src = '/images/placeholder.svg';
            }
        }, true);
    </script>
</body>
</html>
