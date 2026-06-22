# 🔴 ACİL: Hosting Bitmeden Yedek Al

**Neden ben (Claude) yapamıyorum?** Bu oturum bir bulut konteynerinde çalışıyor ve
ağ politikası `analizdegerleme.com` adresine erişimi engelliyor (`403 host_not_allowed`).
Ayrıca senin yerel bilgisayarında değilim ve FTP bilgilerini de görmüyorum. Bu yüzden
**yedeği bugün senin kendi bilgisayarından alman gerekiyor.** Aşağıdaki komutları
sırayla çalıştır — 15-20 dakikada biter.

Üç parça var, üçünü de al:
1. **Veritabanı** (en kritik — tüm yazılar, sayfalar, ayarlar burada)
2. **FTP dosyaları** (tema, eklentiler, `wp-content/uploads` görselleri)
3. **Genel HTML kopyası** (dışarıdan görünen hali — yedeğin yedeği)

---

## 1) VERİTABANI YEDEĞİ (önce bunu yap!)

### Yol A — Hosting paneli / phpMyAdmin (en kolay)
1. Hosting cPanel/Plesk paneline gir → **phpMyAdmin**.
2. Soldan WordPress veritabanını seç (adını `wp-config.php` içindeki `DB_NAME`'den görebilirsin).
3. Üstten **Export (Dışa Aktar)** → Method: **Custom** → Format: **SQL** →
   "Add DROP TABLE" işaretle → **Go**. İnen `.sql` dosyasını sakla.

### Yol B — Komut satırı (SSH varsa)
```bash
mysqldump -h SUNUCU_HOST -u DB_KULLANICI -p DB_ADI \
  --single-transaction --default-character-set=utf8mb4 \
  > analizdegerleme_db_$(date +%Y%m%d).sql
```

`wp-config.php` içinde şu satırları bul (FTP ile indirip aç):
```php
define('DB_NAME', '...');      // DB_ADI
define('DB_USER', '...');      // DB_KULLANICI
define('DB_PASSWORD', '...');  // şifre
define('DB_HOST', '...');      // SUNUCU_HOST (genelde localhost)
```

---

## 2) FTP İLE TÜM DOSYALARI İNDİR

`lftp` en hızlısı (Mac: `brew install lftp`, Ubuntu: `sudo apt install lftp`,
Windows: WSL veya FileZilla kullan).

```bash
# Bilgileri kendi FTP'ne göre doldur:
FTP_HOST="ftp.analizdegerleme.com"
FTP_USER="ftp_kullanici_adin"
FTP_PASS="ftp_sifren"

mkdir -p analizdegerleme_ftp_yedek
lftp -u "$FTP_USER","$FTP_PASS" "$FTP_HOST" <<EOF
set ftp:ssl-allow no
mirror --verbose --parallel=5 / ./analizdegerleme_ftp_yedek
bye
EOF
```

> FileZilla kullanacaksan: bağlan → sağ panelde kök dizine git (genelde
> `public_html` veya `httpdocs`) → hepsini seç → sol panele (bilgisayarına) sürükle.

**En önemli klasör:** `wp-content/uploads` (tüm görseller burada),
`wp-content/themes`, `wp-content/plugins` ve kökteki `wp-config.php`.

---

## 3) DIŞARIDAN GÖRÜNEN SİTENİN HTML KOPYASI

Bu, sayfaların gerçek görünümünü (HTML + CSS + görsel) statik olarak kaydeder.

```bash
wget \
  --mirror \
  --convert-links \
  --adjust-extension \
  --page-requisites \
  --no-parent \
  --restrict-file-names=windows \
  -e robots=off \
  --user-agent="Mozilla/5.0" \
  --wait=0.3 \
  -P analizdegerleme_html_yedek \
  https://analizdegerleme.com/
```

Alternatif (daha eksiksiz tarama): **HTTrack** (`httrack https://analizdegerleme.com/ -O ./yedek`).

---

## 4) HEPSİNİ BU REPOYA YÜKLE (ben devralayım)

Yedekleri aldıktan sonra bana ulaştırmanın en hızlı yolu — bu GitHub deposuna koymak:

```bash
git clone <bu-repo-url> tutorial
cd tutorial
git checkout claude/wordpress-laravel-migration-6h74gp

# Veritabanı dökümünü ve uploads görsellerini koy:
cp /path/analizdegerleme_db_*.sql            site-backup/database/
cp -r /path/analizdegerleme_ftp_yedek/wp-content/uploads/*  site-backup/uploads/
cp -r /path/analizdegerleme_html_yedek/*     site-backup/html/

git add site-backup
git commit -m "site yedegi eklendi"
git push
```

> ⚠️ `.sql` ve `uploads` büyükse (>100MB) GitHub reddedebilir. O durumda bana haber ver,
> sadece `.sql` dökümünü ve `wp-config.php`'yi koymanı isteyeceğim — gerisini ona göre planlarız.

---

Yedeği koyduğunda **veritabanındaki içerikten** Laravel sitesini ve admin panelini
otomatik dolduracağım. İskelet hazır bekliyor (`laravel-app/`).
