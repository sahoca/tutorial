@extends('admin.layout')
@section('title', 'İletişim Mesajları')

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr><th class="px-6 py-3">Ad</th><th class="px-6 py-3">Konu</th><th class="px-6 py-3">Tarih</th><th class="px-6 py-3">Durum</th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($messages as $message)
                    <tr class="{{ $message->read_at ? '' : 'font-semibold bg-blue-50/40' }}">
                        <td class="px-6 py-3"><a href="{{ route('admin.messages.show', $message) }}" class="hover:underline">{{ $message->name }}</a></td>
                        <td class="px-6 py-3 text-gray-600">{{ $message->subject ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $message->created_at->format('d.m.Y H:i') }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded text-xs {{ $message->read_at ? 'bg-gray-200 text-gray-600' : 'bg-blue-100 text-blue-700' }}">
                                {{ $message->read_at ? 'Okundu' : 'Yeni' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">Henüz mesaj yok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $messages->links() }}</div>
@endsection
