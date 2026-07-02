<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz istek.');
    redirect('../admin-restaurants.php');
}

ensure_partner_applications_table();
ensure_restaurant_location_columns();

$applicationId = (int) ($_POST['application_id'] ?? 0);
$action = clean_input($_POST['action'] ?? '');
$reviewNotes = clean_input($_POST['review_notes'] ?? '');

if ($applicationId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
    flash('danger', 'Başvuru işlemi geçersiz.');
    redirect('../admin-restaurants.php');
}

if (mb_strlen($reviewNotes) > 500) {
    flash('danger', 'İnceleme notu en fazla 500 karakter olabilir.');
    redirect('../admin-restaurants.php');
}

try {
    if ($action === 'approve') {
        $pdo->beginTransaction();

        $appStmt = $pdo->prepare(
            "SELECT * FROM restaurant_partner_applications
             WHERE id = :id AND status = 'pending'
             LIMIT 1
             FOR UPDATE"
        );
        $appStmt->execute([':id' => $applicationId]);
        $application = $appStmt->fetch();

        if (!$application) {
            throw new RuntimeException('Onaylanacak bekleyen başvuru bulunamadı.');
        }

        $email = (string) $application['restaurant_email'];
        $contactName = (string) $application['contact_name'];

        $userStmt = $pdo->prepare('SELECT id, role FROM users WHERE email = :email LIMIT 1');
        $userStmt->execute([':email' => $email]);
        $user = $userStmt->fetch();

        if ($user && $user['role'] === 'admin') {
            throw new RuntimeException('Admin e-postasi host hesabina donusturulemez.');
        }

        if ($user) {
            $updateUserStmt = $pdo->prepare(
                'UPDATE users SET role = :role, full_name = :full_name WHERE email = :email'
            );
            $updateUserStmt->execute([
                ':role' => 'host',
                ':full_name' => $contactName,
                ':email' => $email,
            ]);
        } else {
            $insertUserStmt = $pdo->prepare(
                'INSERT INTO users (email, full_name, password_hash, role, created_date)
                 VALUES (:email, :full_name, :password_hash, :role, NOW())'
            );
            $insertUserStmt->execute([
                ':email' => $email,
                ':full_name' => $contactName,
                ':password_hash' => (string) $application['password_hash'],
                ':role' => 'host',
            ]);
        }

        $restaurantStmt = $pdo->prepare(
            'INSERT INTO restaurants
            (name, description, cuisine_type, city, district, neighborhood, address, phone, price_range, cover_image, opening_time, closing_time, reservation_duration_minutes, owner_email, status, is_featured, rating, total_reservations)
             VALUES
            (:name, :description, :cuisine_type, :city, :district, :neighborhood, :address, :phone, :price_range, :cover_image, :opening_time, :closing_time, :reservation_duration_minutes, :owner_email, :status, :is_featured, :rating, :total_reservations)'
        );
        $restaurantStmt->execute([
            ':name' => (string) $application['restaurant_name'],
            ':description' => (string) $application['description'],
            ':cuisine_type' => (string) $application['cuisine_type'],
            ':city' => (string) $application['city'],
            ':district' => (string) ($application['district'] ?? ''),
            ':neighborhood' => (string) ($application['neighborhood'] ?? ''),
            ':address' => (string) $application['address'],
            ':phone' => (string) $application['phone'],
            ':price_range' => '$$',
            ':cover_image' => !empty($application['image_url']) ? (string) $application['image_url'] : null,
            ':opening_time' => (string) $application['opening_time'],
            ':closing_time' => (string) $application['closing_time'],
            ':reservation_duration_minutes' => 90,
            ':owner_email' => $email,
            ':status' => 'approved',
            ':is_featured' => 0,
            ':rating' => 0,
            ':total_reservations' => 0,
        ]);
        $restaurantId = (int) $pdo->lastInsertId();

        $categorySeedStmt = $pdo->prepare(
            'INSERT INTO menu_categories (restaurant_id, name, display_order)
             VALUES (:restaurant_id, :name, :display_order)'
        );
        $defaultCategories = [
            ['name' => 'Starters', 'display_order' => 1],
            ['name' => 'Main Courses', 'display_order' => 2],
            ['name' => 'Desserts', 'display_order' => 3],
            ['name' => 'Drinks', 'display_order' => 4],
        ];
        foreach ($defaultCategories as $category) {
            $categorySeedStmt->execute([
                ':restaurant_id' => $restaurantId,
                ':name' => $category['name'],
                ':display_order' => $category['display_order'],
            ]);
        }

        $tableSeedStmt = $pdo->prepare(
            'INSERT INTO `tables` (restaurant_id, table_number, capacity, location, is_active, description)
             VALUES (:restaurant_id, :table_number, :capacity, :location, :is_active, :description)'
        );
        $defaultTables = [
            ['table_number' => 'A1', 'capacity' => 2, 'location' => 'Salon', 'description' => 'İki kişilik masa'],
            ['table_number' => 'A2', 'capacity' => 4, 'location' => 'Salon', 'description' => 'Dört kişilik masa'],
            ['table_number' => 'B1', 'capacity' => 6, 'location' => 'Bahçe', 'description' => 'Altı kişilik masa'],
        ];
        foreach ($defaultTables as $table) {
            $tableSeedStmt->execute([
                ':restaurant_id' => $restaurantId,
                ':table_number' => $table['table_number'],
                ':capacity' => $table['capacity'],
                ':location' => $table['location'],
                ':is_active' => 1,
                ':description' => $table['description'],
            ]);
        }

        $updateAppStmt = $pdo->prepare(
            'UPDATE restaurant_partner_applications
             SET status = :status,
                 linked_restaurant_id = :linked_restaurant_id,
                 review_notes = :review_notes,
                 reviewed_by_email = :reviewed_by_email,
                 reviewed_at = NOW()
             WHERE id = :id'
        );
        $updateAppStmt->execute([
            ':status' => 'approved',
            ':linked_restaurant_id' => $restaurantId,
            ':review_notes' => $reviewNotes !== '' ? $reviewNotes : null,
            ':reviewed_by_email' => (string) current_user_email(),
            ':id' => $applicationId,
        ]);

        $pdo->commit();
        flash('success', 'Başvuru onaylandi ve host hesabi oluşturuldu.');
        log_event('info', 'Partner başvurusu onaylandi', [
            'application_id' => $applicationId,
            'restaurant_id' => $restaurantId,
            'restaurant_email' => $email,
        ]);
    } else {
        $stmt = $pdo->prepare(
            "UPDATE restaurant_partner_applications
             SET status = :status,
                 review_notes = :review_notes,
                 reviewed_by_email = :reviewed_by_email,
                 reviewed_at = NOW()
             WHERE id = :id AND status = 'pending'"
        );
        $stmt->execute([
            ':status' => 'rejected',
            ':review_notes' => $reviewNotes !== '' ? $reviewNotes : null,
            ':reviewed_by_email' => (string) current_user_email(),
            ':id' => $applicationId,
        ]);

        if ($stmt->rowCount() < 1) {
            throw new RuntimeException('Reddedilecek bekleyen başvuru bulunamadı.');
        }

        flash('success', 'Başvuru reddedildi.');
        log_event('info', 'Partner başvurusu reddedildi', [
            'application_id' => $applicationId,
        ]);
    }
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    log_event('error', 'Partner başvuru admin işlemi hatası', ['error' => $exception->getMessage()]);
    flash('danger', $exception->getMessage());
}

redirect('../admin-restaurants.php');
