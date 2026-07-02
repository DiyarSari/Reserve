<?php
$pageTitle = 'Host Paneli';
$activePage = 'host';
$allowStaffPublicAccess = true;
$activeTab = $activeTab ?? 'overview';
$extraScripts = ['../assets/js/host.js'];
require_once __DIR__ . '/../includes/auth.php';
require_role(['host']);
require_once __DIR__ . '/../includes/header.php';

$restaurant = get_host_restaurant((string) current_user_email());
$tables = $restaurant ? get_host_tables((int) $restaurant['id']) : [];
$menuCategories = $restaurant ? get_menu_categories((int) $restaurant['id']) : [];
$hostMenu = $restaurant ? get_restaurant_menu((int) $restaurant['id'], false) : [];
$selectedDate = clean_input($_GET['date'] ?? date('Y-m-d'));
$dailyReservations = get_host_daily_reservations((string) current_user_email(), $selectedDate);
$activeReservations = array_filter($dailyReservations, static fn($r) => in_array($r['status'], ['pending', 'confirmed'], true));
$statusLabels = [
    'pending' => 'Bekliyor',
    'confirmed' => 'Onaylandı',
    'checked_in' => 'Tamamlandı',
    'completed' => 'Tamamlandı',
    'cancelled' => 'İptal edildi',
    'no_show' => 'Gelmedi',
];

$allowedTabs = ['overview', 'reservations', 'tables', 'menu', 'qr'];
$activeTab = clean_input((string) ($activeTab ?? ($_GET['tab'] ?? 'overview')));
if (!in_array($activeTab, $allowedTabs, true)) {
    $activeTab = 'overview';
}

$menuItemCount = 0;
foreach ($hostMenu as $categoryData) {
    $menuItemCount += count($categoryData['items']);
}

$heroTextMap = [
    'overview' => 'Yönetim araçlarına hızlıca eriş.',
    'reservations' => 'Rezervasyonları tek ekranda görüntüle, filtrele ve durumlarını güncelle.',
    'tables' => 'Masa kapasitesi ve kullanılabilirlik durumlarını ayrı bir ekranda yönet.',
    'menu' => 'Menü kategorilerini ve ürünlerini modern kart/tablo düzeninde güncelle.',
    'qr' => 'QR doğrulama akışını bu sayfadan yönet ve tarayıcıya geçiş yap.',
];
$heroText = $heroTextMap[$activeTab] ?? $heroTextMap['overview'];
?>
<section class="host-hero">
    <div>
        <span class="section-eyebrow" data-i18n="host.panel">Host Panel</span>
        <h1><?= $restaurant ? e($restaurant['name']) : 'Host Paneli' ?></h1>
        <p><?= e($heroText) ?></p>
    </div>
    <a href="<?= e(BASE_URL) ?>/views/host_qr_scanner.php" class="btn btn-reserve">
        <i class="bi bi-qr-code-scan me-2"></i><span data-i18n="host.qr_checkin">QR Check-in</span>
    </a>
</section>

<?php if (!$restaurant): ?>
    <div class="alert alert-warning" data-i18n="host.no_restaurant_account">Bu hesap için restoran kaydı bulunamadı.</div>
