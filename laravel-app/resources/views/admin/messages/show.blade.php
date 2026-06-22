@extends('admin.layout')
@section('title', 'Mesaj Detayı')

@section('content')
    <div class="max-w-2xl bg-white rounded-lg shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Ad:</span> <strong>{{ $message->name }}</strong></div>
            <div><span class="text-gray-500">Tarih:</span> {{ $message->created_at->format('d.m.Y H:i') }}</div>
            <div><span class="text-gray-500">E-posta:</span> {{ $message->email ?? '—' }}</div>
            <div><span class="text-gray-500">Telefon:</span> {{ $message->phone ?? '—' }}</div>
            <div class="col-span-2"><span class="text-gray-500">Konu:</span> {{ $message->subject ?? '—' }}</div>
        </div>
        <hr>
        <div class="whitespace-pre-line text-gray-800">{{ $message->message }}</div>
        <div class="flex gap-3 pt-4">
            @if ($message->email)
                <a href="mailto:{{ $message->email }}" class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-800">Yanıtla</a>
            @endif
            <a href="{{ route('admin.messages.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm hover:bg-gray-50">Geri</a>
            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Silinsin mi?')">
                @csrf @method('DELETE')
                <button class="px-4 py-2 rounded-md border border-red-300 text-red-600 text-sm hover:bg-red-50">Sil</button>
            </form>
        </div>
    </div>
@endsection
