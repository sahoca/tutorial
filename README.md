# analizdegerleme.com — WordPress'ten Laravel'e Taşıma

Bu depo, `analizdegerleme.com` WordPress sitesinin yedeklenmesi ve PHP **Laravel 13 /
PHP 8.4** ile yeniden yazılması çalışmasını içerir.

## Klasör Yapısı

| Klasör | İçerik |
|--------|--------|
| `docs/` | 🔴 **Acil yedek talimatları** ve hazır yedek scripti |
| `site-backup/` | Eski siteden alınan yedekler buraya konacak (`database/`, `uploads/`, `html/`) |
| `laravel-app/` | Yeni Laravel CMS (admin panel + site) |

## ⚠️ Önce Yedek (hosting bugün doluyor)

Bu çalışma ortamı `analizdegerleme.com` adresine ağ politikası nedeniyle erişemiyor,
bu yüzden yedeği **kendi bilgisayarından** alman gerekiyor:

- 👉 **`docs/01-ACIL-YEDEK-AL.md`** — adım adım (veritabanı + FTP + HTML)
- 👉 **`docs/yedek-al.sh`** — bilgileri doldurup tek komutla çalıştır

Yedeği aldıktan sonra `site-backup/` altına koyup push edersen, içeriği Laravel'e aktarırım.

## Yeni Site (Laravel) — Hızlı Başlangıç

```bash
cd laravel-app
composer install
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve     # http://127.0.0.1:8000
```

Admin: `http://127.0.0.1:8000/giris` — `admin@analizdegerleme.com` / `admin1234`

Detaylar için `laravel-app/README.md`.

## Durum

- [x] PHP 8.4 + Laravel 13 kuruldu ve çalışıyor
- [x] Admin panel (giriş, sayfa/yazı/kategori/medya/menü/ayar/mesaj yönetimi)
- [x] Genel site (anasayfa, blog, yazı, dinamik sayfa, iletişim formu)
- [x] WordPress içe aktarma komutu (`php artisan wp:import`)
- [ ] **Eski sitenin yedeği** (senin alman bekleniyor)
- [ ] Yedekteki gerçek içerik ve görsellerin aktarımı
- [ ] Eklenecek ek modüller
