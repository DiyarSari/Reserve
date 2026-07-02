<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role(['user', 'host', 'admin']);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    redirect('../views/my_reservations.php');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz form oturumu.');
    redirect('../views/my_reservations.php');
}

$reservationId = (int) ($_POST['reservation_id'] ?? 0);
$restaurantId = (int) ($_POST['restaurant_id'] ?? 0);
$rating = (int) ($_POST['rating'] ?? 0);
$userEmail = (string) current_user_email();

if ($reservationId <= 0 || $restaurantId <= 0 || $rating < 1 || $rating > 5) {
    flash('danger', 'Geçerli bir puan secin.');
    redirect('../views/my_reservations.php');
}

try {
    ensure_restaurant_reviews_table();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        "SELECT * FROM reservations
         WHERE id = :id
           AND restaurant_id = :restaurant_id
           AND customer_email = :customer_email
         LIMIT 1"
    );
    $stmt->execute([
        ':id' => $reservationId,
        ':restaurant_id' => $restaurantId,
        ':customer_email' => $userEmail,
    ]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        throw new RuntimeException('Puanlanacak rezervasyon bulunamadı.');
    }

    if ((string) ($reservation['status'] ?? '') !== 'completed') {
        throw new RuntimeException('Sadece tamamlanan rezervasyonlar puanlanabilir.');
    }

    $insert = $pdo->prepare(
        "INSERT INTO restaurant_reviews
         (reservation_id, restaurant_id, user_email, rating, created_at)
         VALUES (:reservation_id, :restaurant_id, :user_email, :rating, NOW())"
    );
    $insert->execute([
        ':reservation_id' => $reservationId,
        ':restaurant_id' => $restaurantId,
        ':user_email' => $userEmail,
        ':rating' => $rating,
    ]);

    refresh_restaurant_rating($restaurantId);
    $pdo->commit();

    flash('success', 'Puaniniz kaydedildi. Tesekkur ederiz.');
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    log_event('error', 'Puanlama hatası', ['error' => $exception->getMessage()]);
    flash('danger', $exception->getMessage());
}

redirect('../views/my_reservations.php');
