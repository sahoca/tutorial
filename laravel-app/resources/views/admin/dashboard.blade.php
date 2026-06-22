@extends('admin.layout')
@section('title', 'Gösterge Paneli')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @php
            $cards = [
                ['Sayfalar', $stats['pages'], 'admin.pages.index'],
                ['Yazılar', $stats['posts'], 'admin.posts.index'],
                ['Medya', $stats['media'], 'admin.media.index'],
                ['Okunmamış Mesaj', $stats['messages'], 'admin.messages.index'],
            ];
        @endphp
        @foreach ($cards as [$label, $value, $route])
            <a href="{{ route($route) }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                <div class="text-3xl font-bold">{{ $value }}</div>
                <div class="text-gray-500 text-sm mt-1">{{ $label }}</div>
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="font-semibold mb-4">Son Yazılar</h2>
            <ul class="divide-y text-sm">
                @forelse ($recentPosts as $post)
                    <li class="py-2 flex justify-between">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="hover:underline">{{ $post->title }}</a>
                        <span class="text-gray-400">{{ $post->created_at->format('d.m.Y') }}</span>
                    </li>
                @empty
                    <li class="py-2 text-gray-400">Henüz yazı yok.</li>
                @endforelse
            </ul>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="font-semibold mb-4">Son Mesajlar</h2>
            <ul class="divide-y text-sm">
                @forelse ($recentMessages as $message)
                    <li class="py-2 flex justify-between">
                        <a href="{{ route('admin.messages.show', $message) }}" class="hover:underline">{{ $message->name }}</a>
                        <span class="text-gray-400">{{ $message->created_at->format('d.m.Y') }}</span>
                    </li>
                @empty
                    <li class="py-2 text-gray-400">Henüz mesaj yok.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
