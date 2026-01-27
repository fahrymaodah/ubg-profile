# UBG Profile Website

Sistem website profil untuk Fakultas dan Program Studi Universitas Bumigora.

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

## Installation

```bash
# Clone repository
git clone https://github.com/fahrymaodah/ubg-profile.git
cd ubg-profile

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env then run
php artisan migrate --seed
php artisan storage:link

# Build assets
npm run build
```

## Development

```bash
npm run dev
```

## Production

Lihat [DEPLOYMENT-AAPANEL.md](DEPLOYMENT-AAPANEL.md) untuk panduan deployment.

## License

MIT
