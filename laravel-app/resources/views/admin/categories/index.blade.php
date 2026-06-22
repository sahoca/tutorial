@extends('admin.layout')
@section('title', 'Kategoriler')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-500">
                    <tr><th class="px-6 py-3">Ad</th><th class="px-6 py-3">Slug</th><th class="px-6 py-3">Yazı</th><th class="px-6 py-3 text-right">İşlem</th></tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($categories as $category)
                        <tr>
                            <td class="px-6 py-3 font-medium">{{ $category->name }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $category->slug }}</td>
                            <td class="px-6 py-3">{{ $category->posts_count }}</td>
                            <td class="px-6 py-3 text-right">
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Silinsin mi?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Sil</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">Henüz kategori yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="font-semibold mb-4">Yeni Kategori</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="name" placeholder="Ad *" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                <input type="text" name="slug" placeholder="Slug (opsiyonel)" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                <textarea name="description" placeholder="Açıklama" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"></textarea>
                <button class="w-full bg-gray-900 text-white py-2 rounded-md text-sm hover:bg-gray-800">Ekle</button>
            </form>
        </div>
    </div>
    <div class="mt-4">{{ $categories->links() }}</div>
@endsection
