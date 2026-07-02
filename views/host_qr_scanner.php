<?php
$pageTitle = 'QR Tarayıcı';
$activePage = 'host';
$activeTab = 'qr';
$allowStaffPublicAccess = true;
$extraScripts = ['../assets/js/qr-scanner.js'];
require_once __DIR__ . '/../includes/auth.php';
require_role(['host']);
require_once __DIR__ . '/../includes/header.php';

$token = clean_input($_GET['token'] ?? '');
$validation = null;
$reservation = null;

if ($token !== '') {
    $validation = validate_host_qr_token($token, (string) current_user_email());
    if ($validation['valid']) {
        $updatedReservation = confirm_host_qr_reservation($token, (string) current_user_email());
        if ($updatedReservation) {
            $reservation = $updatedReservation;
            $validation = [
                'valid' => true,
                'message' => 'Rezervasyon doğrulandı.',
                'reservation' => $updatedReservation,
            ];
        } else {
            $reservation = $validation['reservation'];
        }
    } else {
        $reservation = $validation['reservation'];
    }
}
?>
<div class="dashboard-hero">
    <div>
        <span class="eyebrow dark" data-i18n="host.qr_verification">QR Doğrulama</span>
        <h1 data-i18n="host.scan_qr_title">Rezervasyon QR Tara</h1>
        <p data-i18n="host.qr_page_text">Misafirin QR kodunu tara veya token bilgisini manuel girerek rezervasyonu doğrula.</p>
    </div>
    <a href="<?= e(BASE_URL) ?>/host-qr.php" class="btn btn-outline-primary" data-i18n="host.back_dashboard">Panele dön</a>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h2 class="h5" data-i18n="host.camera_scanner">Kamera tarayıcı</h2>
                <p class="text-muted" data-i18n="host.camera_scanner_text">Kamera erişimine izin ver. Tarayıcı QR okumayı desteklemiyorsa manuel token girişi kullanabilirsin.</p>
                <div class="qr-scanner-frame">
                    <video id="qrScannerVideo" muted playsinline></video>
                    <div class="scanner-placeholder" id="qrScannerStatus" data-i18n="host.scanner_ready">Tarayıcı hazır.</div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" type="button" id="startQrScanner" data-i18n="host.open_scanner">Tarayıcıyı Aç</button>
                    <button class="btn btn-outline-secondary" type="button" id="stopQrScanner" data-i18n="host.stop">Durdur</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h5" data-i18n="host.manual_verification">Manuel doğrulama</h2>
                <form method="get" class="needs-validation" novalidate id="qrTokenForm">
                    <label for="qrTokenInput" class="form-label">QR Token</label>
                    <input class="form-control" id="qrTokenInput" name="token" value="<?= e($token) ?>" required minlength="8" maxlength="64" pattern="[A-Za-z0-9-]{8,64}" placeholder="AB3D-9K2M-P7QX" autocomplete="off" spellcheck="false">
                    <div class="form-text mb-3">Kısa token formatı: AB3D-9K2M-P7QX</div>
                    <button class="btn btn-primary w-100" data-i18n="host.verify_reservation">Rezervasyonu Doğrula</button>
                </form>
            </div>
        </div>

        <?php if ($validation): ?>
            <div class="alert <?= $validation['valid'] ? 'alert-success' : 'alert-danger' ?> mb-3">
                <?= e($validation['message']) ?>
            </div>
        <?php endif; ?>

        <?php if ($reservation): ?>
            <div class="card qr-checkin-result">
                <div class="card-body">
                    <div class="qr-checkin-head">
                        <div>
                            <span class="section-eyebrow mb-2 d-inline-block" data-i18n="host.reservation_details">Rezervasyon detayları</span>
                            <h2 class="h5 mb-1"><?= e($reservation['restaurant_name']) ?></h2>
                            <p class="text-muted mb-0"><?= e($reservation['customer_name']) ?></p>
                        </div>
                        <span class="badge reservation-status-badge" data-status="<?= e($reservation['status']) ?>" data-i18n="status.<?= e($reservation['status']) ?>">
                            <?= e($reservation['status']) ?>
                        </span>
                    </div>

                    <div class="qr-checkin-highlight mt-3 mb-3">
                        <i class="bi bi-grid-3x3-gap"></i>
                        <div>
                            <small data-i18n="host.table">Masa</small>
                            <strong>#<?= e((string) $reservation['table_number']) ?></strong>
                        </div>
                    </div>

                    <div class="row g-2 qr-checkin-meta mb-3">
                        <div class="col-sm-6">
                            <div class="qr-meta-chip">
                                <i class="bi bi-calendar-event"></i>
                                <span><?= e($reservation['reservation_date']) ?> <?= e(substr((string) $reservation['reservation_time'], 0, 5)) ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="qr-meta-chip">
                                <i class="bi bi-people"></i>
                                <span><?= (int) $reservation['guest_count'] ?> kişi</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="qr-meta-chip">
                                <i class="bi bi-upc-scan"></i>
                                <span><?= e(format_qr_token_for_display((string) $reservation['qr_token'])) ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if ($validation && $validation['valid']): ?>
                        <div class="d-flex flex-wrap gap-2">
                            <form action="<?= e(BASE_URL) ?>/controllers/qr_status_process.php" method="post">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="qr_token" value="<?= e($reservation['qr_token']) ?>">
                                <input type="hidden" name="action" value="completed">
                                <button class="btn btn-dark" data-i18n="host.mark_completed">Tamamlandı Olarak İşaretle</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
