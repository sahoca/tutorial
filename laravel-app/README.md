# Analiz Değerleme — Laravel CMS

`analizdegerleme.com` (WordPress) sitesinin Laravel 13 + PHP 8.4 ile yeniden yazılmış
sürümü. WordPress benzeri içerik yönetimi ve admin paneli içerir.

## Özellikler

- **Admin Panel** (`/admin`) — oturum tabanlı giriş, yetki kontrolü
- **Sayfalar** — statik sayfalar (Hakkımızda, Hizmetler vb.), SEO alanları
- **Yazılar (Blog)** — kategori, öne çıkan görsel, taslak/yayın durumu
- **Kategoriler**
- **Medya kütüphanesi** — dosya yükleme
- **Menü yönetimi** — header / footer
- **Site ayarları** — site adı, slogan, iletişim bilgileri
- **İletişim formu** — gelen mesajlar admin panelinde
- **WordPress içe aktarma** — `php artisan wp:import dosya.xml`

## Kurulum

```bash
cd laravel-app
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Varsayılan veritabanı **SQLite** (`database/database.sqlite`). MySQL kullanmak için
`.env` içinde `DB_CONNECTION=mysql` ve bağlantı bilgilerini ayarla.

## Giriş Bilgileri (seed)

- URL: `/giris`
- E-posta: `admin@analizdegerleme.com`
- Şifre: `admin1234`

> ⚠️ Canlıya almadan önce bu şifreyi mutlaka değiştir.

## WordPress İçeriğini Aktarma

1. Eski WordPress'te **Araçlar → Dışa Aktar → Tüm içerik** ile `.xml` (WXR) indir.
2. Dosyayı sunucuya koy ve çalıştır:
   ```bash
   php artisan wp:import /path/to/export.xml
   ```
3. Görseller: WordPress `wp-content/uploads` klasörünü `public/storage` altına kopyala.

Alternatif olarak elinde sadece **SQL dump** varsa, `wp_posts` tablosundaki içerik
`pages`/`posts` tablolarına aktarılabilir — gerekirse bunun için ayrı bir komut eklenir.

## Teknik

- Laravel 13.x, PHP 8.4
- Tailwind CSS (CDN — build adımı gerektirmez)
- Yapı: `app/Http/Controllers/Admin`, `app/Models`, `resources/views/{admin,site}`
