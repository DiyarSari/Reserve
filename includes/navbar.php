<?php
$user = current_user();
$activePage = $activePage ?? '';
$activeTab = clean_input((string) ($activeTab ?? ($_GET['tab'] ?? '')));
$brandHref = BASE_URL . '/views/home.php';
if (($user['role'] ?? '') === 'admin') {
    $brandHref = BASE_URL . '/admin-dashboard.php';
} elseif (($user['role'] ?? '') === 'host') {
    $brandHref = BASE_URL . '/host-dashboard.php';
}
?>
<nav class="navbar navbar-expand-lg site-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand brand-font fw-bold" href="<?= e($brandHref) ?>">
            Reserve
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Menü">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3">
                <?php if (($user['role'] ?? '') === 'host'): ?>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'host' && ($activeTab === '' || $activeTab === 'overview') ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/host-dashboard.php" data-i18n="nav.host_panel">Host Paneli</a></li>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'host' && $activeTab === 'reservations' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/host-reservations.php" data-i18n="host.reservations">Rezervasyonlar</a></li>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'host' && $activeTab === 'tables' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/host-tables.php" data-i18n="host.tables">Masalar</a></li>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'host' && $activeTab === 'menu' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/host-menu.php" data-i18n="host.menu">Menü</a></li>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'host' && $activeTab === 'qr' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/host-qr.php" data-i18n="host.qr_checkin">QR Check-in</a></li>
                <?php elseif (($user['role'] ?? '') === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'admin' && ($activeTab === '' || $activeTab === 'overview') ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/admin-dashboard.php" data-i18n="nav.admin_panel">Admin Paneli</a></li>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'admin' && $activeTab === 'restaurants' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/admin-restaurants.php" data-i18n="admin.restaurants">Restoranlar</a></li>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'admin' && $activeTab === 'reservations' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/admin-reservations.php" data-i18n="admin.reservations">Rezervasyonlar</a></li>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'admin' && $activeTab === 'users' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/admin-users.php" data-i18n="admin.users">Kullanıcılar</a></li>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'admin' && $activeTab === 'logs' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/admin-logs.php" data-i18n="nav.logs">Loglar</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/views/home.php" data-i18n="nav.home">Ana Sayfa</a></li>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'restaurants' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/views/restaurants.php" data-i18n="nav.restaurants">Restoranlar</a></li>
                    <li class="nav-item"><a class="nav-link <?= $activePage === 'reservations' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/views/my_reservations.php" data-i18n="nav.my_reservations">Rezervasyonlarım</a></li>
                    <li class="nav-item"><a class="nav-link js-about-link <?= $activePage === 'about' ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/views/home.php#siteFooter" data-i18n="nav.about">Hakkımızda</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex gap-2 align-items-center">
                <input type="hidden" id="languageSelect" value="tr">
                <div class="dropdown">
                    <button class="icon-btn" type="button" data-bs-toggle="dropdown" aria-label="Language">
                        <i class="bi bi-globe2"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item" type="button" data-language-choice="en">EN</button></li>
                        <li><button class="dropdown-item" type="button" data-language-choice="tr">TR</button></li>
                    </ul>
                </div>
                <?php if ($user): ?>
                    <?php if ($user['role'] === 'admin'): ?>
                        <a href="<?= e(BASE_URL) ?>/admin-dashboard.php" class="icon-btn" aria-label="Profile"><i class="bi bi-person"></i></a>
                    <?php elseif ($user['role'] === 'host'): ?>
                        <a href="<?= e(BASE_URL) ?>/host-dashboard.php" class="icon-btn" aria-label="Profile"><i class="bi bi-person"></i></a>
                    <?php else: ?>
                        <a href="<?= e(BASE_URL) ?>/views/my_reservations.php" class="icon-btn" aria-label="Profile"><i class="bi bi-person"></i></a>
                    <?php endif; ?>
                    <a href="<?= e(BASE_URL) ?>/controllers/logout_process.php" class="icon-btn" aria-label="Logout"><i class="bi bi-box-arrow-right"></i></a>
                <?php else: ?>
                    <a href="<?= e(BASE_URL) ?>/views/login.php" class="icon-btn" aria-label="Login" title="Login">
                        <i class="bi bi-person"></i>
                    </a>
                    <a href="<?= e(BASE_URL) ?>/views/register.php" class="icon-btn icon-btn-primary" aria-label="Sign Up" title="Sign Up">
                        <i class="bi bi-person-plus"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
