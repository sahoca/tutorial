# analizdegerleme.com — WordPress'ten Laravel'e Taşıma

Bu depo, `analizdegerleme.com` WordPress sitesinin yedeklenmesi ve PHP **Laravel 13 /
PHP 8.4** ile yeniden yazılması çalışmasını içerir.

## Klasör Yapısı

| Klasör | İçerik |
|--------|--------|
| `docs/` | Yedek talimatları, MariaDB kurulum & içerik aktarım rehberi, scriptler |
| `site-backup/data/` | WordPress içeriğinin temizlenmiş **JSON** hali (sayfa, yazı, kategori, menü, ayar) |
| `site-backup/` | (`uploads/`, `html/`) — görsel ve HTML yedekleri buraya konacak |
| `laravel-app/` | Yeni Laravel CMS (admin panel + site), **gerçek içerikle dolu** |

## ⚠️ Önce Yedek (hosting bugün doluyor)

Bu çalışma ortamı `analizdegerleme.com` adresine ağ politikası nedeniyle erişemiyor,
bu yüzden yedeği **kendi bilgisayarından** alman gerekiyor:

- 👉 **`docs/01-ACIL-YEDEK-AL.md`** — adım adım (veritabanı + FTP + HTML)
- 👉 **`docs/yedek-al.sh`** — bilgileri doldurup tek komutla çalıştır

Yedeği aldıktan sonra `site-backup/` altına koyup push edersen, içeriği Laravel'e aktarırım.

## Yeni Site (Laravel) — Hızlı Başlangıç (içerikle birlikte)

JSON içerik depoda; MariaDB gerekmez, SQLite ile çalışır:

```bash
cd laravel-app
composer install
php artisan key:generate
php artisan migrate --seed
php artisan content:import-json    # WordPress içeriğini JSON'dan yükler
php artisan storage:link
php artisan serve                  # http://127.0.0.1:8000
```

Admin: `http://127.0.0.1:8000/giris` — `admin@analizdegerleme.com` / `admin1234`

Ham WordPress dökümünden MariaDB ile kurulum için: `docs/02-MARIADB-VE-ICERIK.md`.

## Durum

- [x] PHP 8.4 + Laravel 13 kuruldu ve çalışıyor
- [x] Admin panel (giriş, sayfa/yazı/kategori/medya/menü/ayar/mesaj yönetimi)
- [x] Genel site (anasayfa, blog, yazı, dinamik sayfa, iletişim formu)
- [x] **WordPress veritabanı (site.sql) MariaDB'ye import edildi** (106 tablo)
- [x] **Gerçek içerik aktarıldı**: 11 sayfa, 37 yazı, 12 kategori, menü, ayarlar
- [x] İçerik JSON'a dönüştürüldü (`site-backup/data/`) — SQL → JSON ✓
- [x] İçe aktarma komutları: `wp:import-db`, `content:export-json`, `content:import-json`
- [ ] Görsellerin aktarımı (FTP `uploads` → `public/storage`)
- [ ] SPK/BDDK mevzuat & yönetmelik modülü (sıradaki)
- [ ] Eklenecek diğer modüller
