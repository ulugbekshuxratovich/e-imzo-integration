# Contributing to Laravel E-IMZO Integration

Rahmat, siz bu proyektga qo'shilmoqchisiz! 🎉

## 📋 Qoidalar

### Code Style

- PSR-12 standard
- Laravel best practices
- SOLID principles
- Clean Code

### Commit Messages

```
type(scope): subject

body

footer
```

**Types:**
- `feat`: Yangi xususiyat
- `fix`: Bug fix
- `docs`: Dokumentatsiya
- `style`: Kod formatlash
- `refactor`: Code refactoring
- `test`: Testlar
- `chore`: Build yoki dependency

**Misol:**
```
feat(auth): add ID Card support

- Implemented ID Card authentication
- Added validation for PINFL
- Updated tests

Closes #123
```

### Pull Request Process

1. **Fork** qiling
2. **Branch** yarating: `git checkout -b feature/AmazingFeature`
3. **Commit** qiling: `git commit -m 'feat: Add AmazingFeature'`
4. **Test** o'tkazing: `php artisan test`
5. **Push** qiling: `git push origin feature/AmazingFeature`
6. **Pull Request** oching

### Code Review

PR yuborish oldidan:

- ✅ Barcha testlar o'tishi kerak
- ✅ Code style to'g'ri bo'lishi kerak
- ✅ Dokumentatsiya yangilangan bo'lishi kerak
- ✅ Breaking changes haqida yozilgan bo'lishi kerak

### Testing

```bash
# Unit tests
php artisan test --testsuite=Unit

# Feature tests  
php artisan test --testsuite=Feature

# Coverage
php artisan test --coverage --min=80
```

### Documentation

- README.md yangilang
- PHPDoc qo'shing
- JSDoc qo'shing
- API documentation yangilang

## 🐛 Bug Report

Issue ochganda quyidagilarni kiriting:

- Laravel versiya
- PHP versiya
- E-IMZO Server versiya
- Error message
- Steps to reproduce
- Expected behavior
- Actual behavior
- Screenshots (agar kerak bo'lsa)

## 💡 Feature Request

- Qanday muammo hal qiladi
- Qanday ishlashi kerak
- Alternative yechimlar
- Qo'shimcha kontekst

## ❓ Savollar

Agar savol bo'lsa:

1. Avval [docs/](docs/) ni o'qing
2. [Issues](../../issues) da qidiring
3. Yangi issue oching

## 📞 Contact

- Email: shuxratovichulugbek0@gmail.uz
- Telegram: @luubeck

---

**Rahmat sizning hissangiz uchun! 🙏**
