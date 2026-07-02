<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role(['host']);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Geçersiz istek.');
    redirect('../host-menu.php');
}

$restaurant = get_host_restaurant((string) current_user_email());
if (!$restaurant) {
    flash('danger', 'Restoran kaydı bulunamadı.');
    redirect('../host-menu.php');
}

$restaurantId = (int) $restaurant['id'];
$action = $_POST['action'] ?? '';

try {
    if ($action === 'add_category') {
        $name = clean_input($_POST['category_name'] ?? '');
        $displayOrder = (int) ($_POST['display_order'] ?? 0);

        if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
            throw new RuntimeException('Kategori adı 2 ile 100 karakter arasında olmalıdır.');
        }

        $stmt = $pdo->prepare('INSERT INTO menu_categories (restaurant_id, name, display_order) VALUES (:restaurant_id, :name, :display_order)');
        $stmt->execute([
            ':restaurant_id' => $restaurantId,
            ':name' => $name,
            ':display_order' => $displayOrder,
        ]);
        flash('success', 'Menü kategorisi eklendi.');
    } elseif ($action === 'add_item' || $action === 'update_item') {
        $itemId = (int) ($_POST['item_id'] ?? 0);
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $name = clean_input($_POST['item_name'] ?? '');
        $description = clean_input($_POST['description'] ?? '');
        $price = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
        $imageUrl = filter_var($_POST['image_url'] ?? '', FILTER_SANITIZE_URL);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($categoryId <= 0 || mb_strlen($name) < 2 || mb_strlen($name) > 150 || mb_strlen($description) > 500 || $price === false || $price < 0 || $price > 1000000) {
            throw new RuntimeException('Menü urunu için kategori, ad ve geçerli fiyat zorunludur.');
        }

        if ($imageUrl !== '' && filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
            throw new RuntimeException('Gorsel URL geçersiz.');
        }

        $categoryCheck = $pdo->prepare('SELECT id FROM menu_categories WHERE id = :id AND restaurant_id = :restaurant_id');
        $categoryCheck->execute([
            ':id' => $categoryId,
            ':restaurant_id' => $restaurantId,
        ]);

        if (!$categoryCheck->fetch()) {
            throw new RuntimeException('Secilen kategori bu restorana ait değil.');
        }

        if ($action === 'add_item') {
            $stmt = $pdo->prepare(
                'INSERT INTO menu_items (restaurant_id, category_id, name, description, price, image_url, is_active)
                 VALUES (:restaurant_id, :category_id, :name, :description, :price, :image_url, :is_active)'
            );
            $stmt->execute([
                ':restaurant_id' => $restaurantId,
                ':category_id' => $categoryId,
                ':name' => $name,
                ':description' => $description,
                ':price' => $price,
                ':image_url' => $imageUrl,
                ':is_active' => $isActive,
            ]);
            flash('success', 'Menü urunu eklendi.');
        } else {
            $stmt = $pdo->prepare(
                'UPDATE menu_items
                 SET category_id = :category_id, name = :name, description = :description, price = :price, image_url = :image_url, is_active = :is_active
                 WHERE id = :id AND restaurant_id = :restaurant_id'
            );
            $stmt->execute([
                ':category_id' => $categoryId,
                ':name' => $name,
                ':description' => $description,
                ':price' => $price,
                ':image_url' => $imageUrl,
                ':is_active' => $isActive,
                ':id' => $itemId,
                ':restaurant_id' => $restaurantId,
            ]);
            flash('success', 'Menü urunu güncellendi.');
        }
    } elseif ($action === 'toggle_item') {
        $itemId = (int) ($_POST['item_id'] ?? 0);
        $stmt = $pdo->prepare(
            'UPDATE menu_items
             SET is_active = IF(is_active = 1, 0, 1)
             WHERE id = :id AND restaurant_id = :restaurant_id'
        );
        $stmt->execute([
            ':id' => $itemId,
            ':restaurant_id' => $restaurantId,
        ]);
        flash('success', 'Menü urunu durumu güncellendi.');
    } elseif ($action === 'delete_item') {
        $itemId = (int) ($_POST['item_id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM menu_items WHERE id = :id AND restaurant_id = :restaurant_id');
        $stmt->execute([
            ':id' => $itemId,
            ':restaurant_id' => $restaurantId,
        ]);
        flash('success', 'Menü urunu silindi.');
    } else {
        throw new RuntimeException('Menü işlemi geçersiz.');
    }

    log_event('info', 'Host menu işlemi', ['action' => $action, 'restaurant_id' => $restaurantId]);
} catch (Throwable $exception) {
    log_event('error', 'Host menu işlemi hatası', ['error' => $exception->getMessage()]);
    flash('danger', $exception->getMessage());
}

redirect('../host-menu.php');
