#!/usr/bin/env bash
# WordPress SQL dokumunu MariaDB'ye import eder.
# Kullanim: bash docs/mariadb-kur.sh /yol/site.sql
set -euo pipefail

SQL="${1:-}"
if [ -z "$SQL" ] || [ ! -f "$SQL" ]; then
  echo "Kullanim: bash docs/mariadb-kur.sh /yol/site.sql"
  exit 1
fi

DB="analizdegerleme"
DB_USER="laravel"
DB_PASS="laravel_secret"

# MariaDB kurulu degilse kur (Debian/Ubuntu)
if ! command -v mariadbd >/dev/null 2>&1 && ! command -v mysqld >/dev/null 2>&1; then
  echo ">> MariaDB kuruluyor..."
  sudo apt-get update -q && sudo apt-get install -y -q mariadb-server
fi

# Veri dizinini hazirla ve sunucuyu baslat (systemd yoksa elle)
sudo mkdir -p /var/lib/mysql /run/mysqld
sudo chown -R mysql:mysql /var/lib/mysql /run/mysqld
if [ ! -d /var/lib/mysql/mysql ]; then
  sudo mariadb-install-db --user=mysql --datadir=/var/lib/mysql >/dev/null
fi
if ! pgrep -x mariadbd >/dev/null && ! pgrep -x mysqld >/dev/null; then
  sudo mariadbd --user=mysql >/tmp/mariadbd.log 2>&1 &
  sleep 6
fi

echo ">> Veritabani ve kullanici olusturuluyor..."
sudo mariadb -e "
CREATE DATABASE IF NOT EXISTS \`$DB\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'127.0.0.1' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB\`.* TO '$DB_USER'@'127.0.0.1';
FLUSH PRIVILEGES;"

echo ">> Dokum import ediliyor ($SQL)..."
sudo mariadb "$DB" < "$SQL"

echo ">> Tamam. Tablolar:"
sudo mariadb "$DB" -e "SELECT COUNT(*) AS tablo FROM information_schema.tables WHERE table_schema='$DB';"
echo ">> Simdi laravel-app/.env icinde MariaDB ayarlarini gir ve: php artisan wp:import-db"