<?php elseif ($activeTab === 'overview'): ?>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="host-stat-card">
                <div class="host-stat-icon"><i class="bi bi-grid-3x3-gap"></i></div>
                <span data-i18n="host.total_tables">Toplam masa</span>
                <strong><?= count($tables) ?></strong>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="host-stat-card">
                <div class="host-stat-icon"><i class="bi bi-calendar2-check"></i></div>
                <span data-i18n="host.today_reservations">Bugünkü rezervasyon</span>
                <strong><?= count($dailyReservations) ?></strong>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="host-stat-card">
                <div class="host-stat-icon"><i class="bi bi-clock-history"></i></div>
                <span data-i18n="host.active_reservations">Aktif rezervasyon</span>
                <strong><?= count($activeReservations) ?></strong>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="host-stat-card">
                <div class="host-stat-icon"><i class="bi bi-card-list"></i></div>
                <span data-i18n="host.menu">Menü</span>
                <strong><?= $menuItemCount ?></strong>
            </div>
        </div>
    </div>

    <div class="host-section-card mb-4">
        <div class="host-card-heading mb-0">
            <div>
                <span class="section-eyebrow">Hızlı Geçiş</span>
                <h2>Host Araçları</h2>
            </div>
        </div>
        <div class="panel-nav-grid panel-nav-grid-host mt-3">
            <a class="panel-nav-btn" href="<?= e(BASE_URL) ?>/host-reservations.php">
                <i class="bi bi-calendar2-week"></i>
                <span data-i18n="host.reservations">Rezervasyonlar</span>
            </a>
            <a class="panel-nav-btn" href="<?= e(BASE_URL) ?>/host-tables.php">
                <i class="bi bi-grid-3x3-gap"></i>
                <span data-i18n="host.tables">Masalar</span>
            </a>
            <a class="panel-nav-btn" href="<?= e(BASE_URL) ?>/host-menu.php">
                <i class="bi bi-card-list"></i>
                <span data-i18n="host.menu">Menü</span>
            </a>
            <a class="panel-nav-btn" href="<?= e(BASE_URL) ?>/host-qr.php">
                <i class="bi bi-qr-code-scan"></i>
                <span data-i18n="host.qr_checkin">QR Check-in</span>
            </a>
        </div>
    </div>

    <div class="host-section-card mb-4">
        <div class="host-card-heading mb-3">
            <div>
                <span class="section-eyebrow" data-i18n="host.profile">Profil</span>
                <h2 data-i18n="host.restaurant_profile">Restoran profili</h2>
            </div>
        </div>
        <form action="<?= e(BASE_URL) ?>/controllers/host_restaurant_process.php" method="post" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" data-i18n="host.restaurant_name">Restoran adı</label>
                    <input class="form-control" name="name" value="<?= e($restaurant['name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" data-i18n="host.cuisine_type">Mutfak türü</label>
                    <input class="form-control" name="cuisine_type" value="<?= e($restaurant['cuisine_type']) ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label" data-i18n="host.description">Açıklama</label>
                    <textarea class="form-control" name="description" rows="2" required><?= e($restaurant['description']) ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label" data-i18n="host.city">Şehir</label>
                    <input class="form-control" name="city" value="<?= e($restaurant['city']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">İlçe</label>
                    <input class="form-control" name="district" value="<?= e((string) ($restaurant['district'] ?? '')) ?>" required maxlength="80">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mahalle</label>
                    <input class="form-control" name="neighborhood" value="<?= e((string) ($restaurant['neighborhood'] ?? '')) ?>" required maxlength="120">
                </div>
                <div class="col-md-6">
                    <label class="form-label" data-i18n="host.phone">Telefon</label>
                    <input type="tel" class="form-control" name="phone" value="<?= e($restaurant['phone']) ?>" required minlength="10" maxlength="13" pattern="(\+90|0)?[1-9][0-9]{9}" data-validate-phone-tr="1" inputmode="tel" placeholder="+905551112233" autocomplete="tel">
                    <div class="invalid-feedback" data-i18n="validation.phone_required">Telefon alanı zorunludur.</div>
                </div>
                <div class="col-12">
                    <label class="form-label" data-i18n="host.address">Adres</label>
                    <input class="form-control" name="address" value="<?= e($restaurant['address']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" data-i18n="host.opening">Açılış</label>
                    <input type="time" class="form-control" name="opening_time" value="<?= e(substr($restaurant['opening_time'], 0, 5)) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" data-i18n="host.closing">Kapanış</label>
                    <input type="time" class="form-control" name="closing_time" value="<?= e(substr($restaurant['closing_time'], 0, 5)) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" data-i18n="host.reservation_duration">Rezervasyon süresi dakika</label>
                    <input type="number" class="form-control" name="reservation_duration_minutes" value="<?= (int) $restaurant['reservation_duration_minutes'] ?>" min="30" max="300">
                </div>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                <button class="btn btn-primary" data-i18n="host.save_profile">Profili Kaydet</button>
            </div>
        </form>
    </div>

