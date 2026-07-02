<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    redirect('../views/become_partner.php');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz form oturumu.');
    redirect('../views/become_partner.php');
}

ensure_partner_applications_table();

$restaurantName = clean_input($_POST['restaurant_name'] ?? '');
$contactName = clean_input($_POST['contact_name'] ?? '');
$restaurantEmailRaw = trim((string) ($_POST['restaurant_email'] ?? ''));
$restaurantEmail = filter_var($restaurantEmailRaw, FILTER_VALIDATE_EMAIL);
$password = (string) ($_POST['password'] ?? '');
$phoneRaw = (string) ($_POST['phone'] ?? '');
$phone = normalize_tr_phone($phoneRaw);
$city = clean_input($_POST['city'] ?? '');
$district = clean_input($_POST['district'] ?? '');
$neighborhood = clean_input($_POST['neighborhood'] ?? '');
$address = clean_input($_POST['address'] ?? '');
$cuisineType = clean_input($_POST['cuisine_type'] ?? '');
$description = clean_input($_POST['description'] ?? '');
$openingTime = clean_input($_POST['opening_time'] ?? '');
$closingTime = clean_input($_POST['closing_time'] ?? '');
$imageUrlRaw = trim((string) ($_POST['image_url'] ?? ''));
$imageUrl = $imageUrlRaw !== '' ? filter_var($imageUrlRaw, FILTER_VALIDATE_URL) : '';

if (
    mb_strlen($restaurantName) < 2
    || mb_strlen($restaurantName) > 160
    || !is_valid_person_name($contactName, 2, 150)
    || !$restaurantEmail
    || strlen((string) $restaurantEmail) > 190
    || strlen($password) < 6
    || strlen($password) > 72
    || !$phone
    || mb_strlen($city) < 2
    || mb_strlen($city) > 80
    || mb_strlen($district) < 2
    || mb_strlen($district) > 80
    || mb_strlen($neighborhood) < 2
    || mb_strlen($neighborhood) > 120
    || mb_strlen($address) < 5
    || mb_strlen($address) > 255
    || mb_strlen($cuisineType) < 2
    || mb_strlen($cuisineType) > 80
    || mb_strlen($description) < 10
    || mb_strlen($description) > 1500
    || !is_valid_hhmm($openingTime)
    || !is_valid_hhmm($closingTime)
) {
    flash('danger', 'Lütfen tüm zorunlu alanları doğru şekilde doldurun.');
    redirect('../views/become_partner.php');
}

if ($imageUrlRaw !== '' && $imageUrl === false) {
    flash('danger', 'Geçersiz görsel URL girdiniz.');
    redirect('../views/become_partner.php');
}

if (strtotime($openingTime) >= strtotime($closingTime)) {
    flash('danger', 'Açılış saati kapanış saatinden once olmali.');
    redirect('../views/become_partner.php');
}

$restaurantEmail = strtolower((string) $restaurantEmail);

try {
    $duplicateStmt = $pdo->prepare(
        "SELECT id FROM restaurant_partner_applications
         WHERE restaurant_email = :restaurant_email AND status = 'pending'
         LIMIT 1"
    );
    $duplicateStmt->execute([':restaurant_email' => $restaurantEmail]);
    if ($duplicateStmt->fetch()) {
        flash('warning', 'Bu e-posta için bekleyen bir başvuru zaten bulunuyor.');
        redirect('../views/become_partner.php');
    }

    $stmt = $pdo->prepare(
        'INSERT INTO restaurant_partner_applications
        (restaurant_name, contact_name, restaurant_email, phone, city, district, neighborhood, address, cuisine_type, description, opening_time, closing_time, image_url, password_hash, status)
         VALUES
        (:restaurant_name, :contact_name, :restaurant_email, :phone, :city, :district, :neighborhood, :address, :cuisine_type, :description, :opening_time, :closing_time, :image_url, :password_hash, :status)'
    );
    $stmt->execute([
        ':restaurant_name' => $restaurantName,
        ':contact_name' => $contactName,
        ':restaurant_email' => $restaurantEmail,
        ':phone' => $phone,
        ':city' => $city,
        ':district' => $district,
        ':neighborhood' => $neighborhood,
        ':address' => $address,
        ':cuisine_type' => $cuisineType,
        ':description' => $description,
        ':opening_time' => $openingTime . ':00',
        ':closing_time' => $closingTime . ':00',
        ':image_url' => $imageUrl ?: null,
        ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ':status' => 'pending',
    ]);

    log_event('info', 'Partner restoran başvurusu oluşturuldu', [
        'restaurant_name' => $restaurantName,
        'restaurant_email' => $restaurantEmail,
    ]);
    flash('success', 'Başvurunuz alindi. Admin onayi sonrasi host hesabiniz aktif edilecektir.');
} catch (Throwable $exception) {
    log_event('error', 'Partner restoran başvuru hatası', ['error' => $exception->getMessage()]);
    flash('danger', 'Başvuru oluşturulamadı. Lütfen tekrar deneyin.');
}

redirect('../views/become_partner.php');
