<?php
$pageTitle = 'Partner Ol';
$activePage = 'partner';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="admin-hero mb-4">
    <div>
        <span class="section-eyebrow">Partner Programı</span>
        <h1>Reserve ile Partner Ol</h1>
        <p>Restoranını kaydet, admin onayı sonrası host panelinle rezervasyonlarını ve menünü yönetmeye başla.</p>
    </div>
    <a href="<?= e(BASE_URL) ?>/views/login.php" class="btn btn-outline-primary">
        <i class="bi bi-box-arrow-in-right me-2"></i>Host girişi
    </a>
</section>

<section class="card host-panel-card">
    <div class="card-body p-4 p-lg-5">
        <div class="row g-4">
            <div class="col-lg-8">
                <h2 class="h4 mb-2">Restoran Başvuru Formu</h2>
                <p class="text-muted mb-4">Formu doldurduktan sonra başvurun admin panelinde incelemeye alınacaktır.</p>
            </div>
        </div>
        <form action="<?= e(BASE_URL) ?>/controllers/partner_application_process.php" method="post" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="restaurant_name" class="form-label">Restoran adı</label>
                    <input type="text" class="form-control" id="restaurant_name" name="restaurant_name" required minlength="2" maxlength="160" autocomplete="organization">
                </div>
                <div class="col-md-6">
                    <label for="contact_name" class="form-label">Yetkili / iletişim adı</label>
                    <input type="text" class="form-control" id="contact_name" name="contact_name" required minlength="2" maxlength="150" autocomplete="name">
                </div>
                <div class="col-md-6">
                    <label for="restaurant_email" class="form-label">Restoran e-postası</label>
                    <input type="email" class="form-control" id="restaurant_email" name="restaurant_email" required maxlength="190" autocomplete="email" placeholder="ornek@restoran.com">
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">Host giriş şifresi</label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="6" maxlength="72" autocomplete="new-password">
                    <div class="form-text">Onaylandığında bu e-posta ve şifre ile Host Paneline giriş yapacaksın.</div>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Telefon</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required minlength="10" maxlength="13" pattern="(\+90|0)?[1-9][0-9]{9}" inputmode="tel" data-validate-phone-tr="1" placeholder="+905551112233" autocomplete="tel">
                    <div class="invalid-feedback" data-i18n="validation.phone_required">Telefon alanı zorunludur.</div>
                </div>
                <div class="col-md-6">
                    <label for="city" class="form-label">Şehir</label>
                    <input type="text" class="form-control" id="city" name="city" required minlength="2" maxlength="80" autocomplete="address-level2">
                </div>
                <div class="col-md-6">
                    <label for="district" class="form-label">İlçe</label>
                    <input type="text" class="form-control" id="district" name="district" required minlength="2" maxlength="80" autocomplete="address-level2">
                </div>
                <div class="col-md-6">
                    <label for="neighborhood" class="form-label">Mahalle</label>
                    <input type="text" class="form-control" id="neighborhood" name="neighborhood" required minlength="2" maxlength="120" autocomplete="address-level3">
                </div>
                <div class="col-12">
                    <label for="address" class="form-label">Adres</label>
                    <input type="text" class="form-control" id="address" name="address" required minlength="5" maxlength="255" autocomplete="street-address">
                </div>
                <div class="col-md-6">
                    <label for="cuisine_type" class="form-label">Mutfak türü</label>
                    <input type="text" class="form-control" id="cuisine_type" name="cuisine_type" required minlength="2" maxlength="80" placeholder="Akdeniz, Asya, Brunch...">
                </div>
                <div class="col-md-3">
                    <label for="opening_time" class="form-label">Açılış saati</label>
                    <input type="time" class="form-control" id="opening_time" name="opening_time" value="09:00" step="60" required>
                </div>
                <div class="col-md-3">
                    <label for="closing_time" class="form-label">Kapanış saati</label>
                    <input type="time" class="form-control" id="closing_time" name="closing_time" value="22:00" step="60" required>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Açıklama</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required minlength="10" maxlength="1500" placeholder="Mekanın konsepti, servis tarzı ve öne çıkan deneyimleri."></textarea>
                </div>
                <div class="col-12">
                    <label for="image_url" class="form-label">Görsel URL (opsiyonel)</label>
                    <input type="url" class="form-control" id="image_url" name="image_url" placeholder="https://...">
                </div>
            </div>
            <div class="d-flex flex-column flex-md-row gap-2 justify-content-md-end mt-4">
                <a href="<?= e(BASE_URL) ?>/views/home.php#siteFooter" class="btn btn-outline-secondary">Vazgeç</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-2"></i>Başvuruyu Gönder
                </button>
            </div>
        </form>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
