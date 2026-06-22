@extends('site.layout')
@section('meta_title', 'Blog — ' . ($siteSettings['site_name'] ?? 'Analiz Değerleme'))

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-8">Blog</h1>
        @if ($posts->count())
            <div class="grid md:grid-cols-3 gap-8">
                @foreach ($posts as $post)
                    @include('site.partials.post-card', ['post' => $post])
                @endforeach
            </div>
            <div class="mt-10">{{ $posts->links() }}</div>
        @else
            <p class="text-gray-500">Henüz yazı yayımlanmadı.</p>
        @endif
    </div>
@endsection
