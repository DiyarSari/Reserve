<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
enforce_post_payload_guard();

function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function current_user_role(): ?string
{
    return $_SESSION['user']['role'] ?? null;
}

function current_user_email(): ?string
{
    return $_SESSION['user']['email'] ?? null;
}

function redirect_to_role_home(?string $role = null): void
{
    $effectiveRole = $role ?? current_user_role();

    if ($effectiveRole === 'admin') {
        redirect(BASE_URL . '/admin-dashboard.php');
    }
    if ($effectiveRole === 'host') {
        redirect(BASE_URL . '/host-dashboard.php');
    }
    if ($effectiveRole === 'user') {
        redirect(BASE_URL . '/views/home.php');
    }

    redirect(BASE_URL . '/views/home.php');
}

function enforce_staff_panel_scope(bool $allowStaffPublicAccess = false): void
{
    if (!is_logged_in() || $allowStaffPublicAccess) {
        return;
    }

    $role = current_user_role();
    if ($role === 'admin' || $role === 'host') {
        redirect_to_role_home($role);
    }
}

function require_login(): void
{
    if (!is_logged_in()) {
        $uiLang = $_COOKIE['reserveai_lang'] ?? 'tr';
        $message = $uiLang === 'en'
            ? 'You must log in to continue.'
            : 'Devam etmek için giriş yapmalısınız.';

        flash('warning', $message);
        redirect(BASE_URL . '/views/login.php');
    }
}

function require_role(array $roles): void
{
    require_login();

    if (!in_array(current_user_role(), $roles, true)) {
        log_event('warning', 'Yetkisiz erişim denemesi', [
            'email' => current_user_email(),
            'path' => $_SERVER['REQUEST_URI'] ?? '',
        ]);
        flash('danger', 'Bu sayfaya erişim yetkiniz yok.');

        redirect_to_role_home(current_user_role());
    }
}
