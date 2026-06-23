#!/usr/bin/env bash
# analizdegerleme.com - tek komutla yedek alma scripti
# Kullanim:  bash yedek-al.sh
# Bilgileri asagida doldur, sonra calistir. Mac/Linux/WSL uzerinde calisir.
set -euo pipefail

# ===== DOLDUR =====
SITE_URL="https://analizdegerleme.com/"
FTP_HOST="ftp.analizdegerleme.com"
FTP_USER="FTP_KULLANICI"
FTP_PASS="FTP_SIFRE"
# (Veritabanini phpMyAdmin'den manuel almak daha guvenli; SSH yoksa bu kismi atla)
DB_HOST=""        # ornek: localhost  (bos birakirsan DB adimi atlanir)
DB_NAME=""
DB_USER=""
DB_PASS=""
# ==================

STAMP=$(date +%Y%m%d_%H%M%S)
OUT="analizdegerleme_yedek_$STAMP"
mkdir -p "$OUT"/{db,ftp,html}
echo ">> Yedek klasoru: $OUT"

echo ">> [1/3] HTML kopyasi aliniyor..."
wget --mirror --convert-links --adjust-extension --page-requisites --no-parent \
     --restrict-file-names=windows -e robots=off --user-agent="Mozilla/5.0" \
     --wait=0.3 -P "$OUT/html" "$SITE_URL" || echo "!! HTML adimi kismen basarisiz olabilir"

echo ">> [2/3] FTP dosyalari indiriliyor..."
if command -v lftp >/dev/null 2>&1; then
  lftp -u "$FTP_USER","$FTP_PASS" "$FTP_HOST" <<EOF || echo "!! FTP adimi basarisiz - bilgileri kontrol et"
set ftp:ssl-allow no
mirror --verbose --parallel=5 / "$OUT/ftp"
bye
EOF
else
  echo "!! lftp yok. Kur: (mac) brew install lftp / (ubuntu) sudo apt install lftp / ya da FileZilla kullan"
fi

echo ">> [3/3] Veritabani aliniyor..."
if [ -n "$DB_NAME" ]; then
  mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" \
    --single-transaction --default-character-set=utf8mb4 \
    > "$OUT/db/${DB_NAME}_$STAMP.sql" && echo ">> DB yedegi tamam" \
    || echo "!! DB adimi basarisiz - phpMyAdmin'den manuel al"
else
  echo ".. DB bilgisi girilmedi, atlandi. phpMyAdmin > Export ile manuel al."
fi

echo ""
echo ">> BITTI. Klasor: $OUT"
echo ">> Onemli: $OUT/ftp/wp-content/uploads (gorseller) ve $OUT/db/*.sql sakla."
