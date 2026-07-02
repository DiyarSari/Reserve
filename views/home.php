<?php
$pageTitle = 'Home';
$activePage = 'home';
$mainClass = 'site-main';
require_once __DIR__ . '/../includes/header.php';
$featuredRestaurants = get_featured_restaurants(7);
$latestApprovedStmt = $pdo->prepare("SELECT * FROM restaurants WHERE status = 'approved' ORDER BY id DESC LIMIT 1");
$latestApprovedStmt->execute();
$latestApprovedRestaurant = $latestApprovedStmt->fetch();
if ($latestApprovedRestaurant) {
    $alreadyListed = false;
    foreach ($featuredRestaurants as $featuredRestaurant) {
        if ((int) $featuredRestaurant['id'] === (int) $latestApprovedRestaurant['id']) {
            $alreadyListed = true;
            break;
        }
    }
    if (!$alreadyListed) {
        array_unshift($featuredRestaurants, $latestApprovedRestaurant);
    }
}
$featuredRestaurants = array_slice($featuredRestaurants, 0, 8);
?>
<section class="home-clean-hero">
    <div class="container">
        <div class="home-clean-hero-content">
            <div class="col-xl-8 col-lg-9 mx-auto text-center">
                <h1 data-i18n="hero.title">M&uuml;kemmel Masan&#305; Ay&#305;rt</h1>
                <p class="home-clean-hero-text" data-i18n="hero.subtitle">Pop&uuml;ler restoranlar&#305; ke&#351;fet, men&uuml;leri incele ve saniyeler i&ccedil;inde rezervasyon yap.</p>
                <div class="home-clean-hero-actions">
                    <a href="<?= e(BASE_URL) ?>/views/restaurants.php" class="btn btn-reserve-light"><i class="bi bi-search"></i> <span data-i18n="hero.find">Restoranlar&#305; Ke&#351;fet</span></a>
                    <a href="<?= e(BASE_URL) ?>/views/restaurants.php" class="btn btn-reserve"><i class="bi bi-calendar2-check"></i> <span data-i18n="hero.reserve">Rezervasyon Yap</span></a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="home-clean-featured">
    <div class="container">
        <div class="home-clean-heading">
            <div>
                <span class="home-clean-eyebrow" data-i18n="home.featured_eyebrow">Pop&uuml;ler Restoranlar</span>
                <h2 class="section-title mb-0" data-i18n="home.popular">Bu Haftan&#305;n Favorileri</h2>
                <p class="text-secondary mb-0" data-i18n="home.featured_text">En y&uuml;ksek puanl&#305; mekanlar&#305; ke&#351;fet ve h&#305;zl&#305;ca rezervasyon yap.</p>
            </div>
            <a href="<?= e(BASE_URL) ?>/views/restaurants.php" class="btn btn-outline-primary px-4" data-i18n="home.view_all">T&uuml;m Restoranlar</a>
        </div>

        <div class="row g-4">
            <?php foreach ($featuredRestaurants as $restaurant): ?>
                <div class="col-sm-6 col-lg-3">
                    <article class="card home-clean-card h-100">
                        <div class="home-clean-card-image">
                            <img src="<?= e(restaurant_image_url($restaurant)) ?>" alt="<?= e($restaurant['name']) ?>" loading="lazy" onerror="this.onerror=null;this.src='<?= e(restaurant_image_fallback_url($restaurant)) ?>';">
                            <span class="rating-badge home-rating-badge"><i class="bi bi-star-fill"></i> <?= e((string) $restaurant['rating']) ?></span>
                        </div>
                        <div class="card-body">
                            <h3><?= e($restaurant['name']) ?></h3>
                            <p>
                                <i class="bi bi-geo-alt"></i> <?= e($restaurant['city']) ?>
                                <span><?= e($restaurant['cuisine_type']) ?></span>
                            </p>
                            <a href="<?= e(BASE_URL) ?>/views/restaurant_detail.php?id=<?= (int) $restaurant['id'] ?>" class="btn btn-reserve w-100" data-i18n="cards.reserve_table">Rezervasyon Yap</a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
            <?php if (empty($featuredRestaurants)): ?>
                <div class="col-12">
                    <div class="empty-state text-center py-5">
                        <i class="bi bi-shop display-5 text-success"></i>
                        <p class="mt-3 mb-0 text-secondary" data-i18n="home.no_restaurants">Hen&uuml;z restoran bulunmuyor.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
