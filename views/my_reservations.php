<?php
$pageTitle = 'Rezervasyonlarım';
$activePage = 'reservations';
require_once __DIR__ . '/../includes/auth.php';
require_role(['user']);
require_once __DIR__ . '/../includes/header.php';

$reservations = get_user_reservations((string) current_user_email());
$today = date('Y-m-d');
$totalReservations = count($reservations);
$upcomingReservations = 0;
$completedReservations = 0;

foreach ($reservations as $reservationRow) {
    $status = (string) ($reservationRow['status'] ?? '');
    $reservationDate = (string) ($reservationRow['reservation_date'] ?? '');
    if (in_array($status, ['pending', 'confirmed'], true) && $reservationDate >= $today) {
        $upcomingReservations++;
    }
    if ($status === 'completed') {
        $completedReservations++;
    }
}

$statusLabels = [
    'pending' => 'Bekliyor',
    'confirmed' => 'Onaylandı',
    'checked_in' => 'Tamamlandı',
    'completed' => 'Tamamlandı',
    'cancelled' => 'İptal Edildi',
    'no_show' => 'Gelmedi',
];
?>

<section class="my-reservations-hero">
    <div class="my-reservations-hero-copy">
        <span class="section-eyebrow">Hesap Özeti</span>
        <h1>Rezervasyonlarım</h1>
        <p>Aktif rezervasyonlarını takip et, geçmiş deneyimlerini puanla ve QR koduna tek yerden eriş.</p>
    </div>
    <a href="<?= e(BASE_URL) ?>/views/restaurants.php" class="btn btn-primary">Yeni Rezervasyon</a>
</section>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <article class="my-reservations-stat">
            <span>Toplam Rezervasyon</span>
            <strong><?= $totalReservations ?></strong>
        </article>
    </div>
    <div class="col-sm-6 col-xl-4">
        <article class="my-reservations-stat">
            <span>Yaklaşan Rezervasyon</span>
            <strong><?= $upcomingReservations ?></strong>
        </article>
    </div>
    <div class="col-sm-6 col-xl-4">
        <article class="my-reservations-stat">
            <span>Tamamlanan Rezervasyon</span>
            <strong><?= $completedReservations ?></strong>
        </article>
    </div>
</div>

<?php if (empty($reservations)): ?>
    <section class="my-reservations-empty">
        <i class="bi bi-calendar2-x"></i>
        <h2>Henüz rezervasyon yok</h2>
        <p>Restoranları keşfederek ilk rezervasyonunu hemen oluşturabilirsin.</p>
        <a href="<?= e(BASE_URL) ?>/views/restaurants.php" class="btn btn-primary">Restoranları Keşfet</a>
    </section>
<?php else: ?>
    <section class="my-reservations-grid">
        <?php foreach ($reservations as $reservation): ?>
            <?php
            $review = get_reservation_review((int) $reservation['id'], (string) current_user_email());
            $restaurant = get_restaurant((int) $reservation['restaurant_id']);
            $imageUrl = $restaurant ? restaurant_image_url($restaurant) : restaurant_image_fallback_url([
                'name' => (string) ($reservation['restaurant_name'] ?? 'Reserve'),
                'city' => '',
                'cuisine_type' => '',
            ]);
            $status = (string) ($reservation['status'] ?? '');
            $statusLabel = $statusLabels[$status] ?? $status;
            ?>
            <article class="my-reservation-card">
                <div class="my-reservation-main">
                    <div class="my-reservation-top">
                        <img src="<?= e($imageUrl) ?>" alt="<?= e($reservation['restaurant_name']) ?>" class="my-reservation-thumb" loading="lazy">
                        <div>
                            <span class="badge reservation-status-badge" data-status="<?= e($status) ?>"><?= e($statusLabel) ?></span>
                            <h2><?= e($reservation['restaurant_name']) ?></h2>
                            <p class="my-reservation-subtitle">
                                <?= e(date('d.m.Y', strtotime((string) $reservation['reservation_date']))) ?>
                                •
                                <?= e(substr((string) $reservation['reservation_time'], 0, 5)) ?>
                                •
                                <?= (int) $reservation['guest_count'] ?> Kişi
                            </p>
                        </div>
                    </div>

                    <div class="my-reservation-meta">
                        <span><strong>Kod:</strong> <?= e($reservation['reservation_code']) ?></span>
                        <span><strong>Masa:</strong> <?= e((string) $reservation['table_number']) ?></span>
                    </div>

                    <?php if (!empty($reservation['notes'])): ?>
                        <p class="my-reservation-note"><strong>Not:</strong> <?= e((string) $reservation['notes']) ?></p>
                    <?php endif; ?>

                    <div class="my-reservation-actions">
                        <a href="<?= e(BASE_URL) ?>/views/restaurant_detail.php?id=<?= (int) $reservation['restaurant_id'] ?>" class="btn btn-outline-primary btn-sm">Restoranı Gör</a>
                    </div>

                    <?php if ($review): ?>
                        <div class="reservation-rating-box mt-3">
                            <div class="rating-saved">
                                <span class="rating-mini-stars" aria-label="Saved rating">
                                    <?php for ($star = 1; $star <= 5; $star++): ?>
                                        <i class="bi <?= $star <= (int) $review['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                    <?php endfor; ?>
                                </span>
                                <strong><?= (int) $review['rating'] ?>/5</strong>
                                <span>Puanladın.</span>
                            </div>
                        </div>
                    <?php elseif ($status === 'completed'): ?>
                        <div class="reservation-rating-box mt-3">
                            <form action="<?= e(BASE_URL) ?>/controllers/review_process.php" method="post" class="reservation-rating-form needs-validation" novalidate>
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="reservation_id" value="<?= (int) $reservation['id'] ?>">
                                <input type="hidden" name="restaurant_id" value="<?= (int) $reservation['restaurant_id'] ?>">
                                <div class="rating-form-copy">
                                    <strong>Deneyimini Puanla</strong>
                                    <span>Bu restoranın puanına katkı sağla.</span>
                                </div>
                                <div class="star-rating-input" aria-label="Restoranı puanla">
                                    <?php for ($star = 5; $star >= 1; $star--): ?>
                                        <input type="radio" id="rating_<?= (int) $reservation['id'] ?>_<?= $star ?>" name="rating" value="<?= $star ?>" required>
                                        <label for="rating_<?= (int) $reservation['id'] ?>_<?= $star ?>" title="<?= $star ?>/5"><i class="bi bi-star-fill"></i></label>
                                    <?php endfor; ?>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary">Puanla</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <aside class="my-reservation-qr">
                    <img src="<?= e(qr_code_url((string) $reservation['qr_token'], 170)) ?>" alt="Reservation QR code" class="qr-image">
                    <code><?= e(format_qr_token_for_display((string) $reservation['qr_token'])) ?></code>
                </aside>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
