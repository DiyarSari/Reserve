<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    redirect('../views/login.php');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz form oturumu.');
    redirect('../views/login.php');
}

$emailRaw = trim((string) ($_POST['email'] ?? ''));
$email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
$password = (string) ($_POST['password'] ?? '');

if (!$email || strlen((string) $email) > 190 || $password === '' || strlen($password) > 72) {
    flash('danger', 'E-posta ve şifre zorunludur.');
    redirect('../views/login.php');
}

$email = strtolower((string) $email);

try {
    $stmt = $pdo->prepare('SELECT id, email, full_name, password_hash, role FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        log_event('warning', 'Başarısız giriş denemesi', ['email' => $email]);
        flash('danger', 'Giriş bilgileri hatalı.');
        redirect('../views/login.php');
    }

    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'email' => $user['email'],
        'full_name' => $user['full_name'],
        'role' => $user['role'],
    ];

    log_event('info', 'Kullanıcı giriş yaptı', ['email' => $email]);

    if ($user['role'] === 'admin') {
        redirect('../admin-dashboard.php');
    }
    if ($user['role'] === 'host') {
        redirect('../host-dashboard.php');
    }
    redirect('../views/home.php');
} catch (Throwable $exception) {
    log_event('error', 'Giriş işlemi hatası', ['error' => $exception->getMessage()]);
    flash('danger', 'Giriş sırasında beklenmeyen bir hata oluştu.');
    redirect('../views/login.php');
}
