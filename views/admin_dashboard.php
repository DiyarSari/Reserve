<?php
$pageTitle = 'Admin Paneli';
$activePage = 'admin';
$allowStaffPublicAccess = true;
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/header.php';

ensure_partner_applications_table();

$activeTab = clean_input((string) ($activeTab ?? ($_GET['tab'] ?? 'overview')));
$allowedTabs = ['overview', 'restaurants', 'reservations', 'users', 'logs'];
if (!in_array($activeTab, $allowedTabs, true)) {
    $activeTab = 'overview';
}

$stats = get_admin_stats();

$restaurants = [];
$users = [];
$reservations = [];
$logs = [];
$applications = [];
$pendingApplications = [];
$recentReservations = [];
$recentLogs = [];

if (in_array($activeTab, ['overview', 'restaurants'], true)) {
    $applications = get_partner_applications();
    $pendingApplications = array_values(array_filter($applications, static fn (array $application): bool => $application['status'] === 'pending'));

    $restaurantStmt = $pdo->prepare('SELECT * FROM restaurants ORDER BY status ASC, name ASC');
    $restaurantStmt->execute();
    $restaurants = $restaurantStmt->fetchAll();
}

if ($activeTab === 'users') {
    $userStmt = $pdo->prepare('SELECT id, email, full_name, role, created_date FROM users ORDER BY created_date DESC');
    $userStmt->execute();
    $users = $userStmt->fetchAll();
}

if ($activeTab === 'reservations') {
    $reservationStmt = $pdo->prepare(
        'SELECT id, reservation_code, restaurant_name, customer_name, customer_email, guest_count, reservation_date, reservation_time, table_number, status, owner_email
         FROM reservations
         ORDER BY reservation_date DESC, reservation_time DESC, id DESC'
    );
    $reservationStmt->execute();
    $reservations = $reservationStmt->fetchAll();
}

if ($activeTab === 'logs') {
    $logStmt = $pdo->prepare('SELECT * FROM system_logs ORDER BY created_at DESC LIMIT 200');
    $logStmt->execute();
    $logs = $logStmt->fetchAll();
}

if ($activeTab === 'overview') {
    $recentReservationStmt = $pdo->prepare(
        'SELECT reservation_code, restaurant_name, reservation_date, reservation_time, status
         FROM reservations
         ORDER BY reservation_date DESC, reservation_time DESC, id DESC
         LIMIT 5'
    );
    $recentReservationStmt->execute();
    $recentReservations = $recentReservationStmt->fetchAll();

    $recentLogStmt = $pdo->prepare('SELECT created_at, level, message FROM system_logs ORDER BY created_at DESC LIMIT 5');
    $recentLogStmt->execute();
    $recentLogs = $recentLogStmt->fetchAll();
}

$restaurantStatusLabels = [
    'pending' => 'Bekliyor',
    'approved' => 'Onaylandı',
    'rejected' => 'Reddedildi',
    'suspended' => 'Askıya alındı',
];
$applicationStatusLabels = [
    'pending' => 'Bekliyor',
    'approved' => 'Onaylandı',
    'rejected' => 'Reddedildi',
];
$reservationStatusLabels = [
    'pending' => 'Bekliyor',
    'confirmed' => 'Onaylandı',
    'checked_in' => 'Tamamlandı',
    'completed' => 'Tamamlandı',
    'cancelled' => 'İptal edildi',
    'no_show' => 'Gelmedi',
];

$heroTextMap = [
    'overview' => 'Yönetim araçlarına hızlıca eriş.',
    'restaurants' => 'Restoran başvurularını ve yayın durumlarını bu sayfadan yönet.',
    'reservations' => 'Tüm rezervasyon kayıtlarını merkezi olarak görüntüle.',
    'users' => 'Kullanıcı hesaplarını ve rollerini ayrı sayfada takip et.',
    'logs' => 'Sistem loglarını ayrı sayfada incele.',
];
$heroText = $heroTextMap[$activeTab] ?? $heroTextMap['overview'];
?>
<section class="admin-hero">
    <div>
        <span class="section-eyebrow" data-i18n="admin.panel">Admin Paneli</span>
        <h1 data-i18n="admin.dashboard">Admin Paneli</h1>
        <p><?= e($heroText) ?></p>
    </div>
