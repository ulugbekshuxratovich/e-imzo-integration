# ⚡ Tez O'rnatish Qo'llanmasi

## 1. Repository'ni clone qiling

```bash
git clone https://github.com/yourusername/laravel-eimzo-integration.git
cd laravel-eimzo-integration
```

## 2. Dependencies o'rnatish

```bash
composer install
npm install
```

## 3. Environment sozlash

```bash
cp .env.example .env
php artisan key:generate
```

`.env` faylini tahrirlang:

```env
EIMZO_SERVER_URL=http://localhost:8080
EIMZO_FRONTEND_URL=https://yourdomain.uz
EIMZO_API_KEYS='localhost:YOUR_API_KEY_HERE'

REDIS_HOST=localhost
REDIS_PASSWORD=your_password
```

## 4. E-IMZO Server kalitlarini joylashtiring

```bash
# eimzo-server papkasiga jar faylni joylashtiring
mkdir -p eimzo-server eimzo-config/keys

# VPN kalitlar va config.properties ni joylashtiring
# (NIC'dan olingan)
```

## 5. Docker bilan ishga tushirish

```bash
docker-compose up -d
```

## 6. Database migration

```bash
php artisan migrate
```

## 7. Tekshirish

```bash
# E-IMZO Server
curl http://localhost:8080/ping

# Laravel
php artisan test
```

## 8. Frontend build

```bash
npm run build
# yoki development uchun
npm run dev
```

## ✅ Tayyor!

Server ishga tushdi:
- Laravel: http://localhost
- E-IMZO Server: http://localhost:8080

---

## Qo'shimcha sozlamalar

### Production uchun

1. HTTPS sozlash (majburiy!)
2. SSL sertifikat (Let's Encrypt)
3. Nginx konfiguratsiya
4. Redis password
5. Rate limiting
6. Logging

To'liq qo'llanma: [docs/](docs/)

### Troubleshooting

- E-IMZO serveri ishlamasa: `docker logs eimzo-server`
- Redis muammosi: `docker restart eimzo-redis`
- Permission error: `chmod -R 775 storage bootstrap/cache`

---

📚 **Ko'proq ma'lumot:** [README.md](../README.md)
