<article class="border rounded-lg overflow-hidden hover:shadow-md transition">
    @if ($post->featured_image)
        <a href="{{ route('post', $post->slug) }}">
            <img src="{{ $post->featured_url }}" class="h-44 w-full object-cover">
        </a>
    @endif
    <div class="p-5">
        <div class="text-xs text-gray-400 mb-2">{{ optional($post->published_at)->format('d.m.Y') }}</div>
        <h3 class="font-semibold text-lg mb-2">
            <a href="{{ route('post', $post->slug) }}" class="hover:text-blue-600">{{ $post->title }}</a>
        </h3>
        <p class="text-sm text-gray-600 line-clamp-3">{{ $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}</p>
        <a href="{{ route('post', $post->slug) }}" class="inline-block mt-3 text-sm text-blue-600 hover:underline">Devamını oku →</a>
    </div>
</article>
