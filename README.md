# Reserve

Reserve, restoran keşfi ve masa rezervasyonunu tek akışta birleştiren PHP ve MySQL tabanlı bir rezervasyon platformudur. Sistem; kullanıcılar, restoran hostları ve yöneticiler için ayrı çalışma alanları sunar. Bilingual arayüz, QR tabanlı check-in ve rol bazlı yönetim panelleriyle günlük operasyonları sadeleştirir.

## 📸 Project Images

<p><img width="480" alt="ChatGPT Image 2 Tem 2026 19_37_57" src="https://github.com/user-attachments/assets/5388736f-498e-4c18-99c9-97db080a74d9" /></p>
<p><img width="280" alt="WhatsApp Image 2026-07-02 at 19 32 43" src="https://github.com/user-attachments/assets/6c82cbd2-0327-483c-8931-238dcd343715" /></p>
<p><img width="480" alt="Screenshot 2026-07-02 at 19 27 50" src="https://github.com/user-attachments/assets/b2ee6234-fd40-40f8-abfc-54b29f599b7f" /></p>
<p><img width="480" alt="Screenshot 2026-07-02 at 19 28 16" src="https://github.com/user-attachments/assets/9b73d1e2-7bcd-404a-a9d2-102e726db027" /></p>
<p><img width="480" alt="Screenshot 2026-07-02 at 19 28 44" src="https://github.com/user-attachments/assets/bcbc79b0-4799-437e-90e8-04fc1b157ec3" /></p>
<p><img width="480" alt="Screenshot 2026-07-02 at 19 29 23" src="https://github.com/user-attachments/assets/ead6d0be-65e1-497f-a9f6-5a44a34008da" /></p>



## Features

- Kullanıcı kayıt ve giriş akışı
- Rol tabanlı erişim: user, host ve admin panelleri
- Restoran listeleme, arama, filtreleme ve sayfalama
- Restoran detay sayfası ve menü görüntüleme
- Rezervasyon oluşturma ve uygun masa eşleştirme
- Rezervasyon geçmişi ve değerlendirme akışı
- Host tarafında restoran profili, masa, menü ve günlük rezervasyon yönetimi
- QR tabanlı check-in ve rezervasyon doğrulama
- Admin tarafında restoran moderasyonu, kullanıcı yönetimi ve sistem logları
- Partner başvuru ve iletişim akışları
- Türkçe ve İngilizce arayüz desteği
- CSRF korumalı formlar ve sunucu tarafı doğrulama

## Project Architecture

- Frontend/UI: PHP ile render edilen sayfalar, Bootstrap tabanlı arayüz ve Vanilla JavaScript ile desteklenen etkileşimler bu katmanda yer alır. Ziyaretçi, kullanıcı, host ve admin deneyimleri ayrı ekranlar üzerinden sunulur.
- Backend / Controller Layer: Form gönderimleri ve işlem akışları controller dosyaları üzerinden yönetilir. Giriş, kayıt, rezervasyon, QR doğrulama, partner başvurusu ve yönetim aksiyonları burada işlenir.
- Authentication & Access Control: Oturum tabanlı kimlik doğrulama, rol kontrolü ve CSRF token doğrulaması hassas işlemleri korur. Admin ve host alanları yalnızca yetkili kullanıcılar tarafından erişilebilir.
- Database Layer: MariaDB veritabanı kullanıcılar, restoranlar, rezervasyonlar, masalar, menüler, değerlendirmeler ve log kayıtlarını saklar. Şema, platformun ana iş süreçlerini ilişkisel olarak modeller.
- Localization Layer: TR/EN metinleri JSON tabanlı yerelleştirme dosyaları üzerinden sağlanır. Bu yapı, arayüz metinlerinin merkezi ve tutarlı kalmasını destekler.
- Static Asset Layer: CSS, JS ve görseller kullanıcı deneyimini, form doğrulamasını ve QR tarayıcı akışını destekler. Tüm stil ve istemci tarafı davranışlar bu katmanda toplanır.

## Tech Stack

