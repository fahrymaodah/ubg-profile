#!/usr/bin/env bash
# Deploy ubg-profile. Jalankan dari root project DI SERVER.
#
# CATATAN: public/build ikut di-commit, jadi server TIDAK perlu Node/npm.
# Yang wajib rebuild adalah mesin developer -- pakai ./build.sh sebelum commit.
set -euo pipefail

cd "$(dirname "$0")"

echo "==> 1. Ambil kode terbaru (termasuk public/build)"
git pull --ff-only origin main

echo "==> 2. Dependency PHP"
composer install --no-dev --optimize-autoloader

echo "==> 3. Migrasi"
php artisan migrate --force

echo "==> 4. Buang SEMUA cache lama sebelum di-cache ulang"
php artisan optimize:clear          # config + route + view + cache + compiled

echo "==> 5. Cache ulang"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
php artisan filament:cache-components

echo "==> 6. Permission"
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo
echo "==> Selesai. Asset yang sekarang aktif:"
php -r '$m=json_decode(file_get_contents("public/build/manifest.json"),true);
foreach($m as $k=>$v){ if(!empty($v["isEntry"])) printf("    %-45s -> %s\n",$k,$v["file"]); }'
echo
echo "Kalau tampilan masih lama, purge URL ini di Cloudflare:"
php -r '$m=json_decode(file_get_contents("public/build/manifest.json"),true);
foreach($m as $v){ if(!empty($v["isEntry"])) echo "    /build/".$v["file"]."\n"; }'
