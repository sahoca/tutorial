@extends('site.layout')

@section('content')
    {{-- Hero --}}
    <section class="bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-6xl mx-auto px-4 py-20 text-center">
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">
                {{ $siteSettings['site_name'] ?? 'Analiz Değerleme' }}
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-8">
                {{ $siteSettings['site_tagline'] ?? 'Gayrimenkul değerleme ve analiz hizmetlerinde güvenilir çözüm ortağınız.' }}
            </p>
            <a href="{{ route('contact') }}" class="inline-block bg-gray-900 text-white px-6 py-3 rounded-md hover:bg-gray-800">Bize Ulaşın</a>
        </div>
    </section>

    @if ($page && $page->content)
        <section class="max-w-4xl mx-auto px-4 py-12 prose max-w-none">
            {!! $page->content !!}
        </section>
    @endif

    {{-- Son yazılar --}}
    @if ($posts->count())
        <section class="max-w-6xl mx-auto px-4 py-12">
            <h2 class="text-2xl font-bold mb-8">Son Yazılar</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach ($posts as $post)
                    @include('site.partials.post-card', ['post' => $post])
                @endforeach
            </div>
        </section>
    @endif
@endsection
