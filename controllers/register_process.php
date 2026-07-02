<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    redirect('../views/register.php');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz form oturumu.');
    redirect('../views/register.php');
}

$fullName = clean_input($_POST['full_name'] ?? '');
$emailRaw = trim((string) ($_POST['email'] ?? ''));
$email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
$password = (string) ($_POST['password'] ?? '');
$role = 'user';

if (!is_valid_person_name($fullName, 3, 120) || !$email || strlen((string) $email) > 190 || strlen($password) < 6 || strlen($password) > 72) {
    flash('danger', 'Bilgileri kontrol edin. Ad soyad, e-posta veya şifre geçersiz.');
    redirect('../views/register.php');
}

$email = strtolower((string) $email);

try {
    $stmt = $pdo->prepare('INSERT INTO users (email, full_name, password_hash, role, created_date) VALUES (:email, :full_name, :password_hash, :role, NOW())');
    $stmt->execute([
        ':email' => $email,
        ':full_name' => $fullName,
        ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ':role' => $role,
    ]);

    log_event('info', 'Yeni kullanici kaydı', ['email' => $email, 'role' => $role]);
    flash('success', 'Kayıt basarili. Giriş yapabilirsiniz.');
    redirect('../views/login.php');
} catch (PDOException $exception) {
    log_event('error', 'Kayıt hatası', ['error' => $exception->getMessage(), 'email' => $email]);
    flash('danger', 'Bu e-posta kullanılıyor olabilir.');
    redirect('../views/register.php');
}
