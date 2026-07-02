<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role(['host']);
ensure_restaurant_location_columns();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz istek.');
    redirect('../host-dashboard.php');
}

try {
    $name = clean_input($_POST['name'] ?? '');
    $description = clean_input($_POST['description'] ?? '');
    $cuisineType = clean_input($_POST['cuisine_type'] ?? '');
    $city = clean_input($_POST['city'] ?? '');
    $district = clean_input($_POST['district'] ?? '');
    $neighborhood = clean_input($_POST['neighborhood'] ?? '');
    $address = clean_input($_POST['address'] ?? '');
    $phoneRaw = (string) ($_POST['phone'] ?? '');
    $phone = normalize_tr_phone($phoneRaw);
    $openingTime = clean_input($_POST['opening_time'] ?? '09:00');
    $closingTime = clean_input($_POST['closing_time'] ?? '22:00');
    $duration = (int) ($_POST['reservation_duration_minutes'] ?? 90);

    if (
        mb_strlen($name) < 2 || mb_strlen($name) > 160
        || mb_strlen($description) < 10 || mb_strlen($description) > 1000
        || mb_strlen($cuisineType) < 2 || mb_strlen($cuisineType) > 80
        || mb_strlen($city) < 2 || mb_strlen($city) > 80
        || mb_strlen($district) < 2 || mb_strlen($district) > 80
        || mb_strlen($neighborhood) < 2 || mb_strlen($neighborhood) > 120
        || mb_strlen($address) < 5 || mb_strlen($address) > 255
        || !is_valid_hhmm($openingTime)
        || !is_valid_hhmm($closingTime)
        || $duration < 30 || $duration > 300
    ) {
        throw new RuntimeException('Restoran formundaki alanları doğru formatta doldurun.');
    }

    if ($phone === '') {
        throw new RuntimeException('Telefon alanı Türkiye formatina uygun olmali.');
    }

    if (strtotime($openingTime) >= strtotime($closingTime)) {
        throw new RuntimeException('Açılış saati kapanış saatinden once olmali.');
    }

    $stmt = $pdo->prepare(
        'UPDATE restaurants SET name = :name, description = :description, cuisine_type = :cuisine_type, city = :city, district = :district, neighborhood = :neighborhood, address = :address, phone = :phone, opening_time = :opening_time, closing_time = :closing_time, reservation_duration_minutes = :duration WHERE owner_email = :owner_email'
    );
    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':cuisine_type' => $cuisineType,
        ':city' => $city,
        ':district' => $district,
        ':neighborhood' => $neighborhood,
        ':address' => $address,
        ':phone' => $phone,
        ':opening_time' => $openingTime,
        ':closing_time' => $closingTime,
        ':duration' => $duration,
        ':owner_email' => current_user_email(),
    ]);

    flash('success', 'Restoran bilgileri güncellendi.');
} catch (Throwable $exception) {
    log_event('error', 'Restoran profil güncelleme hatası', ['error' => $exception->getMessage()]);
    flash('danger', 'Restoran bilgileri güncellenemedi.');
}

redirect('../host-dashboard.php');