| Layer | Technology | Usage |
|---|---|---|
| Frontend | PHP, Bootstrap, Bootstrap Icons, Vanilla JavaScript | Server-rendered UI, responsive layouts, and client-side interactions |
| Backend | PHP 8+, PDO | Request handling, validation, session flow, and database access |
| Database | MySQL / MariaDB | Persistent storage for reservations, users, restaurants, menus, and logs |
| Security | CSRF tokens, prepared statements, password hashing, role checks | Form protection, safe data access, and access control |
| Localization | JSON-based i18n | Turkish and English interface text |
| Runtime | Apache via XAMPP | Local development and application hosting |

## Project Structure

```text
admin-*.php, host-*.php   Role-specific dashboard entry points.
config/                   Database connection and application settings.
controllers/              Form submissions and protected actions.
includes/                 Shared auth, layout, and helper bootstrap.
views/                    Public pages and role-based screens.
assets/                   Styles, scripts, and static media.
database/                 Schema and seed scripts.
locales/                  TR/EN interface strings.
```

## Installation

1. Projeyi XAMPP `htdocs` dizinine yerleştirin.
2. Apache ve MySQL servislerini başlatın.
3. Veritabanını oluşturun ve sırasıyla şema ile seed dosyalarını içe aktarın.
4. Gerekirse veritabanı bağlantı ayarlarını [config/db.php](config/db.php) içinde güncelleyin.
5. Uygulamayı tarayıcıda açın.

```bash
cd /Applications/XAMPP/htdocs/Reserve
/Applications/XAMPP/xamppfiles/bin/mysql -u root -h 127.0.0.1 -P 3307 Reserve < database/schema.sql
/Applications/XAMPP/xamppfiles/bin/mysql -u root -h 127.0.0.1 -P 3307 Reserve < database/seed.sql
open http://localhost/Reserve/
```

## Environment Variables

Bu proje mevcut durumda veritabanı bağlantısını [config/db.php](config/db.php) içinde tanımlar. Aşağıdaki değerler, yapılandırmayı `.env` tabanlı bir modele taşımak isterseniz kullanılacak alanları gösterir.

| Variable | Description | Example |
|---|---|---|
| DB_HOST | Database host | `127.0.0.1` |
| DB_PORT | Database port | `3307` |
| DB_NAME | Database name | `Reserve` |
| DB_USER | Database user | `root` |
| DB_PASS | Database password | `********` |
| APP_TIMEZONE | Application timezone | `Europe/Istanbul` |
| BASE_URL | Application base path | `/Reserve` |

## API Overview

Bu projede bağımsız bir public REST API bulunmuyor. Uygulama, form tabanlı controller endpoint'leri üzerinden çalışır.

- Authentication: login, register, logout ve sosyal giriş denemeleri
- Reservations: rezervasyon oluşturma, durum güncelleme, QR doğrulama ve puanlama
- Host Actions: restoran profili, masa yönetimi, menü yönetimi ve QR check-in
- Admin Actions: restoran moderasyonu, kullanıcı yönetimi, rezervasyon ve log takibi
- Partner Flows: partner başvuru ve iletişim formları

## AI Workflow

> To be updated. Current codebase does not include an active AI chatbot or model integration.

## Security

- CSRF token korumalı form gönderimleri
- Prepared statement tabanlı veritabanı erişimi
- Oturum yeniden oluşturma ile giriş sonrası session hardening
- Rol bazlı erişim kontrolü
- Sunucu tarafı alan doğrulama ve veri temizleme
- Şifrelerin hash'lenerek saklanması
- Sistem olaylarının loglanması

## Future Improvements

- `.env` tabanlı konfigürasyon katmanı
- Ayrı bir REST API katmanı
- Otomatik test kapsamının artırılması
- Rezervasyon uygunluk ve çakışma kurallarının genişletilmesi
- Bildirim akışları için e-posta veya anlık bildirim desteği
- QR check-in deneyimi için daha akıcı mobil kullanım
- Yönetim paneli için gelişmiş raporlama ve analiz ekranları

## License

MIT License
