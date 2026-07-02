    </main>
    <footer class="reserve-footer-clean" id="siteFooter">
        <div class="container">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <h2 class="reserve-footer-heading">Ekibimize Katılın</h2>
                    <a class="btn btn-outline-light btn-sm footer-partner-btn mt-2 mb-3" href="<?= e(BASE_URL) ?>/views/become_partner.php">
                        <i class="bi bi-shop-window"></i>
                        <span data-i18n="footer.become_partner">Partner Ol</span>
                    </a>
                    <ul class="reserve-footer-list">
                        <li><a href="<?= e(BASE_URL) ?>/views/partner_info.php">Partner Bilgilendirme</a></li>
                        <li><a href="<?= e(BASE_URL) ?>/views/partner_contact.php">İletişime Geç</a></li>
                    </ul>
                </div>
                <div class="col">
                    <h2 class="reserve-footer-heading" data-i18n="footer.discover">Keşfet</h2>
                    <ul class="reserve-footer-list">
                        <li><a href="<?= e(BASE_URL) ?>/views/restaurants.php" data-i18n="footer.discover_restaurants">Restoranları Keşfet</a></li>
                        <li><a href="<?= e(BASE_URL) ?>/views/restaurants.php" data-i18n="footer.view_menus">Menüleri İncele</a></li>
                    </ul>
                </div>
                <div class="col">
                    <h2 class="reserve-footer-heading" data-i18n="footer.contact">İletişim</h2>
                    <div class="reserve-footer-contact" aria-label="Contact information">
                        <span><i class="bi bi-envelope"></i> support@reserve.local</span>
                        <span><i class="bi bi-telephone"></i> +90 212 000 00 00</span>
                        <span><i class="bi bi-geo-alt"></i> İstanbul, Türkiye</span>
                    </div>
                </div>
            </div>

            <div class="reserve-footer-bottom-clean">
                <span data-i18n="footer.copyright" data-i18n-vars='{"year":"2026"}'>© 2026 Reserve. Tüm hakları saklıdır.</span>
            </div>
        </div>
    </footer>
    <div class="cookie-consent" id="cookieConsentBanner" role="dialog" aria-live="polite" aria-label="Cookie Consent">
        <div class="cookie-consent-content">
            <div class="cookie-consent-body">
                <span class="cookie-consent-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></span>
                <div class="cookie-consent-copy">
                    <strong data-i18n="cookie.title">Çerez tercihlerin</strong>
                    <p data-i18n="cookie.message">Bu sitede deneyimini iyileştirmek için çerezler kullanıyoruz. Devam ederek çerez politikasını kabul etmiş olursun.</p>
                </div>
            </div>
            <div class="cookie-consent-actions">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="cookieEssentialBtn" data-i18n="cookie.essential">Gerekli Çerezlerle Devam Et</button>
                <button type="button" class="btn btn-primary btn-sm" id="cookieAcceptBtn" data-i18n="cookie.accept">Tümünü Kabul Et</button>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php $mainScriptVersion = filemtime(__DIR__ . '/../assets/js/main.js') ?: time(); ?>
    <script src="<?= e(BASE_URL) ?>/assets/js/main.js?v=<?= e((string) $mainScriptVersion) ?>"></script>
    <?php if (!empty($extraScripts)): ?>
        <?php foreach ($extraScripts as $script): ?>
            <?php
            $scriptSrc = $script;
            if (str_starts_with($script, '../assets/') || str_starts_with($script, 'assets/')) {
                $scriptSrc = BASE_URL . '/' . str_replace('../', '', $script);
            }
            $scriptVersion = '';
            if (str_starts_with($script, '../assets/') || str_starts_with($script, 'assets/')) {
                $scriptPath = __DIR__ . '/../' . str_replace('../', '', $script);
                if (is_file($scriptPath)) {
                    $scriptVersion = '?v=' . (string) (filemtime($scriptPath) ?: time());
                }
            }
            ?>
            <script src="<?= e($scriptSrc . $scriptVersion) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