</section>

<?php if ($activeTab === 'overview'): ?>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="admin-stat-card">
                <div class="admin-stat-icon"><i class="bi bi-people"></i></div>
                <span data-i18n="admin.users">Kullanıcılar</span>
                <strong><?= $stats['users'] ?></strong>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="admin-stat-card">
                <div class="admin-stat-icon"><i class="bi bi-shop"></i></div>
                <span data-i18n="admin.restaurants">Restoranlar</span>
                <strong><?= $stats['restaurants'] ?></strong>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="admin-stat-card">
                <div class="admin-stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <span data-i18n="admin.pending">Bekleyen</span>
                <strong><?= $stats['pending_restaurants'] ?></strong>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="admin-stat-card">
                <div class="admin-stat-icon"><i class="bi bi-shop-window"></i></div>
                <span data-i18n="admin.partner_applications">Partner Basvurulari</span>
                <strong><?= count($pendingApplications) ?></strong>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <section class="admin-section-card h-100">
                <div class="admin-card-heading">
                    <div>
                        <span class="section-eyebrow">Hızlı Geçiş</span>
                        <h2>Admin Araçları</h2>
                    </div>
                </div>
                <div class="panel-nav-grid panel-nav-grid-admin mt-3">
                    <a href="<?= e(BASE_URL) ?>/admin-restaurants.php" class="panel-nav-btn">
                        <i class="bi bi-shop-window"></i>
                        <span data-i18n="admin.restaurants">Restoranlar</span>
                    </a>
                    <a href="<?= e(BASE_URL) ?>/admin-reservations.php" class="panel-nav-btn">
                        <i class="bi bi-calendar2-check"></i>
                        <span data-i18n="admin.reservations">Rezervasyonlar</span>
                    </a>
                    <a href="<?= e(BASE_URL) ?>/admin-users.php" class="panel-nav-btn">
                        <i class="bi bi-people"></i>
                        <span data-i18n="admin.users">Kullanıcılar</span>
                    </a>
                    <a href="<?= e(BASE_URL) ?>/admin-logs.php" class="panel-nav-btn">
                        <i class="bi bi-journal-text"></i>
                        <span data-i18n="nav.logs">Loglar</span>
                    </a>
                </div>
            </section>
        </div>
        <div class="col-lg-6">
            <section class="admin-section-card h-100">
                <div class="admin-card-heading">
                    <div>
                        <span class="section-eyebrow" data-i18n="admin.partner_program">Partner Programı</span>
                        <h2>Bekleyen başvurular</h2>
                    </div>
                </div>
                <?php if (empty($pendingApplications)): ?>
                    <div class="admin-empty-state"><i class="bi bi-shop-window"></i><strong data-i18n="admin.no_partner_applications">Henüz partner başvurusu yok.</strong></div>
                <?php else: ?>
                    <div class="table-responsive admin-table-wrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th data-i18n="admin.restaurant">Restoran</th>
                                    <th data-i18n="admin.contact">İletişim</th>
                                    <th data-i18n="admin.status">Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($pendingApplications, 0, 5) as $application): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($application['restaurant_name']) ?></strong>
                                            <div class="small text-muted"><?= e($application['city']) ?> / <?= e($application['cuisine_type']) ?></div>
                                        </td>
                                        <td>
                                            <div><?= e($application['contact_name']) ?></div>
                                            <div class="small text-muted"><?= e($application['restaurant_email']) ?></div>
                                        </td>
                                        <td><span class="badge admin-status-badge" data-status="<?= e($application['status']) ?>"><?= e($applicationStatusLabels[$application['status']] ?? $application['status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <section class="admin-section-card h-100">
                <div class="admin-card-heading">
                    <div>
                        <span class="section-eyebrow" data-i18n="admin.reservations">Rezervasyonlar</span>
                        <h2>Son işlemler</h2>
                    </div>
                </div>
                <?php if (empty($recentReservations)): ?>
                    <div class="admin-empty-state"><i class="bi bi-calendar2-x"></i><strong data-i18n="admin.no_reservations">Henüz rezervasyon yok.</strong></div>
                <?php else: ?>
                    <div class="table-responsive admin-table-wrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th data-i18n="admin.code">Kod</th>
                                    <th data-i18n="admin.restaurant">Restoran</th>
                                    <th data-i18n="admin.date">Tarih</th>
                                    <th data-i18n="admin.status">Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentReservations as $reservation): ?>
                                    <tr>
                                        <td class="fw-bold"><?= e($reservation['reservation_code']) ?></td>
                                        <td><?= e($reservation['restaurant_name']) ?></td>
                                        <td><?= e($reservation['reservation_date']) ?> <?= e(substr((string) $reservation['reservation_time'], 0, 5)) ?></td>
                                        <td><span class="badge reservation-status-badge" data-status="<?= e($reservation['status']) ?>" data-i18n="status.<?= e($reservation['status']) ?>"><?= e($reservationStatusLabels[$reservation['status']] ?? $reservation['status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
        <div class="col-lg-6">
            <section class="admin-section-card h-100">
                <div class="admin-card-heading">
                    <div>
                        <span class="section-eyebrow" data-i18n="admin.logs">Loglar</span>
                        <h2 data-i18n="admin.system_logs">Sistem Logları</h2>
                    </div>
                </div>
                <?php if (empty($recentLogs)): ?>
                    <div class="admin-empty-state"><i class="bi bi-journal-text"></i><strong data-i18n="admin.no_logs">Henüz sistem logu yok.</strong></div>
                <?php else: ?>
                    <div class="table-responsive admin-table-wrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th data-i18n="admin.date">Tarih</th><th data-i18n="admin.level">Seviye</th><th data-i18n="admin.message">Mesaj</th></tr></thead>
                            <tbody>
                            <?php foreach ($recentLogs as $log): ?>
                                <tr>
                                    <td><?= e($log['created_at']) ?></td>
                                    <td><span class="badge text-bg-secondary"><?= e($log['level']) ?></span></td>
                                    <td><?= e($log['message']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>

<?php elseif ($activeTab === 'restaurants'): ?>
    <section class="admin-section-card mb-4">
        <div class="admin-card-heading">
            <div>
                <span class="section-eyebrow" data-i18n="admin.partner_program">Partner Programı</span>
                <h2 data-i18n="admin.partner_applications_title">Restoran partner başvuruları</h2>
            </div>
        </div>
        <div class="table-responsive admin-table-wrap">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th data-i18n="admin.restaurant">Restoran</th>
                        <th data-i18n="admin.contact">İletişim</th>
                        <th data-i18n="admin.details">Bilgiler</th>
                        <th data-i18n="admin.status">Durum</th>
                        <th data-i18n="admin.action">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($applications as $application): ?>
                    <tr>
                        <td>
                            <strong><?= e($application['restaurant_name']) ?></strong>
                            <div class="small text-muted"><?= e($application['city']) ?> / <?= e($application['cuisine_type']) ?></div>
                        </td>
                        <td>
                            <div><?= e($application['contact_name']) ?></div>
                            <div class="small text-muted"><?= e($application['restaurant_email']) ?></div>
                            <div class="small text-muted"><?= e($application['phone']) ?></div>
                        </td>
                        <td class="small">
                            <div><?= e($application['address']) ?></div>
                            <div class="text-muted"><?= e(substr((string) $application['opening_time'], 0, 5)) ?> - <?= e(substr((string) $application['closing_time'], 0, 5)) ?></div>
                        </td>
                        <td>
                            <span class="badge admin-status-badge" data-status="<?= e($application['status']) ?>">
                                <?= e($applicationStatusLabels[$application['status']] ?? $application['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($application['status'] === 'pending'): ?>
                                <form action="<?= e(BASE_URL) ?>/controllers/admin_partner_application_process.php" method="post" class="d-grid gap-2">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="application_id" value="<?= (int) $application['id'] ?>">
                                    <input type="text" class="form-control form-control-sm" name="review_notes" maxlength="500" placeholder="Opsiyonel not" data-i18n-placeholder="admin.review_note_optional">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-success btn-sm w-100" name="action" value="approve" data-i18n="admin.approve">Onayla</button>
                                        <button class="btn btn-outline-danger btn-sm w-100" name="action" value="reject" data-i18n="admin.reject">Reddet</button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <span class="small text-muted" data-i18n="admin.reviewed">İncelendi</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($applications)): ?>
                    <tr><td colspan="5"><div class="admin-empty-state"><i class="bi bi-shop-window"></i><strong data-i18n="admin.no_partner_applications">Henüz partner başvurusu yok.</strong></div></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="admin-section-card">
        <div class="admin-card-heading">
            <div>
                <span class="section-eyebrow" data-i18n="admin.moderation">Moderasyon</span>
                <h2 data-i18n="admin.restaurant_approvals">Restoran onayları</h2>
            </div>
        </div>
        <div class="table-responsive admin-table-wrap">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th data-i18n="admin.name">Ad</th><th data-i18n="admin.owner">Sahip</th><th data-i18n="admin.city">Şehir</th><th data-i18n="admin.status">Durum</th><th data-i18n="admin.action">İşlem</th></tr></thead>
                <tbody>
                <?php foreach ($restaurants as $restaurant): ?>
                    <tr>
                        <td class="fw-bold"><?= e($restaurant['name']) ?></td>
                        <td><?= e($restaurant['owner_email']) ?></td>
                        <td><?= e($restaurant['city']) ?></td>
                        <td><span class="badge admin-status-badge" data-status="<?= e($restaurant['status']) ?>" data-i18n="restaurant_status.<?= e($restaurant['status']) ?>"><?= e($restaurantStatusLabels[$restaurant['status']] ?? $restaurant['status']) ?></span></td>
                        <td>
                            <form action="<?= e(BASE_URL) ?>/controllers/admin_restaurant_process.php" method="post" class="d-flex gap-2">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="restaurant_id" value="<?= (int) $restaurant['id'] ?>">
                                <select class="form-select form-select-sm" name="status">
                                    <?php foreach (['pending', 'approved', 'rejected', 'suspended'] as $status): ?>
                                        <option value="<?= e($status) ?>" <?= $restaurant['status'] === $status ? 'selected' : '' ?> data-i18n="restaurant_status.<?= e($status) ?>"><?= e($restaurantStatusLabels[$status] ?? $status) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-sm btn-primary" data-i18n="admin.save">Kaydet</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($restaurants)): ?>
                    <tr><td colspan="5"><div class="admin-empty-state"><i class="bi bi-shop"></i><strong data-i18n="admin.no_restaurants">Henüz restoran yok.</strong></div></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php elseif ($activeTab === 'reservations'): ?>
    <section class="admin-section-card">
        <div class="admin-card-heading">
            <div>
                <span class="section-eyebrow" data-i18n="admin.reservations">Rezervasyonlar</span>
                <h2 data-i18n="admin.recent_reservations">Son rezervasyonlar</h2>
                <p class="mb-0 text-muted small" data-i18n="admin.all_reservations_hint">Tüm rezervasyon kayıtları listelenir.</p>
            </div>
        </div>
        <div class="table-responsive admin-table-wrap">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th data-i18n="admin.code">Kod</th>
                        <th data-i18n="admin.restaurant">Restoran</th>
                        <th data-i18n="admin.customer">Müşteri</th>
                        <th data-i18n="host.guest_count">Kişi</th>
                        <th data-i18n="admin.date">Tarih</th>
                        <th data-i18n="host.time">Saat</th>
                        <th data-i18n="host.table">Masa</th>
                        <th data-i18n="admin.status">Durum</th>
                        <th data-i18n="admin.host">Host</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td class="fw-bold"><?= e($reservation['reservation_code']) ?></td>
                        <td><?= e($reservation['restaurant_name']) ?></td>
                        <td>
                            <div><?= e($reservation['customer_name']) ?></div>
                            <div class="small text-muted"><?= e($reservation['customer_email']) ?></div>
                        </td>
                        <td><?= (int) $reservation['guest_count'] ?></td>
                        <td><?= e($reservation['reservation_date']) ?></td>
                        <td><?= e(substr((string) $reservation['reservation_time'], 0, 5)) ?></td>
                        <td><?= e((string) $reservation['table_number']) ?></td>
                        <td>
                            <span class="badge reservation-status-badge" data-status="<?= e($reservation['status']) ?>" data-i18n="status.<?= e($reservation['status']) ?>">
                                <?= e($reservationStatusLabels[$reservation['status']] ?? $reservation['status']) ?>
                            </span>
                        </td>
                        <td class="small text-muted"><?= e($reservation['owner_email']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($reservations)): ?>
                    <tr><td colspan="9"><div class="admin-empty-state"><i class="bi bi-calendar2-x"></i><strong data-i18n="admin.no_reservations">Henüz rezervasyon yok.</strong></div></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php elseif ($activeTab === 'users'): ?>
    <section class="admin-section-card">
        <div class="admin-card-heading">
            <div>
                <span class="section-eyebrow" data-i18n="admin.accounts">Hesaplar</span>
                <h2 data-i18n="admin.users">Kullanıcılar</h2>
            </div>
        </div>
        <div class="table-responsive admin-table-wrap">
            <table class="table align-middle mb-0">
                <thead><tr><th data-i18n="admin.name">Ad</th><th data-i18n="admin.email">E-posta</th><th data-i18n="admin.role">Rol</th><th data-i18n="admin.registered">Kayıt</th></tr></thead>
                <tbody>
                <?php foreach ($users as $appUser): ?>
                    <tr>
                        <td><?= e($appUser['full_name']) ?></td>
                        <td><?= e($appUser['email']) ?></td>
                        <td><span class="badge admin-role-badge" data-i18n="role.<?= e($appUser['role']) ?>"><?= e($appUser['role']) ?></span></td>
                        <td class="small text-muted"><?= e($appUser['created_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                    <tr><td colspan="4"><div class="admin-empty-state"><i class="bi bi-people"></i><strong data-i18n="admin.no_users">Henüz kullanıcı yok.</strong></div></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php else: ?>
    <section class="admin-section-card">
        <div class="admin-card-heading">
            <div>
                <span class="section-eyebrow" data-i18n="admin.logs">Loglar</span>
                <h2 data-i18n="admin.system_logs">Sistem Logları</h2>
            </div>
        </div>
        <div class="table-responsive admin-table-wrap">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th data-i18n="admin.date">Tarih</th><th data-i18n="admin.level">Seviye</th><th data-i18n="admin.message">Mesaj</th><th data-i18n="admin.context">Context</th></tr></thead>
                <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= e($log['created_at']) ?></td>
                        <td><span class="badge text-bg-secondary"><?= e($log['level']) ?></span></td>
                        <td><?= e($log['message']) ?></td>
                        <td class="small text-muted"><?= e((string) $log['context']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="4"><div class="admin-empty-state"><i class="bi bi-journal-text"></i><strong data-i18n="admin.no_logs">Henüz sistem logu yok.</strong></div></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
