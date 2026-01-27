# 🚀 Panduan Deployment UBG Profile

Panduan deployment sistem profil fakultas/prodi UBG menggunakan aaPanel.

---

## 📋 Informasi Umum

| Item | Nilai |
|------|-------|
| Server | Cloud Kilat VPS (Ubuntu) |
| Control Panel | aaPanel |
| Web Server | Nginx |
| Database | MySQL 8.0+ |
| PHP | 8.2+ |
| Domain | ubg.ac.id |

### Subdomain yang Dikelola

| Subdomain | Keterangan |
|-----------|------------|
| `fihhp.ubg.ac.id` | Fakultas Ilmu Hukum, Humaniora, dan Pariwisata |
| `sasing.ubg.ac.id` | Prodi Sastra Inggris |
| `hukum.ubg.ac.id` | Prodi Ilmu Hukum |
| `pariwisata.ubg.ac.id` | Prodi Pariwisata |
| `fp.ubg.ac.id` | Fakultas Pendidikan |
| `pti.ubg.ac.id` | Prodi Pendidikan Teknologi Informasi |
| `pko.ubg.ac.id` | Prodi Pendidikan Kepelatihan Olahraga |

---

## 🔧 Langkah Deployment

### Step 1: Buat Website di aaPanel

1. Login ke **aaPanel**
2. Menu **Website** → **Add site**
3. Isi:
   - **Domain**: `fihhp.ubg.ac.id`
   - **Root directory**: `/www/wwwroot/ubg-profile`
   - **PHP Version**: 8.2
   - **Database**: Buat baru → `ubg_profile`

### Step 2: Clone Repository

```bash
cd /www/wwwroot
git clone https://github.com/[YOUR-REPO]/ubg-profile.git ubg-profile
cd ubg-profile
```

### Step 3: Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
```

### Step 4: Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME="Universitas Bumigora"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://fihhp.ubg.ac.id
APP_DOMAIN=ubg.ac.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ubg_profile
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

SESSION_DRIVER=database
SESSION_DOMAIN=.ubg.ac.id

CACHE_STORE=file
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

### Step 5: Setup Database

```bash
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder --force
php artisan storage:link
```

### Step 6: Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Step 7: Optimasi Cache

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
php artisan filament:cache-components
```

---

## 🌐 Konfigurasi Nginx

Di aaPanel: **Website** → pilih `ubg-profile` → **Config**

Ganti isi konfigurasi dengan:

```nginx
server {
    listen 80;
    listen 443 ssl http2;
    
    server_name 
        fihhp.ubg.ac.id
        sasing.ubg.ac.id
        hukum.ubg.ac.id
        pariwisata.ubg.ac.id
        fp.ubg.ac.id
        pti.ubg.ac.id
        pko.ubg.ac.id
    ;
    
    root /www/wwwroot/ubg-profile/public;
    index index.php index.html;

    # Redirect ke HTTPS
    if ($scheme = http) {
        return 301 https://$host$request_uri;
    }

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Gzip
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml;

    # Laravel Routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP
    location ~ \.php$ {
        fastcgi_pass unix:/tmp/php-cgi-82.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    # Cache Static Assets
    location ~* \.(jpg|jpeg|gif|png|webp|svg|ico|css|js|woff|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Block Sensitive Files
    location ~ /\. { deny all; }
    location ~ ^/(\.env|composer\.|package\.) { deny all; }

    # Logs
    access_log /www/wwwlogs/ubg-profile.log;
    error_log /www/wwwlogs/ubg-profile.error.log;
}
```

Reload Nginx:

```bash
nginx -t && systemctl reload nginx
```

---

## 🔐 SSL Certificate

### Via aaPanel (Recommended)

1. **Website** → pilih website → **SSL**
2. Pilih **Let's Encrypt**
3. Masukkan subdomain:
   ```
   fihhp.ubg.ac.id
   sasing.ubg.ac.id
   hukum.ubg.ac.id
   pariwisata.ubg.ac.id
   fp.ubg.ac.id
   pti.ubg.ac.id
   pko.ubg.ac.id
   ```
4. Klik **Apply**

### Atau via Certbot

```bash
certbot certonly --nginx \
  -d fihhp.ubg.ac.id \
  -d sasing.ubg.ac.id \
  -d hukum.ubg.ac.id \
  -d pariwisata.ubg.ac.id \
  -d fp.ubg.ac.id \
  -d pti.ubg.ac.id \
  -d pko.ubg.ac.id
```

---

## 🌍 DNS Records

Pastikan DNS A record mengarah ke IP server:

| Type | Name | Value |
|------|------|-------|
| A | fihhp | [IP Server] |
| A | sasing | [IP Server] |
| A | hukum | [IP Server] |
| A | pariwisata | [IP Server] |
| A | fp | [IP Server] |
| A | pti | [IP Server] |
| A | pko | [IP Server] |

> Jika sudah ada wildcard `*.ubg.ac.id`, tidak perlu tambah record.

---

## 👤 Ganti Password Admin

**WAJIB dilakukan setelah deployment!**

```bash
cd /www/wwwroot/ubg-profile
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::where('email', 'superadmin@ubg.ac.id')
    ->update(['password' => Hash::make('PASSWORD_BARU')]);

User::where('email', 'admin@ubg.ac.id')
    ->update(['password' => Hash::make('PASSWORD_BARU')]);

exit
```

### Akses Admin Panel

- **URL**: https://fihhp.ubg.ac.id/admin
- **Email**: `superadmin@ubg.ac.id`

---

## 🔄 Update Aplikasi

Ketika ada update dari repository:

```bash
cd /www/wwwroot/ubg-profile

git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
php artisan filament:cache-components

chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## ➕ Menambah Subdomain Baru

1. Tambah data Fakultas/Prodi via **Admin Panel**
2. Tambah subdomain di Nginx `server_name`
3. Update SSL certificate
4. Reload Nginx: `nginx -t && systemctl reload nginx`

---

## ✅ Checklist Sebelum Go-Live

- [ ] `APP_ENV=production` dan `APP_DEBUG=false`
- [ ] Password admin sudah diganti
- [ ] SSL aktif (HTTPS)
- [ ] Storage link sudah dibuat
- [ ] Permissions sudah benar

---

## 🐛 Troubleshooting

### Error 500

```bash
tail -f /www/wwwlogs/ubg-profile.error.log
tail -f /www/wwwroot/ubg-profile/storage/logs/laravel.log

chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Subdomain Tidak Berfungsi

1. Cek `server_name` di Nginx sudah include subdomain
2. Cek data fakultas/prodi ada di database
3. Clear cache: `php artisan config:clear`

### Gambar Tidak Muncul

```bash
php artisan storage:link
```

### Config Tidak Update

```bash
php artisan config:clear
php artisan cache:clear
```

---

**Terakhir diupdate**: Januari 2026
