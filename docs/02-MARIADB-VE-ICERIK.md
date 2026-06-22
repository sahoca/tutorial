# MariaDB Kurulumu ve WordPress İçeriğinin Aktarımı

WordPress veritabanı dökümü (`site.sql`, MariaDB 10.11) bu ortamda **MariaDB'ye import
edildi** ve içerik Laravel CMS'e aktarıldı. Bu döküman süreci ve yeniden üretmeyi anlatır.

## Yapılanlar

1. MariaDB 10.11 kuruldu, `analizdegerleme` veritabanı oluşturuldu, döküm import edildi (106 tablo).
2. Laravel `mariadb` sürücüsüyle aynı veritabanına bağlandı; CMS tabloları (`pages`, `posts`,
   `categories`, `menu_items`, `settings` …) `wp_*` tablolarının yanına eklendi.
3. `php artisan wp:import-db` ile içerik aktarıldı:
   - **11 sayfa** (Hakkımızda, Misyon-Vizyon, Yönetim Kadromuz, Şirket Ana Sözleşmesi,
     Hizmetlerimiz, Yetki Belgelerimiz, KVKK …)
   - **37 yazı** (değerleme hizmetleri içerikleri), **12 kategori**, menü ve site ayarları
   - İçerik temizlendi: Gutenberg blok yorumları ve WPBakery kısa kodları (`[vc_row]` vb.) ayıklandı.
4. `php artisan content:export-json` ile içerik `site-backup/data/*.json` olarak dışa aktarıldı.

## ⚠️ Güvenlik notu — ham SQL repoya konmadı

`site.sql` dökümü hassas veri içeriyor: `wp_users` (şifre hash'leri), `wp_options`
(olası API anahtarları / SMTP şifreleri), `wp_paytr_iframe_transaction` (ödeme kayıtları),
Wordfence tabloları. Bu yüzden **ham döküm depoya eklenmedi.** Depoya yalnızca herkese
açık, temizlenmiş **içerik JSON'ları** kondu (`site-backup/data/`).

Dökümü saklamak istersen güvenli bir yerde tut; gerekiyorsa şifre/anahtar alanlarını
temizledikten sonra paylaş.

## Sıfırdan Kurulum — İki Yol

### Yol A) Sadece JSON'dan (MariaDB gerekmez, en kolay)

Depodaki JSON yeterli; SQLite ile çalışır:

```bash
cd laravel-app
composer install
php artisan key:generate
php artisan migrate --seed
php artisan content:import-json     # site-backup/data/*.json -> CMS
php artisan storage:link
php artisan serve
```

### Yol B) Ham WordPress dökümünden (MariaDB ile)

Elinde `site.sql` varsa tam veriyle:

```bash
# 1) MariaDB başlat ve dökümü içe aktar
bash docs/mariadb-kur.sh /yol/site.sql

# 2) laravel-app/.env içinde MariaDB ayarlarını gir:
#    DB_CONNECTION=mariadb
#    DB_HOST=127.0.0.1
#    DB_DATABASE=analizdegerleme
#    DB_USERNAME=laravel
#    DB_PASSWORD=laravel_secret

cd laravel-app
php artisan migrate --force        # CMS tablolarını wp_* yanına ekler (fresh DEĞİL!)
php artisan db:seed --force
php artisan wp:import-db            # wp_* -> CMS
php artisan serve
```

> ⚠️ MariaDB kullanırken **asla `migrate:fresh` çalıştırma** — tüm `wp_*` tablolarını siler.

## Görseller

İçerikteki ve öne çıkan görsel URL'leri hâlâ `http://analizdegerleme.com/wp-content/uploads/...`
adresine işaret ediyor. Kalıcı hale getirmek için:

1. FTP'den `wp-content/uploads` klasörünü indir.
2. `laravel-app/public/storage/uploads` altına kopyala.
3. İçerikteki eski alan adını yeni adresinle değiştir (toplu replace).
