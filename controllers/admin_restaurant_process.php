<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz istek.');
    redirect('../admin-restaurants.php');
}

$restaurantId = (int) ($_POST['restaurant_id'] ?? 0);
$status = $_POST['status'] ?? '';
$allowedStatuses = ['pending', 'approved', 'rejected', 'suspended'];

if ($restaurantId <= 0 || !in_array($status, $allowedStatuses, true)) {
    flash('danger', 'Restoran işlemi geçersiz.');
    redirect('../admin-restaurants.php');
}

try {
    $stmt = $pdo->prepare('UPDATE restaurants SET status = :status WHERE id = :id');
    $stmt->execute([
        ':status' => $status,
        ':id' => $restaurantId,
    ]);

    log_event('info', 'Admin restoran durumu güncelledi', ['restaurant_id' => $restaurantId, 'status' => $status]);
    flash('success', 'Restoran durumu güncellendi.');
} catch (Throwable $exception) {
    log_event('error', 'Admin restoran işlemi hatası', ['error' => $exception->getMessage()]);
    flash('danger', 'Restoran durumu güncellenemedi.');
}

redirect('../admin-restaurants.php');
