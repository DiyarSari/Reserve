<?php
$pageTitle = 'Restoranlar';
$activePage = 'restaurants';
require_once __DIR__ . '/../includes/header.php';
$guestCount = (int) ($_GET['guest_count'] ?? 1);
if ($guestCount < 1 || $guestCount > 10) {
    $guestCount = 1;
}
$filters = [
    'q' => clean_input($_GET['q'] ?? ''),
    'city' => clean_input($_GET['city'] ?? ''),
    'district' => clean_input($_GET['district'] ?? ''),
    'cuisine_type' => clean_input($_GET['cuisine_type'] ?? ''),
    'guest_count' => (string) $guestCount,
];
$perPage = 9;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$totalRestaurants = count_restaurants($filters);
$totalPages = max(1, (int) ceil($totalRestaurants / $perPage));
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $perPage;
$restaurants = get_restaurants($filters, $perPage, $offset);
$cityOptions = get_restaurant_filter_options('city');
$districtOptions = get_district_filter_options($filters['city']);
$cuisineOptions = get_restaurant_filter_options('cuisine_type');
$filterMatrix = get_restaurant_filter_matrix();
$pageQuery = array_filter(
    $filters,
    static function ($value, $key): bool {
        if ($value === '') {
            return false;
        }
        if ($key === 'guest_count' && (string) $value === '1') {
            return false;
        }
        return true;
    },
    ARRAY_FILTER_USE_BOTH
);
?>
<section class="listing-hero">
    <div class="listing-hero-content">
        <span class="eyebrow" data-i18n="listing.eyebrow">Özenle seçilen restoranlar</span>
        <h1 data-i18n="listing.title">Sana uygun masayı bul</h1>
        <p data-i18n="listing.subtitle">Onaylı restoranları incele ve planına uygun bir masa ayırt.</p>
        <form method="get" class="listing-hero-search mt-4">
            <input type="hidden" name="city" value="<?= e($filters['city']) ?>">
            <input type="hidden" name="district" value="<?= e($filters['district']) ?>">
            <input type="hidden" name="cuisine_type" value="<?= e($filters['cuisine_type']) ?>">
            <input type="hidden" name="guest_count" value="<?= e($filters['guest_count']) ?>">
            <div class="input-group input-group-lg listing-search-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input
                    type="search"
                    class="form-control"
                    id="listingSearchInput"
                    name="q"
                    value="<?= e($filters['q']) ?>"
                    maxlength="120"
                    placeholder="Restoran adını yaz..."
                    data-i18n-placeholder="listing.search_placeholder"
                    aria-label="Restaurant search"
                    autocomplete="off"
                >
                <button class="btn btn-light" type="submit" data-i18n="listing.search_button">Ara</button>
            </div>
        </form>
    </div>
</section>

<form method="get" class="filter-bar listing-filter-bar needs-validation" id="restaurantFilters" novalidate>
    <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
    <div class="filter-field">
        <label for="cityFilter"><i class="bi bi-geo-alt"></i> <span data-i18n="listing.city">Şehir</span></label>
        <select class="form-select js-restaurant-city-filter" id="cityFilter" name="city" aria-label="Şehir">
            <option value="" data-i18n="listing.city_all">Tüm şehirler</option>
            <?php foreach ($cityOptions as $city): ?>
                <option value="<?= e($city) ?>" <?= $filters['city'] === $city ? 'selected' : '' ?>><?= e($city) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-field">
        <label for="districtFilter"><i class="bi bi-pin-map"></i> <span data-i18n="listing.district">İlçe</span></label>
        <select class="form-select js-restaurant-district-filter" id="districtFilter" name="district" aria-label="İlçe">
            <option value="" data-i18n="listing.district_all">Tüm ilçeler</option>
            <?php foreach ($districtOptions as $district): ?>
                <option value="<?= e($district) ?>" <?= $filters['district'] === $district ? 'selected' : '' ?>><?= e($district) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-field">
        <label for="cuisineFilter"><i class="bi bi-egg-fried"></i> <span data-i18n="listing.cuisine">Mutfak türü</span></label>
        <select class="form-select js-restaurant-cuisine-filter" id="cuisineFilter" name="cuisine_type" aria-label="Mutfak türü">
            <option value="" data-i18n="listing.cuisine_all">Tüm mutfaklar</option>
            <?php foreach ($cuisineOptions as $cuisine): ?>
                <option value="<?= e($cuisine) ?>" <?= $filters['cuisine_type'] === $cuisine ? 'selected' : '' ?>><?= e($cuisine) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-field filter-field-small">
        <label for="guestFilter"><i class="bi bi-people"></i> <span data-i18n="listing.guests">Kişi</span></label>
        <select class="form-select js-restaurant-guest-filter" id="guestFilter" name="guest_count" aria-label="Kişi sayısı">
            <?php for ($guest = 1; $guest <= 10; $guest++): ?>
                <option value="<?= $guest ?>" <?= (string) $filters['guest_count'] === (string) $guest ? 'selected' : '' ?>><?= $guest ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <button class="btn btn-primary filter-submit" type="submit">
        <i class="bi bi-sliders"></i>
        <span data-i18n="listing.filter">Filtrele</span>
    </button>
