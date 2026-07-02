<?php
$pageTitle = 'Partner Bilgisi';
$activePage = 'partner';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="admin-hero mb-4">
    <div>
        <span class="section-eyebrow">Partner Bilgi Merkezi</span>
        <h1>Reserve Partnerlik Detayları</h1>
        <p>Reserve partnerliği ile restoranını görünür hale getirir, rezervasyonlarını tek noktadan yönetir ve menünü dijital olarak güncel tutarsın. Bu sayfada başvuru kapsamını, gerekli bilgileri ve partner deneyimini adım adım görebilirsin.</p>
    </div>
    <a href="<?= e(BASE_URL) ?>/views/become_partner.php" class="btn btn-primary">
        <i class="bi bi-shop-window me-2"></i>Partner Formuna Git
    </a>
</section>

<div class="row g-4">
    <div class="col-lg-3">
        <div class="card host-panel-card h-100">
            <div class="card-body">
                <h2 class="h5">1. Başvuru</h2>
                <p class="text-muted mb-0">Restoran adı, iletişim kişisi, telefon, e-posta ve adres bilgilerini eksiksiz doldur. Mutfak türü, çalışma saatleri ve açıklama alanı profilinin temelini oluşturur.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card host-panel-card h-100">
            <div class="card-body">
                <h2 class="h5">2. Profil Hazırlığı</h2>
                <p class="text-muted mb-0">Mekan görseli ve açıklama metni ne kadar net olursa, ziyaretçiler restoranını o kadar kolay keşfeder. Menü kategorileri ve ürünleri sonrasında kolayca eklenebilir.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card host-panel-card h-100">
            <div class="card-body">
                <h2 class="h5">3. Sonuç Bildirimi</h2>
                <p class="text-muted mb-0">Başvurunun sonucu kayıtlı e-posta adresine iletilir. Bu nedenle başvuru sırasında aktif kullandığın bir e-posta yazman önerilir.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card host-panel-card h-100">
            <div class="card-body">
                <h2 class="h5">4. Host Panel Başlangıcı</h2>
                <p class="text-muted mb-0">Aktivasyon sonrası restoran e-postanla Host Panel’e giriş yapabilir; rezervasyonlar, masalar, menü ve QR akışını ayrı sekmelerden yönetebilirsin.</p>
            </div>
        </div>
    </div>
</div>

<section class="card host-panel-card mt-4">
    <div class="card-body p-4">
        <h2 class="h5 mb-3">Partner Avantajları</h2>
        <ul class="mb-4">
            <li>Rezervasyon, masa, menü ve QR yönetimini tek bir panelden yürütme</li>
            <li>Menü ürünlerinde fiyat, açıklama, kategori, görsel ve stok/aktiflik kontrolü</li>
            <li>Restoran vitrininde tutarlı profil, çalışma saati ve iletişim görünürlüğü</li>
            <li>Mobilden ve masaüstünden kullanılabilen hızlı, Bootstrap tabanlı yönetim ekranları</li>
        </ul>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= e(BASE_URL) ?>/views/become_partner.php" class="btn btn-primary">Partner Başvuru Formu</a>
            <a href="<?= e(BASE_URL) ?>/views/partner_contact.php" class="btn btn-outline-primary">İletişime Geç</a>
        </div>
    </div>
</section>

<section class="card host-panel-card mt-4">
    <div class="card-body p-4">
        <h2 class="h5 mb-3">Sık Sorulan Sorular</h2>
        <div class="accordion" id="partnerFaq">
            <div class="accordion-item">
                <h3 class="accordion-header" id="faqOneHeading">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne" aria-expanded="true" aria-controls="faqOne">
                        Başvuru için hangi bilgiler gerekli?
                    </button>
                </h3>
                <div id="faqOne" class="accordion-collapse collapse show" aria-labelledby="faqOneHeading" data-bs-parent="#partnerFaq">
                    <div class="accordion-body">
                        Restoran adı, yetkili kişi, e-posta, telefon, açık adres, mutfak türü, açıklama, çalışma saatleri ve görsel bağlantısı yeterlidir.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header" id="faqTwoHeading">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo" aria-expanded="false" aria-controls="faqTwo">
                        Başvuru sonrası hesabımı nasıl kullanırım?
                    </button>
                </h3>
                <div id="faqTwo" class="accordion-collapse collapse" aria-labelledby="faqTwoHeading" data-bs-parent="#partnerFaq">
                    <div class="accordion-body">
                        Süreç tamamlandığında restoran e-postan ile giriş yaparak Host Panel üzerinden operasyonlarını yönetebilirsin.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header" id="faqThreeHeading">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqThree" aria-expanded="false" aria-controls="faqThree">
                        Menü ve fiyat güncellemeleri anlık mı?
                    </button>
                </h3>
                <div id="faqThree" class="accordion-collapse collapse" aria-labelledby="faqThreeHeading" data-bs-parent="#partnerFaq">
                    <div class="accordion-body">
                        Evet. Host Panel’de yaptığın menü güncellemeleri kaydedildiği anda restoran detayında güncel olarak görüntülenir.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
