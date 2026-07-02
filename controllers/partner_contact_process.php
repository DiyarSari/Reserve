<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    redirect('../views/partner_contact.php');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz form oturumu.');
    redirect('../views/partner_contact.php');
}

$fullName = clean_input($_POST['full_name'] ?? '');
$emailRaw = trim((string) ($_POST['email'] ?? ''));
$email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
$phoneRaw = clean_input($_POST['phone'] ?? '');
$subject = clean_input($_POST['subject'] ?? '');
$message = clean_input($_POST['message'] ?? '');
$phone = normalize_tr_phone($phoneRaw);

if (
    !is_valid_person_name($fullName, 2, 120)
    || !$email
    || strlen((string) $email) > 190
    || mb_strlen($subject) < 3
    || mb_strlen($subject) > 160
    || mb_strlen($message) < 10
    || mb_strlen($message) > 1000
    || $phone === ''
) {
    flash('danger', 'İletişim formundaki bilgileri kontrol edin.');
    redirect('../views/partner_contact.php');
}

$email = strtolower((string) $email);

try {
    log_event('info', 'Partner iletişim formu gonderildi', [
        'full_name' => $fullName,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
    ]);

    flash('success', 'Mesajin alindi. Partner ekibi en kisa surede donus yapacak.');
} catch (Throwable $exception) {
    log_event('error', 'Partner iletişim formu hatası', ['error' => $exception->getMessage()]);
    flash('danger', 'Mesaj gönderilirken hata oluştu.');
}

redirect('../views/partner_contact.php');
