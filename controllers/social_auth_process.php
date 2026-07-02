<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    redirect('../views/register.php');
}

$source = ($_POST['source'] ?? '') === 'login' ? 'login' : 'register';
$redirectPath = $source === 'login' ? '../views/login.php' : '../views/register.php';

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz form oturumu.');
    redirect($redirectPath);
}

$provider = strtolower(clean_input($_POST['provider'] ?? ''));
$allowedProviders = ['google', 'apple'];

if (!in_array($provider, $allowedProviders, true)) {
    flash('danger', 'Geçersiz sosyal giriş saglayicisi.');
    redirect($redirectPath);
}

log_event('info', 'Sosyal giriş denemesi', ['provider' => $provider, 'source' => $source]);
flash('warning', 'Sosyal giriş şu anda aktif değil. Lütfen e-posta ve şifre ile devam edin.');
redirect($redirectPath);
