@extends('admin.layout')
@section('title', 'Yazılar')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <p class="text-gray-500">Toplam {{ $posts->total() }} yazı</p>
        <a href="{{ route('admin.posts.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-800">+ Yeni Yazı</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-6 py-3">Başlık</th>
                    <th class="px-6 py-3">Yazar</th>
                    <th class="px-6 py-3">Durum</th>
                    <th class="px-6 py-3">Tarih</th>
                    <th class="px-6 py-3 text-right">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($posts as $post)
                    <tr>
                        <td class="px-6 py-3 font-medium">{{ $post->title }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $post->author?->name ?? '—' }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded text-xs {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $post->status === 'published' ? 'Yayında' : 'Taslak' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $post->created_at->format('d.m.Y') }}</td>
                        <td class="px-6 py-3 text-right space-x-2">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="text-blue-600 hover:underline">Düzenle</a>
                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Silinsin mi?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-6 text-center text-gray-400">Henüz yazı yok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $posts->links() }}</div>
@endsection
