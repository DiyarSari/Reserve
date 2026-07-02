<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    $role = current_user_role();
    if ($role === 'admin') {
        redirect('admin-dashboard.php');
    }
    if ($role === 'host') {
        redirect('host-dashboard.php');
    }
    if ($role === 'user') {
        redirect('views/home.php');
    }
}

redirect('views/home.php');
