<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role(['host']);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz istek.');
    redirect('../host-tables.php');
}

$action = $_POST['action'] ?? '';
$restaurant = get_host_restaurant((string) current_user_email());

if (!$restaurant) {
    flash('danger', 'Restoran kaydı bulunamadı.');
    redirect('../host-tables.php');
}

try {
    if ($action === 'create') {
        $tableNumber = clean_input($_POST['table_number'] ?? '');
        $capacity = (int) ($_POST['capacity'] ?? 0);
        $location = clean_input($_POST['location'] ?? '');
        $description = clean_input($_POST['description'] ?? '');

        if ($tableNumber === '' || !preg_match('/^[A-Za-z0-9\- ]{1,20}$/', $tableNumber) || $capacity < 1 || $capacity > 20 || mb_strlen($location) > 80 || mb_strlen($description) > 255) {
            throw new RuntimeException('Masa numarası ve kapasite zorunludur.');
        }

        $stmt = $pdo->prepare('INSERT INTO `tables` (restaurant_id, table_number, capacity, location, is_active, description) VALUES (:restaurant_id, :table_number, :capacity, :location, 1, :description)');
        $stmt->execute([
            ':restaurant_id' => (int) $restaurant['id'],
            ':table_number' => $tableNumber,
            ':capacity' => $capacity,
            ':location' => $location,
            ':description' => $description,
        ]);
        flash('success', 'Masa eklendi.');
    } elseif ($action === 'toggle') {
        $stmt = $pdo->prepare('UPDATE `tables` SET is_active = IF(is_active = 1, 0, 1) WHERE id = :id AND restaurant_id = :restaurant_id');
        $stmt->execute([
            ':id' => (int) ($_POST['table_id'] ?? 0),
            ':restaurant_id' => (int) $restaurant['id'],
        ]);
        flash('success', 'Masa durumu güncellendi.');
    } elseif ($action === 'update') {
        $tableNumber = clean_input($_POST['table_number'] ?? '');
        $capacity = (int) ($_POST['capacity'] ?? 0);
        $location = clean_input($_POST['location'] ?? '');
        $description = clean_input($_POST['description'] ?? '');

        if ($tableNumber === '' || !preg_match('/^[A-Za-z0-9\- ]{1,20}$/', $tableNumber) || $capacity < 1 || $capacity > 20 || mb_strlen($location) > 80 || mb_strlen($description) > 255) {
            throw new RuntimeException('Masa güncelleme bilgileri geçersiz.');
        }

        $stmt = $pdo->prepare('UPDATE `tables` SET table_number = :table_number, capacity = :capacity, location = :location, description = :description WHERE id = :id AND restaurant_id = :restaurant_id');
        $stmt->execute([
            ':table_number' => $tableNumber,
            ':capacity' => $capacity,
            ':location' => $location,
            ':description' => $description,
            ':id' => (int) ($_POST['table_id'] ?? 0),
            ':restaurant_id' => (int) $restaurant['id'],
        ]);
        flash('success', 'Masa güncellendi.');
    } else {
        throw new RuntimeException('Masa işlemi geçersiz.');
    }

    log_event('info', 'Host masa işlemi', ['action' => $action, 'owner_email' => current_user_email()]);
} catch (Throwable $exception) {
    log_event('error', 'Host masa işlemi hatası', ['error' => $exception->getMessage()]);
    flash('danger', 'Masa işlemi tamamlanamadı.');
}

redirect('../host-tables.php');
