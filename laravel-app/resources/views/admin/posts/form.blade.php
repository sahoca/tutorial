@extends('admin.layout')
@section('title', $post->exists ? 'Yazı Düzenle' : 'Yeni Yazı')

@section('content')
    <form action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}"
          method="POST" enctype="multipart/form-data" class="max-w-3xl space-y-5">
        @csrf
        @if ($post->exists) @method('PUT') @endif

        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Başlık *</label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" required class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Slug (boş bırakılırsa otomatik)</label>
                <input type="text" name="slug" value="{{ old('slug', $post->slug) }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">İçerik (HTML)</label>
                <textarea name="content" rows="14" class="w-full border border-gray-300 rounded-md px-3 py-2 font-mono text-sm">{{ old('content', $post->content) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Özet</label>
                <textarea name="excerpt" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Öne Çıkan Görsel</label>
                @if ($post->featured_image)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}" class="h-32 rounded mb-2 object-cover">
                @endif
                <input type="file" name="image" accept="image/*" class="w-full text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Kategoriler</label>
                <div class="flex flex-wrap gap-3">
                    @forelse ($categories as $category)
                        <label class="flex items-center text-sm">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="mr-1"
                                @checked(in_array($category->id, old('categories', $post->categories->pluck('id')->toArray())))>
                            {{ $category->name }}
                        </label>
                    @empty
                        <span class="text-gray-400 text-sm">Önce kategori ekleyin.</span>
                    @endforelse
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Durum</label>
                <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="published" @selected(old('status', $post->status) === 'published')>Yayında</option>
                    <option value="draft" @selected(old('status', $post->status) === 'draft')>Taslak</option>
                </select>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-md hover:bg-gray-800">Kaydet</button>
            <a href="{{ route('admin.posts.index') }}" class="px-5 py-2 rounded-md border border-gray-300 hover:bg-gray-50">İptal</a>
        </div>
    </form>
@endsection
