@extends('site.layout')
@section('meta_title', 'Görevlendirmeler — ' . ($siteSettings['site_name'] ?? ''))
@section('meta_description', 'SPK ve BDDK tarafından şirketimize verilen değerleme görevlendirmeleri ve düzenleyici bildirimler.')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-2">Görevlendirmeler</h1>
        <p class="text-gray-600 mb-8">Sermaye Piyasası Kurulu (SPK) ve Bankacılık Düzenleme ve Denetleme Kurumu (BDDK) düzenlemeleri kapsamında şirketimize verilen değerleme görevleri ve ilgili belgeler.</p>

        {{-- Kurum filtresi --}}
        <div class="flex flex-wrap gap-2 mb-8">
            <a href="{{ route('assignments') }}" class="px-3 py-1.5 rounded-full text-sm {{ !request('kurum') ? 'bg-gray-900 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">Tümü</a>
            @foreach ($authorities as $auth)
                <a href="{{ route('assignments', ['kurum' => $auth]) }}" class="px-3 py-1.5 rounded-full text-sm {{ request('kurum') === $auth ? 'bg-gray-900 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">{{ $auth }}</a>
            @endforeach
        </div>

        <div class="space-y-4">
            @forelse ($assignments as $a)
                <div class="border rounded-lg p-5 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-700">{{ $a->authority }}</span>
                                @if ($a->reference_no)<span class="text-xs text-gray-400">{{ $a->reference_no }}</span>@endif
                            </div>
                            <h2 class="font-semibold text-lg">
                                <a href="{{ route('assignment', $a->slug) }}" class="hover:text-blue-600">{{ $a->title }}</a>
                            </h2>
                            @if ($a->description)
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($a->description), 160) }}</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-sm text-gray-500">{{ optional($a->assignment_date)->format('d.m.Y') }}</div>
                            <span class="text-xs px-2 py-0.5 rounded {{ $a->status === 'completed' ? 'bg-green-100 text-green-700' : ($a->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">{{ $a->status_label }}</span>
                        </div>
                    </div>
                    @if ($a->document_url)
                        <a href="{{ $a->document_url }}" target="_blank" class="inline-block mt-3 text-sm text-blue-600 hover:underline">📄 Belgeyi görüntüle</a>
                    @endif
                </div>
            @empty
                <p class="text-gray-500 text-center py-10">Bu kategoride görevlendirme bulunmuyor.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $assignments->links() }}</div>
    </div>
@endsection
