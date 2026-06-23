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

## İçerik Aktarma Komutları

| Komut | Açıklama |
|-------|----------|
| `php artisan content:import-json` | `site-backup/data/*.json`'dan içeriği yükler (varsayılan, MariaDB gerekmez) |
| `php artisan content:export-json` | Mevcut CMS içeriğini JSON'a aktarır |
| `php artisan wp:import-db` | Aynı veritabanındaki WordPress `wp_*` tablolarından aktarır (MariaDB) |
| `php artisan wp:import dosya.xml` | WordPress WXR (XML) export dosyasından aktarır |

WordPress veritabanı dökümünden (site.sql) MariaDB ile tam kurulum:
`../docs/02-MARIADB-VE-ICERIK.md`.

### Görseller

İçerikteki görseller eski alan adına işaret eder. Kalıcı yapmak için FTP'den
`wp-content/uploads` klasörünü `public/storage/uploads` altına kopyala ve içerikteki
eski alan adını toplu değiştir.

## Teknik

- Laravel 13.x, PHP 8.4
- Tailwind CSS (CDN — build adımı gerektirmez)
- Yapı: `app/Http/Controllers/Admin`, `app/Models`, `resources/views/{admin,site}`