<?php elseif ($activeTab === 'reservations'): ?>
    <div class="host-section-card">
        <div class="host-card-heading">
            <div>
                <span class="section-eyebrow" data-i18n="host.reservations">Rezervasyonlar</span>
                <h2 data-i18n="host.daily_reservations">Günlük rezervasyonlar</h2>
                <p class="host-section-subtitle mb-0">
                    <span data-i18n="host.selected_date">Seçili tarih</span>:
                    <strong><?= e(date('d.m.Y', strtotime($selectedDate))) ?></strong>
                </p>
            </div>
            <form method="get" class="d-flex gap-2">
                <input type="date" class="form-control form-control-sm" name="date" value="<?= e($selectedDate) ?>">
                <button class="btn btn-outline-primary btn-sm" data-i18n="host.filter">Filtrele</button>
            </form>
        </div>
        <div class="table-responsive host-table-wrap">
            <table class="table align-middle mb-0 js-host-reservation-table">
                <thead><tr><th data-i18n="host.code">Kod</th><th data-i18n="host.customer">Müşteri</th><th data-i18n="host.time">Saat</th><th data-i18n="host.table">Masa</th><th data-i18n="host.status">Durum</th><th data-i18n="host.update">Güncelle</th></tr></thead>
                <tbody>
                <?php foreach ($dailyReservations as $reservation): ?>
                    <tr>
                        <td class="fw-bold"><?= e($reservation['reservation_code']) ?></td>
                        <td><?= e($reservation['customer_name']) ?></td>
                        <td><?= e(substr($reservation['reservation_time'], 0, 5)) ?></td>
                        <td><?= e($reservation['table_number']) ?></td>
                        <td><span class="badge reservation-status-badge" data-status="<?= e($reservation['status']) ?>" data-i18n="status.<?= e($reservation['status']) ?>"><?= e($statusLabels[$reservation['status']] ?? $reservation['status']) ?></span></td>
                        <td class="table-fixed-actions">
                            <form action="<?= e(BASE_URL) ?>/controllers/host_reservation_process.php" method="post" class="d-flex gap-2">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="reservation_id" value="<?= (int) $reservation['id'] ?>">
                                <select class="form-select form-select-sm" name="status">
                                    <?php if ($reservation['status'] === 'no_show'): ?>
                                        <option value="no_show" selected data-i18n="status.no_show"><?= e($statusLabels['no_show'] ?? 'no_show') ?></option>
                                    <?php endif; ?>
                                    <?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status): ?>
                                        <option value="<?= e($status) ?>" <?= $reservation['status'] === $status ? 'selected' : '' ?> data-i18n="status.<?= e($status) ?>"><?= e($statusLabels[$status] ?? $status) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-sm btn-primary" data-i18n="host.save">Kaydet</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($dailyReservations)): ?>
                    <tr class="host-empty-row">
                        <td colspan="6">
                            <div class="host-empty-state">
                                <i class="bi bi-calendar2-check"></i>
                                <strong data-i18n="host.no_daily_reservations">Bu tarihte rezervasyon yok.</strong>
                                <span data-i18n="host.no_daily_reservations_hint">Farklı bir tarih seçerek restoranına gelen rezervasyonları görüntüleyebilirsin.</span>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($activeTab === 'tables'): ?>
    <div class="host-section-card">
        <div class="host-card-heading">
            <div>
                <span class="section-eyebrow" data-i18n="host.table_management">Masa Yönetimi</span>
                <h2 data-i18n="host.tables">Masalar</h2>
            </div>
            <button type="button" class="btn btn-primary btn-sm" data-host-modal-open="tableModal">
                <i class="bi bi-plus-lg me-1"></i><span data-i18n="host.add_table">Masa Ekle</span>
            </button>
        </div>
        <div class="table-responsive host-table-wrap">
            <table class="table align-middle mb-0">
                <thead><tr><th data-i18n="host.table_no_short">No</th><th data-i18n="host.capacity">Kapasite</th><th data-i18n="host.location">Konum</th><th data-i18n="host.status">Durum</th><th data-i18n="host.action">İşlem</th></tr></thead>
                <tbody>
                <?php foreach ($tables as $table): ?>
                    <tr>
                        <td class="fw-bold"><?= e($table['table_number']) ?></td>
                        <td><?= (int) $table['capacity'] ?></td>
                        <td><?= e($table['location']) ?></td>
                        <td><span class="badge <?= $table['is_active'] ? 'text-bg-success' : 'text-bg-secondary' ?>" data-i18n="<?= $table['is_active'] ? 'host.active' : 'host.passive' ?>"><?= $table['is_active'] ? 'Aktif' : 'Pasif' ?></span></td>
                        <td>
                            <form action="<?= e(BASE_URL) ?>/controllers/host_table_process.php" method="post" class="d-flex gap-2">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="table_id" value="<?= (int) $table['id'] ?>">
                                <button
                                    class="btn btn-outline-secondary btn-sm js-table-toggle"
                                    data-table-number="<?= e($table['table_number']) ?>"
                                    data-next-state="<?= $table['is_active'] ? 'passive' : 'active' ?>"
                                    data-i18n="<?= $table['is_active'] ? 'host.deactivate' : 'host.activate' ?>"
                                ><?= $table['is_active'] ? 'Pasife Al' : 'Aktif Yap' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($tables)): ?>
                    <tr><td colspan="5" class="text-muted" data-i18n="host.no_tables">Bu restoran için masa bulunmuyor.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="tableModal" tabindex="-1" aria-labelledby="tableModalTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= e(BASE_URL) ?>/controllers/host_table_process.php" method="post" class="needs-validation js-table-form" novalidate>
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="tableModalTitle" data-i18n="host.add_table">Masa Ekle</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="create">
                        <label class="form-label" for="table_number" data-i18n="host.table_no">Masa no</label>
                        <input class="form-control mb-2" id="table_number" name="table_number" required maxlength="20" pattern="[A-Za-z0-9\- ]{1,20}">
                        <label class="form-label" for="capacity" data-i18n="host.capacity">Kapasite</label>
                        <input type="number" min="1" max="20" class="form-control mb-2" id="capacity" name="capacity" required>
                        <label class="form-label" for="location" data-i18n="host.location">Konum</label>
                        <input class="form-control mb-2" id="location" name="location" maxlength="80">
                        <label class="form-label" for="table_description" data-i18n="host.description">Açıklama</label>
                        <textarea class="form-control" id="table_description" name="description" rows="2" maxlength="255"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" data-i18n="host.close">Kapat</button>
                        <button type="submit" class="btn btn-primary" data-i18n="host.add_table">Masa Ekle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tableStatusConfirmModal" tabindex="-1" aria-labelledby="tableStatusConfirmTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title h6 mb-0" id="tableStatusConfirmTitle">Masa durumunu güncelle</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="mb-0 text-muted small" id="tableStatusConfirmText">Seçili masanın durumunu değiştirmek istediğine emin misin?</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="button" class="btn btn-primary btn-sm" id="tableStatusConfirmAction">Onayla</button>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($activeTab === 'menu'): ?>
    <div class="host-section-card">
        <div class="host-card-heading mb-3">
            <div>
                <span class="section-eyebrow" data-i18n="host.menu">Menü</span>
                <h2 data-i18n="host.menu_management">Menü Yönetimi</h2>
                <p class="host-section-subtitle mb-0" data-i18n="host.menu_management_text">Kategorileri ekle; yemek adlarını, açıklamaları ve TL fiyatlarını güncel tut.</p>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="menu-admin-box h-100">
                    <h3 class="h6" data-i18n="host.add_menu_category">Menü Kategorisi Ekle</h3>
                    <form action="<?= e(BASE_URL) ?>/controllers/host_menu_process.php" method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="add_category">
                        <label class="form-label" for="category_name" data-i18n="host.category_name">Kategori adı</label>
                        <input class="form-control mb-2" id="category_name" name="category_name" placeholder="Starters" required minlength="2" maxlength="100">
                        <label class="form-label" for="display_order" data-i18n="host.display_order">Sıralama</label>
                        <input type="number" class="form-control mb-3" id="display_order" name="display_order" value="0" min="0">
                        <button class="btn btn-primary w-100" data-i18n="host.add_category">Kategori Ekle</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="menu-admin-box h-100">
                    <h3 class="h6" data-i18n="host.add_menu_item">Menü Ürünü Ekle</h3>
                    <form action="<?= e(BASE_URL) ?>/controllers/host_menu_process.php" method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="add_item">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label" for="category_id" data-i18n="host.category">Kategori</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="" data-i18n="host.select_category">Kategori Seç</option>
                                    <?php foreach ($menuCategories as $category): ?>
                                        <option value="<?= (int) $category['id'] ?>"><?= e($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="item_name" data-i18n="host.dish_name">Yemek adı</label>
                                <input class="form-control" id="item_name" name="item_name" required minlength="2" maxlength="150">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="price" data-i18n="host.price_tl">Price (TL)</label>
                                <input type="number" class="form-control" id="price" name="price" min="0" max="1000000" step="0.01" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="description" data-i18n="host.description_en">Açıklama</label>
                                <textarea class="form-control" id="description" name="description" rows="2" maxlength="500"></textarea>
                            </div>
                            <div class="col-md-9">
                                <label class="form-label" for="image_url" data-i18n="host.image_url_optional">Görsel URL opsiyonel</label>
                                <input type="url" class="form-control" id="image_url" name="image_url">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <label class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" checked>
                                    <span class="form-check-label" data-i18n="host.active_en">Aktif</span>
                                </label>
                            </div>
                        </div>
                        <button class="btn btn-primary mt-3" data-i18n="host.add_item">Ürün Ekle</button>
                    </form>
                </div>
            </div>
        </div>

        <?php if (empty($hostMenu)): ?>
            <div class="bg-white border rounded-2 p-3 text-muted" data-i18n="host.no_menu_categories">Henüz menü kategorisi yok.</div>
        <?php else: ?>
            <div class="menu-admin-list">
                <?php foreach ($hostMenu as $category): ?>
                    <div class="menu-admin-category menu-modern-category">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h3 class="mb-0"><?= e($category['name']) ?></h3>
                            <span class="badge rounded-pill text-bg-light" data-i18n="host.item_count" data-i18n-vars='{"count":"<?= count($category['items']) ?>"}'><?= count($category['items']) ?> ürün</span>
                        </div>
                        <?php if (empty($category['items'])): ?>
                            <p class="text-muted mb-0" data-i18n="host.no_items_category">Bu kategoride henüz ürün yok.</p>
                        <?php else: ?>
                            <div class="table-responsive host-table-wrap">
                                <table class="table align-middle mb-0 menu-modern-table">
                                    <thead>
                                        <tr>
                                            <th data-i18n="host.item">Ürün</th>
                                            <th data-i18n="host.description_en">Açıklama</th>
                                            <th data-i18n="host.price_tl">Fiyat</th>
                                            <th data-i18n="host.status">Durum</th>
                                            <th data-i18n="host.action">İşlem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($category['items'] as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="menu-modern-item-cell">
                                                        <?php if (!empty($item['image_url'])): ?>
                                                            <?php
                                                                $menuImageUrl = (string) $item['image_url'];
                                                                if (!preg_match('#^(https?:)?//#', $menuImageUrl) && strpos($menuImageUrl, 'data:') !== 0) {
                                                                    $menuImageUrl = rtrim(BASE_URL, '/') . '/' . ltrim($menuImageUrl, '/');
                                                                }
                                                            ?>
                                                            <img src="<?= e($menuImageUrl) ?>" alt="<?= e($item['name']) ?>" class="menu-modern-thumb">
                                                        <?php else: ?>
                                                            <span class="menu-modern-thumb menu-modern-thumb-placeholder"><i class="bi bi-image"></i></span>
                                                        <?php endif; ?>
                                                        <strong><?= e($item['name']) ?></strong>
                                                    </div>
                                                </td>
                                                <td class="text-muted small"><?= e($item['description']) ?></td>
                                                <td><strong class="js-menu-item-price" data-price-raw="<?= e(number_format((float) $item['price'], 2, '.', '')) ?>"><?= e(format_price((float) $item['price'])) ?></strong></td>
                                                <td>
                                                    <span class="badge <?= $item['is_active'] ? 'text-bg-success' : 'text-bg-secondary' ?>" data-i18n="<?= $item['is_active'] ? 'host.active' : 'host.passive' ?>">
                                                        <?= $item['is_active'] ? 'Aktif' : 'Pasif' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <button
                                                            type="button"
                                                            class="btn btn-outline-primary btn-sm js-menu-edit-open"
                                                            data-menu-item-id="<?= (int) $item['id'] ?>"
                                                            data-menu-item-name="<?= e($item['name']) ?>"
                                                            data-menu-item-description="<?= e((string) $item['description']) ?>"
                                                            data-menu-item-price="<?= e(number_format((float) $item['price'], 2, '.', '')) ?>"
                                                            data-menu-item-image="<?= e((string) $item['image_url']) ?>"
                                                            data-menu-item-category="<?= (int) $category['id'] ?>"
                                                            data-menu-item-active="<?= (int) $item['is_active'] ?>"
                                                            data-host-modal-open="menuItemModal"
                                                            data-i18n="host.edit"
                                                        >
                                                            Düzenle
                                                        </button>
                                                        <form action="<?= e(BASE_URL) ?>/controllers/host_menu_process.php" method="post">
                                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                            <input type="hidden" name="action" value="toggle_item">
                                                            <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                                                            <button class="btn btn-outline-secondary btn-sm" type="submit" data-i18n="<?= $item['is_active'] ? 'host.deactivate' : 'host.activate' ?>">
                                                                <?= $item['is_active'] ? 'Pasife Al' : 'Aktif Yap' ?>
                                                            </button>
                                                        </form>
                                                        <form action="<?= e(BASE_URL) ?>/controllers/host_menu_process.php" method="post">
                                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                            <input type="hidden" name="action" value="delete_item">
                                                            <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                                                            <button class="btn btn-outline-danger btn-sm" type="submit" data-i18n="host.delete">Sil</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="menuItemModal" tabindex="-1" aria-labelledby="menuItemModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="<?= e(BASE_URL) ?>/controllers/host_menu_process.php" method="post" class="needs-validation" novalidate>
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="menuItemModalTitle" data-i18n="host.edit_menu_item">Menü Ürünü Düzenle</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="item_id" id="menuEditItemId" value="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="menuEditName" data-i18n="host.dish_name">Yemek adı</label>
                                <input class="form-control" id="menuEditName" name="item_name" required minlength="2" maxlength="150">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="menuEditCategory" data-i18n="host.category">Kategori</label>
                                <select class="form-select" id="menuEditCategory" name="category_id" required>
                                    <?php foreach ($menuCategories as $category): ?>
                                        <option value="<?= (int) $category['id'] ?>"><?= e($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="menuEditPrice" data-i18n="host.price_tl">Fiyat (TL)</label>
                                <input type="number" class="form-control" id="menuEditPrice" name="price" min="0" max="1000000" step="0.01" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="menuEditDescription" data-i18n="host.description_en">Açıklama</label>
                                <textarea class="form-control" id="menuEditDescription" name="description" rows="3" maxlength="500"></textarea>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" for="menuEditImage" data-i18n="host.image_url">Görsel URL</label>
                                <input type="url" class="form-control" id="menuEditImage" name="image_url">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <label class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="menuEditActive" name="is_active">
                                    <span class="form-check-label" data-i18n="host.active_en">Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" data-i18n="host.close">Kapat</button>
                        <button type="submit" class="btn btn-primary" data-i18n="host.save">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card host-panel-card qr-host-card h-100">
                <div class="card-body d-flex align-items-start gap-3">
                    <div class="scan-icon"><i class="bi bi-qr-code-scan"></i></div>
                    <div>
                        <h2 class="h5" data-i18n="host.scan_qr_title">Rezervasyon QR Tara</h2>
                        <p class="text-muted mb-3" data-i18n="host.scan_qr_text">QR tarayıcıyı aç, müşteri rezervasyonunu doğrula ve geliş/tamamlanma durumunu işaretle.</p>
                        <a href="<?= e(BASE_URL) ?>/views/host_qr_scanner.php" class="btn btn-success" data-i18n="host.open_qr_scanner">QR Tarayıcıyı Aç</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card host-panel-card h-100">
                <div class="card-body">
                    <h3 class="h6 mb-3" data-i18n="host.qr_flow_title">QR İş Akışı</h3>
                    <ol class="small text-muted ps-3 mb-0">
                        <li data-i18n="host.qr_flow_step_1">Misafirin QR kodunu okut.</li>
                        <li data-i18n="host.qr_flow_step_2">Rezervasyon bilgilerini kontrol et.</li>
                        <li data-i18n="host.qr_flow_step_3">Durumu "Geldi" veya "Tamamlandı" olarak güncelle.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
