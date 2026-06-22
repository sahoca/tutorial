@extends('site.layout')
@section('meta_title', ($post->meta_title ?: $post->title) . ' — ' . ($siteSettings['site_name'] ?? ''))
@section('meta_description', $post->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 155))

@section('content')
    <article class="max-w-3xl mx-auto px-4 py-12">
        <div class="text-sm text-gray-400 mb-2">{{ optional($post->published_at)->format('d F Y') }}</div>
        <h1 class="text-4xl font-bold mb-6">{{ $post->title }}</h1>
        @if ($post->featured_image)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}" class="w-full rounded-lg mb-8 object-cover">
        @endif
        <div class="prose max-w-none">{!! $post->content !!}</div>
    </article>

    @if ($related->count())
        <section class="max-w-6xl mx-auto px-4 py-12 border-t">
            <h2 class="text-xl font-bold mb-6">İlgili Yazılar</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach ($related as $post)
                    @include('site.partials.post-card', ['post' => $post])
                @endforeach
            </div>
        </section>
    @endif
@endsection
