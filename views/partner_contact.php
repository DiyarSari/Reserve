<?php
$pageTitle = 'Partner İletişim';
$activePage = 'partner';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="admin-hero mb-4">
    <div>
        <span class="section-eyebrow">Partner İletişim</span>
        <h1>İletişime Geç</h1>
        <p>Partnerlik, başvuru süreci veya onay adımlarıyla ilgili sorularını bize iletebilirsin.</p>
    </div>
    <a href="<?= e(BASE_URL) ?>/views/become_partner.php" class="btn btn-outline-primary">
        <i class="bi bi-ui-checks-grid me-2"></i>Partner Formu
    </a>
</section>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card host-panel-card h-100">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Mesaj Formu</h2>
                <form action="<?= e(BASE_URL) ?>/controllers/partner_contact_process.php" method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="full_name">Ad Soyad</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required minlength="2" maxlength="120" autocomplete="name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email">E-posta</label>
                            <input type="email" class="form-control" id="email" name="email" required maxlength="190" autocomplete="email" placeholder="name@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="phone">Telefon</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required minlength="10" maxlength="13" pattern="(\+90|0)?[1-9][0-9]{9}" data-validate-phone-tr="1" inputmode="tel" placeholder="+905551112233" autocomplete="tel">
                            <div class="invalid-feedback" data-i18n="validation.phone_required">Telefon alanı zorunludur.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="subject">Konu</label>
                            <input type="text" class="form-control" id="subject" name="subject" required minlength="3" maxlength="160">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="message">Mesaj</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required minlength="10" maxlength="1000"></textarea>
                        </div>
                    </div>
                    <div class="mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Mesajı Gönder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card host-panel-card h-100">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Partner Ekibi</h2>
                <div class="d-flex flex-column gap-2 text-muted">
                    <span><i class="bi bi-envelope me-2"></i>partner@reserve.local</span>
                    <span><i class="bi bi-telephone me-2"></i>+90 212 000 00 00</span>
                    <span><i class="bi bi-geo-alt me-2"></i>İstanbul, Türkiye</span>
                </div>
                <hr>
                <p class="mb-0 text-muted">Dilersen önce partner detay sayfasından süreci adım adım inceleyebilirsin.</p>
                <a href="<?= e(BASE_URL) ?>/views/partner_info.php" class="btn btn-outline-primary btn-sm mt-3">Detaylı Bilgi Sayfası</a>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
