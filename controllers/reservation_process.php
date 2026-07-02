<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    redirect('../views/restaurants.php');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz form oturumu.');
    redirect('../views/restaurants.php');
}

$restaurantId = (int) ($_POST['restaurant_id'] ?? 0);
$customerName = clean_input($_POST['customer_name'] ?? '');
$customerEmailRaw = trim((string) ($_POST['customer_email'] ?? ''));
$customerEmail = filter_var($customerEmailRaw, FILTER_VALIDATE_EMAIL);
$customerPhoneRaw = (string) ($_POST['customer_phone'] ?? '');
$customerPhone = normalize_tr_phone($customerPhoneRaw);
$guestCount = (int) ($_POST['guest_count'] ?? 0);
$reservationDate = clean_input($_POST['reservation_date'] ?? '');
$reservationTime = clean_input($_POST['reservation_time'] ?? '');
$notes = clean_input($_POST['notes'] ?? '');

if ($restaurantId <= 0 || $customerName === '' || !$customerEmail || $customerPhone === '' || $guestCount <= 0 || $reservationDate === '' || $reservationTime === '') {
    flash('danger', 'Rezervasyon formundaki zorunlu alanları doldurun.');
    redirect('../views/restaurant_detail.php?id=' . $restaurantId);
}

if (!is_valid_person_name($customerName, 3, 150)) {
    flash('danger', 'Ad soyad 3 ile 150 karakter arasında olmalıdır.');
    redirect('../views/restaurant_detail.php?id=' . $restaurantId);
}

if (!$customerPhone) {
    flash('danger', 'Geçerli bir telefon numarası girin.');
    redirect('../views/restaurant_detail.php?id=' . $restaurantId);
}

if (!$customerEmail || strlen((string) $customerEmail) > 190) {
    flash('danger', 'Geçerli bir e-posta girin.');
    redirect('../views/restaurant_detail.php?id=' . $restaurantId);
}

if (mb_strlen($notes) > 500) {
    flash('danger', 'Rezervasyon notu en fazla 500 karakter olabilir.');
    redirect('../views/restaurant_detail.php?id=' . $restaurantId);
}

if ($guestCount < 1 || $guestCount > 10) {
    flash('danger', 'Kişi sayısı 1 ile 10 arasında olmalıdır.');
    redirect('../views/restaurant_detail.php?id=' . $restaurantId);
}

$dateObject = DateTime::createFromFormat('Y-m-d', $reservationDate);
$timeObject = DateTime::createFromFormat('H:i', $reservationTime) ?: DateTime::createFromFormat('H:i:s', $reservationTime);

if (!$dateObject || $dateObject->format('Y-m-d') !== $reservationDate || !$timeObject) {
    flash('danger', 'Rezervasyon tarihi veya saati geçersiz.');
    redirect('../views/restaurant_detail.php?id=' . $restaurantId);
}

$today = new DateTime('today');
if ($dateObject < $today) {
    flash('danger', 'Geçmiş tarihe rezervasyon yapılamaz.');
    redirect('../views/restaurant_detail.php?id=' . $restaurantId);
}

$maxReservationDate = (new DateTime('today'))->modify('+60 days');
if ($dateObject > $maxReservationDate) {
    flash('danger', 'En fazla 60 gün sonrasina rezervasyon yapabilirsiniz.');
    redirect('../views/restaurant_detail.php?id=' . $restaurantId);
}

    $reservationDateTime = new DateTime($reservationDate . ' ' . substr($reservationTime, 0, 5));
if ($reservationDateTime <= new DateTime()) {
    flash('danger', 'Rezervasyon saati ileri bir zaman olmalıdır.');
    redirect('../views/restaurant_detail.php?id=' . $restaurantId);
}

    $selectedTimeParts = explode(':', substr($reservationTime, 0, 5));
$selectedMinutes = ((int) $selectedTimeParts[0] * 60) + (int) $selectedTimeParts[1];
if ($selectedMinutes % 30 !== 0) {
    flash('danger', 'Rezervasyon saati 30 dakikalık aralıklarla seçilmelidir.');
    redirect('../views/restaurant_detail.php?id=' . $restaurantId);
}

try {
    $pdo->beginTransaction();

    $restaurant = get_restaurant($restaurantId);
    if (!$restaurant || $restaurant['status'] !== 'approved') {
        throw new RuntimeException('Restoran rezervasyona uygun değil.');
    }

    $opening = substr((string) $restaurant['opening_time'], 0, 5);
    $closing = substr((string) $restaurant['closing_time'], 0, 5);
    $selectedTime = substr($reservationTime, 0, 5);

    if ($selectedTime < $opening || $selectedTime > $closing) {
        throw new RuntimeException('Rezervasyon saati restoran çalışma saatleri dışında.');
    }

    $durationMinutes = (int) ($restaurant['reservation_duration_minutes'] ?? 90);
    $durationMinutes = max(30, min(300, $durationMinutes));
    $reservationEndTime = (clone $reservationDateTime)->modify('+' . $durationMinutes . ' minutes');
    $closingDateTime = new DateTime($reservationDate . ' ' . $closing);

    if ($reservationEndTime > $closingDateTime) {
        throw new RuntimeException('Seçtiğiniz saat kapanış saatini aşıyor. Lütfen daha erken bir saat seçin.');
    }

    $table = find_table_for_reservation($restaurantId, $guestCount, $reservationDate, substr($reservationTime, 0, 5), $durationMinutes);
    if (!$table) {
        throw new RuntimeException('Bu saat aralığında uygun masa bulunamadı.');
    }

    $code = generate_reservation_code();
    $qrToken = generate_qr_token();

    $stmt = $pdo->prepare(
        "INSERT INTO reservations
         (reservation_code, restaurant_id, restaurant_name, table_id, table_number, customer_name, customer_email, customer_phone, guest_count, reservation_date, reservation_time, status, qr_token, notes, owner_email)
         VALUES
         (:reservation_code, :restaurant_id, :restaurant_name, :table_id, :table_number, :customer_name, :customer_email, :customer_phone, :guest_count, :reservation_date, :reservation_time, 'pending', :qr_token, :notes, :owner_email)"
    );
    $stmt->execute([
        ':reservation_code' => $code,
        ':restaurant_id' => $restaurantId,
        ':restaurant_name' => $restaurant['name'],
        ':table_id' => (int) $table['id'],
        ':table_number' => $table['table_number'],
        ':customer_name' => $customerName,
        ':customer_email' => strtolower((string) $customerEmail),
        ':customer_phone' => $customerPhone,
        ':guest_count' => $guestCount,
        ':reservation_date' => $reservationDate,
        ':reservation_time' => $reservationTime,
        ':qr_token' => $qrToken,
        ':notes' => $notes,
        ':owner_email' => $restaurant['owner_email'],
    ]);

    $pdo->prepare('UPDATE restaurants SET total_reservations = total_reservations + 1 WHERE id = :id')->execute([':id' => $restaurantId]);
    $pdo->commit();

    log_event('info', 'Rezervasyon oluşturuldu', ['code' => $code, 'email' => $customerEmail]);
    redirect('../views/reservation_success.php?code=' . urlencode($code));
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    log_event('error', 'Rezervasyon hatası', ['error' => $exception->getMessage()]);
    flash('danger', $exception->getMessage());
    redirect('../views/restaurant_detail.php?id=' . $restaurantId);
}
