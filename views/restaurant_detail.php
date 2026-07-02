<?php
$pageTitle = 'Restoran Detay';
require_once __DIR__ . '/../includes/header.php';
$restaurantId = (int) ($_GET['id'] ?? 0);
$restaurant = get_restaurant($restaurantId);
if (!$restaurant || $restaurant['status'] !== 'approved') {
    http_response_code(404);
    exit('Restoran bulunamadı.');
}
$guestCount = max(1, min(10, (int) ($_GET['guest_count'] ?? 1)));
$tables = get_available_tables($restaurantId, $guestCount);
$maxCapacity = 1;
foreach (get_available_tables($restaurantId, 1) as $table) {
    $maxCapacity = min(10, max($maxCapacity, (int) $table['capacity']));
}
$menu = get_restaurant_menu($restaurantId);
$user = current_user();
$reviewSummary = get_restaurant_review_summary($restaurantId);
$displayRating = $reviewSummary['count'] > 0 ? $reviewSummary['average'] : (float) $restaurant['rating'];
?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="restaurant-detail-card">
            <div class="restaurant-detail-top">
                <div>
                    <h1><?= e($restaurant['name']) ?></h1>
                    <p><?= e($restaurant['city']) ?> | <?= e($restaurant['cuisine_type']) ?></p>
                </div>
                <div class="rating-display rating-display-large" aria-label="Restaurant rating">
                    <span class="rating-display-score"><i class="bi bi-star-fill"></i> <?= e(number_format($displayRating, 2)) ?></span>
                    <span class="rating-display-label"><?= $reviewSummary['count'] > 0 ? (int) $reviewSummary['count'] . ' yorum' : 'Platform puanı' ?></span>
                </div>
            </div>
            <p><?= e($restaurant['description']) ?></p>
            <dl class="row">
                <dt class="col-sm-4">Adres</dt><dd class="col-sm-8"><?= e($restaurant['address']) ?></dd>
                <dt class="col-sm-4">Telefon</dt><dd class="col-sm-8"><?= e($restaurant['phone']) ?></dd>
                <dt class="col-sm-4">Saatler</dt><dd class="col-sm-8"><?= e(substr($restaurant['opening_time'], 0, 5)) ?> - <?= e(substr($restaurant['closing_time'], 0, 5)) ?></dd>
            </dl>
            <div class="rating-info-box">
                <i class="bi bi-stars"></i>
                <div>
                    <strong>Puanlama nasıl yapılır?</strong>
                    <span>Rezervasyon yaptıktan sonra "Rezervasyonlarım" sayfasından restoran deneyimini 1-5 yıldızla puanlayabilirsin.</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h2 class="h5 mb-3">Rezervasyon oluştur</h2>
                <form action="<?= e(BASE_URL) ?>/controllers/reservation_process.php" method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="restaurant_id" value="<?= (int) $restaurant['id'] ?>">
                    <div class="mb-2">
                        <label class="form-label" for="customer_name">Ad Soyad</label>
                        <input class="form-control" id="customer_name" name="customer_name" value="<?= e($user['full_name'] ?? '') ?>" required minlength="3" maxlength="150" autocomplete="name">
                        <div class="invalid-feedback">Ad soyad en az 3 karakter olmalıdır.</div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="customer_email">E-posta</label>
                        <input type="email" class="form-control" id="customer_email" name="customer_email" value="<?= e($user['email'] ?? '') ?>" required maxlength="190" autocomplete="email">
                        <div class="invalid-feedback">Geçerli bir e-posta girin.</div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="customer_phone">Telefon</label>
                        <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required minlength="10" maxlength="13" pattern="(\+90|0)?[1-9][0-9]{9}" data-validate-phone-tr="1" inputmode="tel" placeholder="+905551112233" autocomplete="tel">
                        <div class="invalid-feedback">Geçerli bir Türkiye telefon numarası girin.</div>
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <label class="form-label" for="guest_count">Kişi</label>
                            <input type="number" min="1" max="10" class="form-control" id="guest_count" name="guest_count" value="<?= $guestCount ?>" required>
                            <div class="invalid-feedback">Kişi sayısı 1 ile 10 arasında olmalıdır.</div>
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="reservation_date">Tarih</label>
                            <input type="date" class="form-control" id="reservation_date" name="reservation_date" min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+60 days')) ?>" required>
                            <div class="invalid-feedback">Bugün veya ileri bir tarih seçin.</div>
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="reservation_time">Saat</label>
                            <input type="time" class="form-control" id="reservation_time" name="reservation_time" min="<?= e(substr($restaurant['opening_time'], 0, 5)) ?>" max="<?= e(substr($restaurant['closing_time'], 0, 5)) ?>" step="1800" required>
                            <div class="invalid-feedback">Restoran çalışma saatleri içinde 30 dakikalık aralıklarla saat seçin.</div>
                        </div>
                    </div>
                    <div class="mb-3 mt-2">
                        <label class="form-label" for="notes">Not</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" maxlength="500"></textarea>
                        <div class="form-text">En fazla 500 karakter.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" <?= empty($tables) ? 'disabled' : '' ?>>Rezervasyon yap</button>
                    <?php if (empty($tables)): ?>
                        <p class="small text-danger mt-2 mb-0">Bu kişi sayısı için aktif masa bulunmuyor.</p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<section class="menu-section mt-5" id="menu">
    <div class="menu-section-head">
        <div>
            <span class="eyebrow dark" data-i18n="menu.eyebrow">Restoran Menüsü</span>
            <h2 class="section-title mb-0" data-i18n="menu.title">Menü</h2>
            <p class="menu-section-lead">Bu menü <?= e($restaurant['name']) ?> konseptine ve <?= e($restaurant['cuisine_type']) ?> mutfağına göre hazırlandı.</p>
        </div>
        <span class="menu-note-pill" data-i18n="menu.price_note">Fiyatlar TL olarak gösterilir.</span>
    </div>

    <?php if (empty($menu)): ?>
        <div class="bg-white border rounded-2 p-4 text-muted" data-i18n="menu.empty">Bu restoran henüz menü eklemedi.</div>
    <?php else: ?>
        <div class="menu-category-list">
            <?php foreach ($menu as $category): ?>
                <article class="menu-category">
                    <?php $categoryKey = menu_category_i18n_key((string) $category['name']); ?>
                    <div class="menu-category-head">
                        <div>
                            <h3 <?= $categoryKey ? 'data-i18n="' . e($categoryKey) . '"' : '' ?>><?= e($category['name']) ?></h3>
                            <p><?= e(menu_category_description((string) $category['name'], (string) $restaurant['cuisine_type'])) ?></p>
                        </div>
                        <span><?= count($category['items']) ?> seçenek</span>
                    </div>
                    <?php if (empty($category['items'])): ?>
                        <p class="text-muted mb-0" data-i18n="menu.category_empty">Bu kategoride henüz ürün yok.</p>
                    <?php else: ?>
                        <div class="menu-item-grid">
                            <?php foreach ($category['items'] as $item): ?>
                                <div class="menu-item-card">
                                    <?php if (!empty($item['image_url'])): ?>
                                        <?php
                                            $menuImageUrl = (string) $item['image_url'];
                                            if (!preg_match('#^(https?:)?//#', $menuImageUrl) && strpos($menuImageUrl, 'data:') !== 0) {
                                                $menuImageUrl = rtrim(BASE_URL, '/') . '/' . ltrim($menuImageUrl, '/');
                                            }
                                        ?>
                                        <img src="<?= e($menuImageUrl) ?>" alt="<?= e($item['name']) ?>" class="menu-item-image">
                                    <?php else: ?>
                                        <div class="menu-item-placeholder"><i class="bi bi-egg-fried"></i></div>
                                    <?php endif; ?>
                                    <div class="menu-item-content">
                                        <div class="d-flex justify-content-between gap-3">
                                            <?php $nameKey = menu_item_i18n_key((string) $item['name'], 'name'); ?>
                                            <h4 <?= $nameKey ? 'data-i18n="' . e($nameKey) . '"' : '' ?>><?= e($item['name']) ?></h4>
                                            <strong class="menu-price"><?= e(format_price((float) $item['price'])) ?></strong>
                                        </div>
                                        <?php $descriptionKey = menu_item_i18n_key((string) $item['name'], 'description'); ?>
                                        <p <?= $descriptionKey ? 'data-i18n="' . e($descriptionKey) . '"' : '' ?>><?= e($item['description']) ?></p>
                                        <span class="menu-item-meta"><i class="bi bi-check2-circle"></i> Mekana özel hazırlandı</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
