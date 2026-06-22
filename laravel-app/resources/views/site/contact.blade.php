@extends('site.layout')
@section('meta_title', 'İletişim — ' . ($siteSettings['site_name'] ?? ''))

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-12 grid md:grid-cols-2 gap-12">
        <div>
            <h1 class="text-3xl font-bold mb-6">İletişim</h1>
            <div class="space-y-3 text-gray-600">
                @if (!empty($siteSettings['contact_address']))
                    <p><strong class="text-gray-800">Adres:</strong><br>{{ $siteSettings['contact_address'] }}</p>
                @endif
                @if (!empty($siteSettings['contact_phone']))
                    <p><strong class="text-gray-800">Telefon:</strong> {{ $siteSettings['contact_phone'] }}</p>
                @endif
                @if (!empty($siteSettings['contact_email']))
                    <p><strong class="text-gray-800">E-posta:</strong> {{ $siteSettings['contact_email'] }}</p>
                @endif
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-6">
            <h2 class="font-semibold mb-4">Mesaj Gönderin</h2>
            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                @csrf
                @if ($errors->any())
                    <div class="rounded-md bg-red-100 border border-red-300 text-red-800 px-4 py-2 text-sm">
                        <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif
                <input type="text" name="name" placeholder="Adınız *" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                <div class="grid grid-cols-2 gap-4">
                    <input type="email" name="email" placeholder="E-posta" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <input type="text" name="phone" placeholder="Telefon" value="{{ old('phone') }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                <input type="text" name="subject" placeholder="Konu" value="{{ old('subject') }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
                <textarea name="message" placeholder="Mesajınız *" rows="5" required class="w-full border border-gray-300 rounded-md px-3 py-2">{{ old('message') }}</textarea>
                <button type="submit" class="w-full bg-gray-900 text-white py-2.5 rounded-md hover:bg-gray-800">Gönder</button>
            </form>
        </div>
    </div>
@endsection
