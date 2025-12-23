# 📁 Project Structure

```
laravel-eimzo-integration/
│
├── app/
│   ├── Exceptions/
│   │   └── EimzoException.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── EimzoAuthController.php
│   │   │       └── EimzoDocumentController.php
│   │   ├── Middleware/
│   │   │   └── EimzoRateLimiter.php
│   │   └── Requests/
│   │       └── EimzoLoginRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   └── SignedDocument.php
│   ├── Services/
│   │   ├── EimzoService.php
│   │   ├── EimzoAuthService.php
│   │   └── EimzoDocumentService.php
│   └── Providers/
│       └── EimzoServiceProvider.php
│
├── config/
│   ├── app.php
│   ├── services.php
│   └── logging.php
│
├── database/
│   ├── migrations/
│   │   ├── 2024_12_23_000001_add_eimzo_fields_to_users_table.php
│   │   └── 2024_12_23_000002_create_signed_documents_table.php
│   └── seeders/
│
├── docker/
│   ├── Dockerfile
│   ├── docker-compose.yml
│   ├── nginx/
│   │   └── conf.d/
│   │       └── default.conf
│   └── php/
│       └── local.ini
│
├── docs/
│   ├── installation.md
│   ├── api-reference.md
│   ├── frontend-integration.md
│   ├── security.md
│   └── troubleshooting.md
│
├── eimzo-server/
│   └── e-imzo-server.jar
│
├── eimzo-config/
│   ├── config.properties
│   └── keys/
│       ├── your-domain.key
│       ├── vpn.jks
│       └── truststore.jks
│
├── resources/
│   ├── js/
│   │   ├── components/
│   │   │   ├── EimzoAuth.vue
│   │   │   └── EimzoDocumentSign.vue
│   │   ├── lib/
│   │   │   └── eimzo-client.js
│   │   └── app.js
│   └── views/
│
├── routes/
│   ├── api.php
│   └── web.php
│
├── tests/
│   ├── Feature/
│   │   ├── EimzoAuthTest.php
│   │   └── EimzoDocumentTest.php
│   └── Unit/
│       ├── EimzoServiceTest.php
│       └── EimzoAuthServiceTest.php
│
├── .env.example
├── .gitignore
├── composer.json
├── package.json
├── README.md
├── LICENSE
├── CONTRIBUTING.md
└── TELEGRAM_POST.md
```

## 📂 Papka tushuntirishlari

### `app/Services/`
**E-IMZO asosiy business logika**
- `EimzoService.php` - E-IMZO Server bilan API aloqa
- `EimzoAuthService.php` - Autentifikatsiya logikasi
- `EimzoDocumentService.php` - Hujjat imzolash va tekshirish

### `app/Http/Controllers/Api/`
**RESTful API Controllers**
- Clean kod
- Single Responsibility
- Dependency Injection

### `resources/js/`
**Frontend**
- Vue 3 components
- Modern ES6+ JavaScript
- Reusable library

### `docker/`
**Docker konfiguratsiya**
- Development environment
- Production-ready
- Nginx, MySQL, Redis

### `eimzo-server/`
**E-IMZO Server JAR**
- Java application
- VPN connection

### `eimzo-config/`
**E-IMZO Server config**
- Properties file
- VPN keys (NIC'dan)

### `docs/`
**Dokumentatsiya**
- Installation
- API Reference  
- Security Guide
- Troubleshooting

### `tests/`
**Automated Tests**
- Unit tests
- Feature tests
- Integration tests

## 🔧 Konfiguratsiya fayllari

- `.env` - Environment variables
- `config/services.php` - E-IMZO config
- `config/logging.php` - Logging setup
- `docker-compose.yml` - Docker services

## 📝 Dokumentatsiya fayllari

- `README.md` - Asosiy dokumentatsiya
- `CONTRIBUTING.md` - Qo'shilish qoidalari
- `LICENSE` - MIT License
- `TELEGRAM_POST.md` - Telegram uchun post
