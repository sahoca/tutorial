@extends('admin.layout')
@section('title', 'Menüler')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            @foreach (['header' => 'Üst Menü (Header)', 'footer' => 'Alt Menü (Footer)'] as $loc => $title)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-3 border-b font-semibold">{{ $title }}</div>
                    <table class="w-full text-sm">
                        <tbody class="divide-y">
                            @forelse ($items[$loc] ?? [] as $item)
                                <tr>
                                    <td class="px-6 py-3">{{ $item->sort_order }}</td>
                                    <td class="px-6 py-3 font-medium">{{ $item->label }}</td>
                                    <td class="px-6 py-3 text-gray-500">{{ $item->url }}</td>
                                    <td class="px-6 py-3 text-right">
                                        <form action="{{ route('admin.menu.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Silinsin mi?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:underline">Sil</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-4 text-center text-gray-400">Öğe yok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="font-semibold mb-4">Yeni Menü Öğesi</h2>
            <form action="{{ route('admin.menu.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="label" placeholder="Etiket *" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                <input type="text" name="url" placeholder="URL (ör. /hakkimizda)" value="/" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                <select name="location" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="header">Üst Menü</option>
                    <option value="footer">Alt Menü</option>
                </select>
                <input type="number" name="sort_order" placeholder="Sıra" value="0" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                <button class="w-full bg-gray-900 text-white py-2 rounded-md text-sm hover:bg-gray-800">Ekle</button>
            </form>
        </div>
    </div>
@endsection
