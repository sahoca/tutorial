@extends('admin.layout')
@section('title', 'Medya Kütüphanesi')

@section('content')
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-4">
            @csrf
            <input type="file" name="files[]" multiple required class="flex-1 text-sm">
            <button class="bg-gray-900 text-white px-5 py-2 rounded-md text-sm hover:bg-gray-800">Yükle</button>
        </form>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
        @forelse ($media as $item)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                @if (str_starts_with($item->mime_type ?? '', 'image/'))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($item->path) }}" class="h-32 w-full object-cover">
                @else
                    <div class="h-32 flex items-center justify-center bg-gray-100 text-gray-400 text-xs">{{ strtoupper(pathinfo($item->filename, PATHINFO_EXTENSION)) }}</div>
                @endif
                <div class="p-2">
                    <p class="text-xs truncate" title="{{ $item->filename }}">{{ $item->filename }}</p>
                    <div class="flex justify-between items-center mt-1">
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($item->path) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Aç</a>
                        <form action="{{ route('admin.media.destroy', $item) }}" method="POST" onsubmit="return confirm('Silinsin mi?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-600 hover:underline">Sil</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-400 py-10">Henüz medya yok.</p>
        @endforelse
    </div>
    <div class="mt-4">{{ $media->links() }}</div>
@endsection
