@extends('admin.layout')
@section('title', 'Site Ayarları')

@section('content')
    @php $s = fn ($k) => \App\Models\Setting::get($k); @endphp
    <form action="{{ route('admin.settings.update') }}" method="POST" class="max-w-2xl space-y-5">
        @csrf @method('PUT')

        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-sm text-gray-500 uppercase">Genel</h3>
            <div>
                <label class="block text-sm font-medium mb-1">Site Adı</label>
                <input type="text" name="site_name" value="{{ old('site_name', $s('site_name')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Slogan</label>
                <input type="text" name="site_tagline" value="{{ old('site_tagline', $s('site_tagline')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Açıklama (SEO)</label>
                <textarea name="site_description" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2">{{ old('site_description', $s('site_description')) }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-sm text-gray-500 uppercase">İletişim</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">E-posta</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $s('contact_email')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Telefon</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $s('contact_phone')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Adres</label>
                <textarea name="contact_address" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2">{{ old('contact_address', $s('contact_address')) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Footer Metni</label>
                <input type="text" name="footer_text" value="{{ old('footer_text', $s('footer_text')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
        </div>

        <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-md hover:bg-gray-800">Kaydet</button>
    </form>
@endsection