</form>

<script type="application/json" id="restaurantFilterMatrix"><?= json_encode($filterMatrix, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?></script>

<div class="listing-section-heading">
    <div>
        <h2 data-i18n="listing.available_title">Uygun restoranlar</h2>
        <p data-i18n="listing.available_subtitle">Her kartta mekan bilgisi, menü erişimi ve rezervasyon akışı yer alır.</p>
    </div>
    <span data-i18n="listing.page_info" data-i18n-vars='{"page":"<?= $currentPage ?>","pages":"<?= $totalPages ?>"}'>Sayfa <?= $currentPage ?> / <?= $totalPages ?></span>
</div>

<div class="row g-4 restaurant-list-grid" id="restaurants-list">
    <?php foreach ($restaurants as $restaurant): ?>
        <div class="col-sm-6 col-lg-4">
            <article class="restaurant-card">
                <div class="restaurant-card-media">
                    <img src="<?= e(restaurant_image_url($restaurant)) ?>" alt="<?= e($restaurant['name']) ?>" class="restaurant-card-img" loading="lazy" onerror="this.onerror=null;this.src='<?= e(restaurant_image_fallback_url($restaurant)) ?>';">
                    <span class="rating-badge restaurant-card-rating"><i class="bi bi-star-fill"></i> <?= e((string) $restaurant['rating']) ?></span>
                    <span class="restaurant-card-city"><?= e($restaurant['city']) ?></span>
                </div>
                <div class="restaurant-card-body">
                    <h2><?= e($restaurant['name']) ?></h2>
                    <div class="restaurant-meta">
                        <span><i class="bi bi-egg-fried"></i> <?= e($restaurant['cuisine_type']) ?></span>
                        <span><i class="bi bi-geo-alt"></i> <?= e($restaurant['city']) ?></span>
                        <?php if (!empty($restaurant['district'])): ?>
                            <span><i class="bi bi-pin-map"></i> <?= e((string) $restaurant['district']) ?></span>
                        <?php endif; ?>
                    </div>
                    <p><?= e(mb_substr((string) $restaurant['description'], 0, 105)) ?></p>
                    <div class="restaurant-card-footnote">
                        <span><i class="bi bi-clock"></i> <?= e(substr($restaurant['opening_time'], 0, 5)) ?> - <?= e(substr($restaurant['closing_time'], 0, 5)) ?></span>
                        <span><i class="bi bi-card-list"></i> Menü</span>
                    </div>
                    <div class="restaurant-card-actions">
                        <a href="<?= e(BASE_URL) ?>/views/restaurant_detail.php?id=<?= (int) $restaurant['id'] ?>#menu" class="btn btn-outline-primary restaurant-card-cta" data-i18n="cards.view_menu">Menüyü Gör</a>
                        <a href="<?= e(BASE_URL) ?>/views/restaurant_detail.php?id=<?= (int) $restaurant['id'] ?>" class="btn btn-primary restaurant-card-cta" data-i18n="cards.reserve">Rezervasyon Yap</a>
                    </div>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
    <?php if (empty($restaurants)): ?>
        <div class="col-12">
            <div class="empty-state text-center py-5 bg-white border rounded-2">
                <i class="bi bi-search display-6 text-success"></i>
                <p class="mt-3 mb-0 text-secondary" data-i18n="listing.no_results">Bu filtrelere uygun restoran bulunamadı.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
    <nav class="restaurant-pagination" aria-label="Restoran sayfaları">
        <ul class="pagination justify-content-center">
            <?php
            $previousQuery = http_build_query(array_merge($pageQuery, ['page' => max(1, $currentPage - 1)]));
            $nextQuery = http_build_query(array_merge($pageQuery, ['page' => min($totalPages, $currentPage + 1)]));
            ?>
            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                <a class="page-link page-link-nav" href="?<?= e($previousQuery) ?>" aria-label="Önceki sayfa">
                    <i class="bi bi-chevron-left"></i>
                    <span data-i18n="listing.previous">Önceki</span>
                </a>
            </li>
            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                <?php $query = http_build_query(array_merge($pageQuery, ['page' => $page])); ?>
                <li class="page-item <?= $page === $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= e($query) ?>"><?= $page ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link page-link-nav" href="?<?= e($nextQuery) ?>" aria-label="Sonraki sayfa">
                    <span data-i18n="listing.next">Sonraki</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
        <p class="restaurant-pagination-info" data-i18n="listing.page_info" data-i18n-vars='{"page":"<?= $currentPage ?>","pages":"<?= $totalPages ?>"}'>Sayfa <?= $currentPage ?> / <?= $totalPages ?></p>
    </nav>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
