# UBG Profile

Sistem Company Profile Multi-Tingkat untuk **Universitas Bumigora** dengan dukungan subdomain untuk Fakultas dan Program Studi.

## 🎯 Features

### Multi-Tenant Architecture
- **Universitas**: Domain utama (ubg.ac.id)
- **Fakultas**: Subdomain (teknik.ubg.ac.id, ekonomi.ubg.ac.id)
- **Prodi**: Subdomain (ilkom.ubg.ac.id, si.ubg.ac.id)

### Content Management
- 📰 **Berita/Artikel** - Dengan kategori, tag, SEO
- 👨‍🏫 **Dosen** - Profil lengkap dengan penelitian dan publikasi
- 🏆 **Prestasi** - Pencapaian mahasiswa dan dosen
- 📅 **Agenda/Event** - Calendar dengan registrasi
- 🖼️ **Galeri** - Foto dan video
- 📥 **Unduhan** - File dengan counter download
- 📢 **Pengumuman** - Dengan prioritas dan jadwal

### SEO & Performance
- ✅ Open Graph meta tags
- ✅ Twitter Cards
- ✅ Dynamic sitemap.xml per unit
- ✅ Dynamic robots.txt
- ✅ Caching (Menu, Settings, Articles)
- ✅ Lazy loading images

### Security
- ✅ CSRF protection
- ✅ Rate limiting (contact form)
- ✅ Input sanitization (XSS prevention)
- ✅ Honeypot anti-spam
- ✅ Role-based access control

## 🔧 Tech Stack

| Component | Technology |
|-----------|------------|
| Framework | Laravel 12 |
| Admin Panel | Filament 4 |
| Frontend | Blade + Tailwind CSS |
| Database | MySQL 8 |
| Cache | Redis / File |
| Web Server | Nginx |
| Container | Docker |

## 🚀 Quick Start

### Requirements
- Docker & Docker Compose
- Git

### Installation

```bash
# Clone repository
git clone https://github.com/ubg/ubg-profile.git
cd ubg-profile

# Copy environment
cp .env.example .env

# Start containers
docker-compose up -d

# Install dependencies
docker exec ubg-profile-app composer install
docker exec ubg-profile-app npm install && npm run build

# Setup application
docker exec ubg-profile-app php artisan key:generate
docker exec ubg-profile-app php artisan migrate --seed
docker exec ubg-profile-app php artisan storage:link
```

### Access Points

| Service | URL |
|---------|-----|
| Website | http://localhost:8080 |
| Admin Panel | http://localhost:8080/admin |
| phpMyAdmin | http://localhost:8081 |

### Default Credentials

**Admin Panel:**
- Email: `superadmin@ubg.ac.id`
- Password: `password`

**Database:**
- Host: `mysql` / `localhost:3306`
- Database: `ubg-profile`
- Username: `ubg_user`
- Password: `secret`

## 🏗️ Project Structure

```
ubg-profile/
├── app/
│   ├── Enums/             # PHP Enums (UserRole, UnitType, etc.)
│   ├── Filament/          # Admin panel resources, pages, widgets
│   ├── Http/
│   │   ├── Controllers/   # Frontend controllers
│   │   └── Middleware/    # ResolveUnit, CheckUnitPublished
│   ├── Models/            # Eloquent models (17 models)
│   ├── Policies/          # Authorization policies
│   ├── Services/          # Business logic (Menu, Setting, Theme, ArticleCache)
│   └── Traits/            # HasUnit, Sluggable, HasActivityLog
├── resources/
│   └── views/
│       ├── components/    # Blade components
│       ├── home/          # Homepage views per unit type
│       ├── articles/      # Article views
│       ├── dosen/         # Dosen views
│       └── layouts/       # App layout, navbar, footer
├── database/
│   ├── migrations/        # 17+ migrations
│   └── seeders/           # User, Fakultas/Prodi, Settings seeders
├── docs/                  # Documentation
│   ├── TODO.md            # Development progress
│   └── analysis.md        # Project analysis
└── docker/                # Docker configuration
```

## 🔀 Multi-Tenant Routing

### Development Mode
Gunakan query parameter untuk testing:
```
http://localhost:8080/?_unit=teknik      # Fakultas Teknik
http://localhost:8080/?_unit=ilkom       # Prodi Ilmu Komputer
```

### Production Mode
Setup wildcard DNS dan Nginx:
```nginx
server_name *.ubg.ac.id ubg.ac.id;
```

## 📦 Available Commands

### Docker
```bash
docker-compose up -d       # Start
docker-compose down        # Stop
docker-compose logs -f     # View logs
```

### Artisan
```bash
docker exec ubg-profile-app php artisan migrate
docker exec ubg-profile-app php artisan db:seed
docker exec ubg-profile-app php artisan cache:clear
docker exec ubg-profile-app php artisan optimize
```

### Development
```bash
docker exec ubg-profile-app npm run dev    # Watch mode
docker exec ubg-profile-app npm run build  # Production build
```

## 👥 User Roles

| Role | Access |
|------|--------|
| **Superadmin** | Full access to everything |
| **Universitas** | Manage all fakultas & prodi |
| **Fakultas** | Manage own fakultas & child prodi |
| **Prodi** | Manage own prodi only |

## 📖 Documentation

- [Development TODO](docs/TODO.md) - Progress tracking
- [Project Analysis](docs/analysis.md) - Technical analysis

## 🧪 Testing

```bash
# Run tests
docker exec ubg-profile-app php artisan test

# Test specific file
docker exec ubg-profile-app php artisan test --filter=HomeControllerTest
```

## 🛠️ Troubleshooting

### Permission Issues
```bash
docker exec ubg-profile-app chmod -R 775 storage bootstrap/cache
```

### Clear All Caches
```bash
docker exec ubg-profile-app php artisan optimize:clear
```

### Rebuild Assets
```bash
docker exec ubg-profile-app npm run build
docker exec ubg-profile-app php artisan filament:assets
```

## 📄 License

MIT License - Universitas Bumigora © 2026
