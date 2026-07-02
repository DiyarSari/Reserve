<?php
$pageTitle = 'Giriş';
$mainClass = 'auth-page-main';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="auth-shell">
    <section class="auth-visual-panel">
        <div class="auth-visual-overlay">
            <h1 data-i18n="login.title">Reserve'e hoş geldiniz</h1>
            <p data-i18n="login.hero_text">Restoranları keşfet, menüleri incele ve rezervasyonlarını tek yerden yönet.</p>
        </div>
    </section>

    <section class="auth-form-panel">
        <div class="auth-form-card">
            <div class="auth-form-icon">
                <i class="bi bi-person-lock"></i>
            </div>
            <div class="text-center mb-4">
                <h2 class="mb-2" data-i18n="login.sign_in">Giriş Yap</h2>
                <p class="text-muted mb-0" data-i18n="login.form_text">Reserve'e tekrar hoş geldin.</p>
            </div>

            <form action="<?= e(BASE_URL) ?>/controllers/login_process.php" method="post" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <div class="mb-3">
                    <label for="email" class="form-label" data-i18n="login.email">Email</label>
                    <div class="auth-input-group">
                        <i class="bi bi-envelope"></i>
                        <input type="email" class="form-control" id="email" name="email" required maxlength="190" autocomplete="email" placeholder="name@example.com">
                    </div>
                    <div class="invalid-feedback" data-i18n="login.email_feedback">Geçerli bir e-posta girin.</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label" data-i18n="login.password">Şifre</label>
                    <div class="auth-input-group">
                        <i class="bi bi-lock"></i>
                        <input type="password" class="form-control" id="password" name="password" required maxlength="72" autocomplete="current-password" placeholder="••••••••">
                    </div>
                    <div class="invalid-feedback" data-i18n="login.password_feedback">Şifre zorunludur.</div>
                </div>
                <button type="submit" class="btn btn-primary auth-submit-btn w-100" data-i18n="login.submit">Giriş Yap</button>
            </form>

            <p class="auth-register-link text-center mt-4 mb-0">
                <span data-i18n="login.no_account">Hesabın yok mu?</span>
                <a href="<?= e(BASE_URL) ?>/views/register.php" data-i18n="login.create_account">Hesap Oluştur</a>
            </p>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
