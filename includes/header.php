<?php
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? 'Reserve';
$mainClass = $mainClass ?? 'container py-4';
$allowStaffPublicAccess = $allowStaffPublicAccess ?? false;

enforce_staff_panel_scope((bool) $allowStaffPublicAccess);

if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | Reserve</title>
    <link rel="icon" type="image/svg+xml" href="<?= e(BASE_URL) ?>/assets/favicon.svg">
    <link rel="shortcut icon" href="<?= e(BASE_URL) ?>/assets/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php $styleVersion = filemtime(__DIR__ . '/../assets/css/style.css') ?: time(); ?>
    <link href="<?= e(BASE_URL) ?>/assets/css/style.css?v=<?= e((string) $styleVersion) ?>" rel="stylesheet">
    <?php $overrideStyleVersion = filemtime(__DIR__ . '/../assets/css/overrides.css') ?: time(); ?>
    <link href="<?= e(BASE_URL) ?>/assets/css/overrides.css?v=<?= e((string) $overrideStyleVersion) ?>" rel="stylesheet">
</head>
<body>
<?php require __DIR__ . '/navbar.php'; ?>
<main class="<?= e($mainClass) ?>">
    <?php $flashMessages = consume_flash(); ?>
    <?php if (!empty($flashMessages)): ?>
    <div class="container py-4">
    <?php foreach ($flashMessages as $message): ?>
        <div class="alert alert-<?= e($message['type']) ?> alert-dismissible fade show js-auto-alert" role="alert">
            <?= e($message['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
        </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
