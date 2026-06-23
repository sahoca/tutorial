@extends('admin.layout')
@section('title', $page->exists ? 'Sayfa Düzenle' : 'Yeni Sayfa')

@section('content')
    <form action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}"
          method="POST" class="max-w-3xl space-y-5">
        @csrf
        @if ($page->exists) @method('PUT') @endif

        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Başlık *</label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}" required
                       class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Slug (boş bırakılırsa otomatik)</label>
                <input type="text" name="slug" value="{{ old('slug', $page->slug) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">İçerik (HTML)</label>
                <textarea name="content" rows="14" class="w-full border border-gray-300 rounded-md px-3 py-2 font-mono text-sm">{{ old('content', $page->content) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Özet</label>
                <textarea name="excerpt" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2">{{ old('excerpt', $page->excerpt) }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-sm text-gray-500 uppercase">SEO & Yayın</h3>
            <div>
                <label class="block text-sm font-medium mb-1">Meta Başlık</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Meta Açıklama</label>
                <textarea name="meta_description" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2">{{ old('meta_description', $page->meta_description) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Durum</label>
                    <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="published" @selected(old('status', $page->status) === 'published')>Yayında</option>
                        <option value="draft" @selected(old('status', $page->status) === 'draft')>Taslak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Sıra</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $page->sort_order ?? 0) }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-md hover:bg-gray-800">Kaydet</button>
            <a href="{{ route('admin.pages.index') }}" class="px-5 py-2 rounded-md border border-gray-300 hover:bg-gray-50">İptal</a>
        </div>
    </form>
@endsection
