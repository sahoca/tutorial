@extends('site.layout')
@section('meta_title', $assignment->title . ' — ' . ($siteSettings['site_name'] ?? ''))

@section('content')
    <article class="max-w-3xl mx-auto px-4 py-12">
        <a href="{{ route('assignments') }}" class="text-sm text-blue-600 hover:underline">← Görevlendirmeler</a>

        <div class="flex items-center gap-2 mt-4 mb-2">
            <span class="px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-700">{{ $assignment->authority }}</span>
            <span class="text-xs px-2 py-0.5 rounded {{ $assignment->status === 'completed' ? 'bg-green-100 text-green-700' : ($assignment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">{{ $assignment->status_label }}</span>
        </div>

        <h1 class="text-3xl font-bold mb-4">{{ $assignment->title }}</h1>

        <dl class="grid grid-cols-2 gap-3 text-sm bg-gray-50 rounded-lg p-4 mb-6">
            @if ($assignment->reference_no)
                <div><dt class="text-gray-500">Referans / Görev No</dt><dd class="font-medium">{{ $assignment->reference_no }}</dd></div>
            @endif
            @if ($assignment->assignment_date)
                <div><dt class="text-gray-500">Görev Tarihi</dt><dd class="font-medium">{{ $assignment->assignment_date->format('d.m.Y') }}</dd></div>
            @endif
            @if ($assignment->due_date)
                <div><dt class="text-gray-500">Son Tarih</dt><dd class="font-medium">{{ $assignment->due_date->format('d.m.Y') }}</dd></div>
            @endif
        </dl>

        @if ($assignment->description)
            <div class="prose max-w-none">{!! nl2br(e($assignment->description)) !!}</div>
        @endif

        @if ($assignment->document_url)
            <a href="{{ $assignment->document_url }}" target="_blank" class="inline-block mt-6 bg-gray-900 text-white px-5 py-2.5 rounded-md hover:bg-gray-800">📄 Belgeyi İndir / Görüntüle</a>
        @endif
    </article>
@endsection
