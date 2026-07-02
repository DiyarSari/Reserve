<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role(['host']);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz istek.');
    redirect('../host-reservations.php');
}

$allowedStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
$status = $_POST['status'] ?? '';
$reservationId = (int) ($_POST['reservation_id'] ?? 0);

if (!in_array($status, $allowedStatuses, true) || $reservationId <= 0) {
    flash('danger', 'Rezervasyon durumu geçersiz.');
    redirect('../host-reservations.php');
}

try {
    auto_mark_no_show_reservations((string) current_user_email());
    $stmt = $pdo->prepare('UPDATE reservations SET status = :status WHERE id = :id AND owner_email = :owner_email');
    $stmt->execute([
        ':status' => $status,
        ':id' => $reservationId,
        ':owner_email' => current_user_email(),
    ]);

    log_event('info', 'Rezervasyon durumu güncellendi', ['reservation_id' => $reservationId, 'status' => $status]);
    flash('success', 'Rezervasyon durumu güncellendi.');
} catch (Throwable $exception) {
    log_event('error', 'Rezervasyon durumu hatası', ['error' => $exception->getMessage()]);
    flash('danger', 'Rezervasyon güncellenemedi.');
}

redirect('../host-reservations.php');
