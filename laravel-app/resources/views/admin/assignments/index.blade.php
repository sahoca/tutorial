@extends('admin.layout')
@section('title', 'Görevlendirmeler')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <p class="text-gray-500">SPK / BDDK görevlendirmeleri — toplam {{ $assignments->total() }}</p>
        <a href="{{ route('admin.assignments.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-800">+ Yeni Görevlendirme</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-6 py-3">Başlık</th>
                    <th class="px-6 py-3">Kurum</th>
                    <th class="px-6 py-3">Referans</th>
                    <th class="px-6 py-3">Tarih</th>
                    <th class="px-6 py-3">Durum</th>
                    <th class="px-6 py-3 text-right">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($assignments as $a)
                    <tr>
                        <td class="px-6 py-3 font-medium">{{ $a->title }}</td>
                        <td class="px-6 py-3"><span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-700">{{ $a->authority }}</span></td>
                        <td class="px-6 py-3 text-gray-500">{{ $a->reference_no ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ optional($a->assignment_date)->format('d.m.Y') ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $a->status_label }} {{ $a->is_published ? '' : '(gizli)' }}</td>
                        <td class="px-6 py-3 text-right space-x-2">
                            <a href="{{ route('admin.assignments.edit', $a) }}" class="text-blue-600 hover:underline">Düzenle</a>
                            <form action="{{ route('admin.assignments.destroy', $a) }}" method="POST" class="inline" onsubmit="return confirm('Silinsin mi?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-6 text-center text-gray-400">Henüz görevlendirme yok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $assignments->links() }}</div>
@endsection
