<?php
$pageTitle = 'Rezervasyon Başarılı';
require_once __DIR__ . '/../includes/header.php';

$code = clean_input($_GET['code'] ?? '');
$stmt = $pdo->prepare('SELECT * FROM reservations WHERE reservation_code = :code LIMIT 1');
$stmt->execute([':code' => $code]);
$reservation = $stmt->fetch();

if (!$reservation) {
    http_response_code(404);
    exit('Rezervasyon bulunamadı.');
}
?>

<section class="reservation-success-page">
    <div class="reservation-success-card">
        <div class="reservation-success-head">
            <span class="reservation-success-icon"><i class="bi bi-check2"></i></span>
            <span class="eyebrow dark">Rezervasyon onaylandı</span>
            <h1>Masanı ayırdık.</h1>
            <p>Restorana geldiğinde QR kodunu veya rezervasyon kodunu göstermen yeterli.</p>
        </div>

        <div class="reservation-success-grid">
            <div class="reservation-code-panel">
                <span class="reservation-code-label">Rezervasyon Kodu</span>
                <strong><?= e($reservation['reservation_code']) ?></strong>
                <p><?= e($reservation['restaurant_name']) ?></p>
            </div>

            <div class="reservation-qr-panel">
                <img src="<?= e(qr_code_url((string) $reservation['qr_token'], 180)) ?>" alt="Rezervasyon QR kodu" class="qr-image">
                <span>QR check-in için hazır</span>
                <code><?= e(format_qr_token_for_display((string) $reservation['qr_token'])) ?></code>
            </div>
        </div>

        <dl class="reservation-summary-list">
            <div>
                <dt>Tarih</dt>
                <dd><?= e($reservation['reservation_date']) ?></dd>
            </div>
            <div>
                <dt>Saat</dt>
                <dd><?= e(substr($reservation['reservation_time'], 0, 5)) ?></dd>
            </div>
            <div>
                <dt>Kişi</dt>
                <dd><?= (int) $reservation['guest_count'] ?></dd>
            </div>
            <div>
                <dt>Masa</dt>
                <dd><?= e($reservation['table_number']) ?></dd>
            </div>
        </dl>

        <div class="reservation-success-actions">
            <a class="btn btn-primary" href="<?= e(BASE_URL) ?>/views/my_reservations.php">Rezervasyonlarım</a>
            <a class="btn btn-outline-primary" href="<?= e(BASE_URL) ?>/views/restaurants.php">Restoranlara Dön</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
