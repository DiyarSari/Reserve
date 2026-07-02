<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role(['host']);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz istek.');
    redirect('../views/host_qr_scanner.php');
}

$token = clean_input($_POST['qr_token'] ?? '');
$action = $_POST['action'] ?? '';
$status = $action === 'completed' ? 'completed' : '';

if ($token === '' || $status === '') {
    flash('danger', 'QR işlemi geçersiz.');
    redirect('../views/host_qr_scanner.php');
}

try {
    $updated = update_reservation_status_by_qr($token, (string) current_user_email(), $status);

    if (!$updated) {
        flash('danger', 'Rezervasyon durumu güncellenemedi. QR geçersiz, süresi dolmuş veya kullanılmış olabilir.');
    } else {
        log_event('info', 'QR rezervasyon durumu güncellendi', [
            'owner_email' => current_user_email(),
            'status' => $status,
        ]);
        flash('success', 'Rezervasyon tamamlandı olarak işaretlendi.');
    }
} catch (Throwable $exception) {
    log_event('error', 'QR durum güncelleme hatası', ['error' => $exception->getMessage()]);
    flash('danger', 'QR işlemi tamamlanamadı.');
}

redirect('../views/host_qr_scanner.php?token=' . urlencode($token));
