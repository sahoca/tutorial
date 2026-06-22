@extends('admin.layout')
@section('title', $assignment->exists ? 'Görevlendirme Düzenle' : 'Yeni Görevlendirme')

@section('content')
    <form action="{{ $assignment->exists ? route('admin.assignments.update', $assignment) : route('admin.assignments.store') }}"
          method="POST" class="max-w-3xl space-y-5">
        @csrf
        @if ($assignment->exists) @method('PUT') @endif

        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Başlık *</label>
                <input type="text" name="title" value="{{ old('title', $assignment->title) }}" required class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Kurum *</label>
                    <select name="authority" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        @foreach (\App\Models\Assignment::AUTHORITIES as $auth)
                            <option value="{{ $auth }}" @selected(old('authority', $assignment->authority) === $auth)>{{ $auth }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Referans / Görev No</label>
                    <input type="text" name="reference_no" value="{{ old('reference_no', $assignment->reference_no) }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Görev Tarihi</label>
                    <input type="date" name="assignment_date" value="{{ old('assignment_date', optional($assignment->assignment_date)->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Son Tarih</label>
                    <input type="date" name="due_date" value="{{ old('due_date', optional($assignment->due_date)->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Açıklama</label>
                <textarea name="description" rows="6" class="w-full border border-gray-300 rounded-md px-3 py-2">{{ old('description', $assignment->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Belge Bağlantısı (PDF/URL)</label>
                <input type="text" name="document_url" value="{{ old('document_url', $assignment->document_url) }}" placeholder="/storage/uploads/2026/01/belge.pdf" class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 grid grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium mb-1">Durum</label>
                <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    @foreach (\App\Models\Assignment::STATUSES as $val => $label)
                        <option value="{{ $val }}" @selected(old('status', $assignment->status) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Sıra</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $assignment->sort_order ?? 0) }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
            <label class="flex items-center text-sm pb-2">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" class="mr-2" @checked(old('is_published', $assignment->is_published ?? true))> Yayında
            </label>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-md hover:bg-gray-800">Kaydet</button>
            <a href="{{ route('admin.assignments.index') }}" class="px-5 py-2 rounded-md border border-gray-300 hover:bg-gray-50">İptal</a>
        </div>
    </form>
@endsection
