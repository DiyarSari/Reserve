<?php
$pageTitle = 'Kayıt';
$mainClass = 'auth-page-main';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="auth-shell">
    <section class="auth-visual-panel auth-register-visual">
        <div class="auth-visual-overlay">
            <h1 data-i18n="register.hero_title">Reserve ailesine katıl</h1>
            <p data-i18n="register.hero_text">Favori restoranlarını kaydet, menüleri incele ve rezervasyonlarını kolayca yönet.</p>
        </div>
    </section>

    <section class="auth-form-panel">
        <div class="auth-form-card">
            <div class="auth-form-icon">
                <i class="bi bi-person-plus"></i>
            </div>
            <div class="text-center mb-4">
                <h1 class="mb-2" data-i18n="register.title">Hesap oluştur</h1>
                <p class="text-muted mb-0" data-i18n="register.subtitle">Müşteri hesabı ile başla. Restoran sahibi olmak için girişten sonra başvuru yapabilirsin.</p>
            </div>

                <form action="<?= e(BASE_URL) ?>/controllers/register_process.php" method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <div class="mb-3">
                        <label for="full_name" class="form-label" data-i18n="register.full_name">Ad Soyad</label>
                        <div class="auth-input-group">
                            <i class="bi bi-person"></i>
                            <input type="text" class="form-control" id="full_name" name="full_name" required minlength="3" maxlength="120" autocomplete="name">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label" data-i18n="register.email">E-posta</label>
                        <div class="auth-input-group">
                            <i class="bi bi-envelope"></i>
                            <input type="email" class="form-control" id="email" name="email" required maxlength="190" autocomplete="email">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label" data-i18n="register.password">Şifre</label>
                        <div class="auth-input-group">
                            <i class="bi bi-lock"></i>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6" maxlength="72" autocomplete="new-password">
                        </div>
                        <div class="form-text" data-i18n="register.password_hint">En az 6 karakter kullan.</div>
                    </div>
                    <button type="submit" class="btn btn-primary auth-submit-btn w-100" data-i18n="register.submit">Kayıt Ol</button>
                </form>

                <p class="auth-register-link text-center mt-4 mb-0">
                    <span data-i18n="register.have_account">Zaten hesabın var mı?</span>
                    <a href="<?= e(BASE_URL) ?>/views/login.php" data-i18n="register.login">Giriş Yap</a>
                </p>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
