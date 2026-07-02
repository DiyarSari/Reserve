<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role(['host']);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz istek.');
    redirect('../host-qr.php');
}

$token = clean_input($_POST['qr_token'] ?? '');

try {
    $updated = update_reservation_status_by_qr($token, (string) current_user_email(), 'completed');

    if (!$updated) {
        flash('warning', 'Rezervasyon bulunamadı, süresi dolmuş veya check-in için uygun değil.');
    } else {
        flash('success', 'Check-in tamamlandi. Ilgili masa dolu olarak takip edilebilir.');
        log_event('info', 'QR check-in tamamlandi', ['owner_email' => current_user_email()]);
    }
} catch (Throwable $exception) {
    log_event('error', 'Check-in hatası', ['error' => $exception->getMessage()]);
    flash('danger', 'Check-in işlemi tamamlanamadı.');
}

redirect('../views/host_qr_scanner.php?token=' . urlencode($token));
