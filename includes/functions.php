<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function clean_input(?string $value): string
{
    return trim(filter_var((string) $value, FILTER_SANITIZE_SPECIAL_CHARS));
}

function normalize_tr_display_value(string $value): string
{
    $trimmed = trim($value);
    if ($trimmed === '') {
        return '';
    }

    static $map = [
        'istanbul' => 'İstanbul',
        'izmir' => 'İzmir',
        'ankara' => 'Ankara',
        'antalya' => 'Antalya',
        'bursa' => 'Bursa',
        'kadikoy' => 'Kadıköy',
        'cankaya' => 'Çankaya',
        'konak' => 'Konak',
        'muratpasa' => 'Muratpaşa',
        'osmangazi' => 'Osmangazi',
        'atasehir' => 'Ataşehir',
        'besiktas' => 'Beşiktaş',
        'sisli' => 'Şişli',
        'uskudar' => 'Üsküdar',
        'cekmekoy' => 'Çekmeköy',
        'turk mutfagi' => 'Türk Mutfağı',
        'dunya mutfagi' => 'Dünya Mutfağı',
        'deniz urunleri' => 'Deniz Ürünleri',
    ];

    $lookup = mb_strtolower($trimmed, 'UTF-8');
    return $map[$lookup] ?? $trimmed;
}

function normalize_tr_phone(string $phone): string
{
    $raw = trim($phone);
    if ($raw === '') {
        return '';
    }

    $raw = str_replace([' ', '(', ')', '-', '.'], '', $raw);

    if (str_starts_with($raw, '00')) {
        $raw = '+' . substr($raw, 2);
    }

    if (str_starts_with($raw, '+')) {
        $raw = '+' . preg_replace('/\D/', '', substr($raw, 1));
    } else {
        $raw = preg_replace('/\D/', '', $raw);
    }

    $local = '';
    if (str_starts_with($raw, '+90')) {
        $local = substr($raw, 3);
    } elseif (str_starts_with($raw, '90') && strlen($raw) === 12) {
        $local = substr($raw, 2);
    } elseif (str_starts_with($raw, '0') && strlen($raw) === 11) {
        $local = substr($raw, 1);
    } elseif (strlen($raw) === 10) {
        $local = $raw;
    }

    if (!preg_match('/^[1-9][0-9]{9}$/', $local)) {
        return '';
    }

    return '+90' . $local;
}

function is_valid_tr_phone(string $phone): bool
{
    return normalize_tr_phone($phone) !== '';
}

function is_valid_hhmm(string $time): bool
{
    return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time) === 1;
}

function is_valid_person_name(string $name, int $min = 2, int $max = 150): bool
{
    $trimmed = trim($name);
    $length = mb_strlen($trimmed);
    if ($length < $min || $length > $max) {
        return false;
    }

    return preg_match("/^[\p{L}\p{M}][\p{L}\p{M}\s'.-]*$/u", $trimmed) === 1;
}

function has_unsafe_payload_pattern(string $value): bool
{
    $text = trim($value);
    if ($text === '') {
        return false;
    }

    return preg_match(
        '/<\s*\/?\s*(script|iframe|object|embed|style|link|meta)\b|javascript\s*:|data\s*:\s*text\/html|on[a-z]+\s*=|<\?php|union\s+select|drop\s+table|truncate\s+table|(?:\'|")\s*(or|and)\s*(?:\'|")?\s*\d?/iu',
        $text
    ) === 1;
}

function should_skip_payload_guard_key(string $key): bool
{
    static $skipKeys = [
        'csrf_token',
        'password',
        'password_confirm',
        'current_password',
        'new_password',
        'confirm_password',
    ];

    return in_array($key, $skipKeys, true);
}

function collect_unsafe_payload_fields(array $payload, string $prefix = ''): array
{
    $unsafe = [];

    foreach ($payload as $key => $value) {
        $keyName = (string) $key;
        if (should_skip_payload_guard_key($keyName)) {
            continue;
        }

        $path = $prefix === '' ? $keyName : $prefix . '.' . $keyName;

        if (is_array($value)) {
            $unsafe = array_merge($unsafe, collect_unsafe_payload_fields($value, $path));
            continue;
        }

        if (!is_scalar($value)) {
            continue;
        }

        if (has_unsafe_payload_pattern((string) $value)) {
            $unsafe[] = $path;
        }
    }

    return $unsafe;
}

function enforce_post_payload_guard(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || empty($_POST)) {
        return;
    }

    $unsafeFields = collect_unsafe_payload_fields($_POST);
    if ($unsafeFields === []) {
        return;
    }

    log_event('warning', 'Unsafe payload blocked', [
        'fields' => $unsafeFields,
        'path' => $_SERVER['REQUEST_URI'] ?? '',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ]);

    flash('danger', 'Girdi güvenlik kontrolünden geçemedi. Lütfen yalnızca geçerli metin girin.');

    $fallback = BASE_URL . '/views/home.php';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if (is_string($referer) && $referer !== '') {
        $parsed = parse_url($referer);
        $refererPath = $parsed['path'] ?? '';
        if (is_string($refererPath) && str_starts_with($refererPath, BASE_URL . '/')) {
            $fallback = $refererPath;
            if (!empty($parsed['query'])) {
                $fallback .= '?' . $parsed['query'];
            }
        }
    }

    redirect($fallback);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function consume_flash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function log_event(string $level, string $message, array $context = []): void
{
    global $pdo;

    try {
        $stmt = $pdo->prepare('INSERT INTO system_logs (level, message, context, created_at) VALUES (:level, :message, :context, NOW())');
        $stmt->execute([
            ':level' => $level,
            ':message' => $message,
            ':context' => json_encode($context, JSON_UNESCAPED_UNICODE),
        ]);
    } catch (Throwable $exception) {
        error_log('Log write failed: ' . $exception->getMessage());
    }
}

function generate_reservation_code(): string
{
    return 'RSV-' . strtoupper(bin2hex(random_bytes(3))) . '-' . date('His');
}

function normalize_qr_token_input(string $token): string
{
    $cleaned = trim($token);
    if ($cleaned === '') {
        return '';
    }

    $cleaned = preg_replace('/[^A-Za-z0-9]/', '', $cleaned);
    return strtoupper((string) $cleaned);
}

function format_qr_token_for_display(string $token): string
{
    $normalized = normalize_qr_token_input($token);
    if ($normalized === '') {
        return '';
    }

    if (strlen($normalized) <= 16) {
        return implode('-', str_split($normalized, 4));
    }

    return $normalized;
}

function generate_qr_token(): string
{
    global $pdo;

    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $length = 12;

    for ($attempt = 0; $attempt < 12; $attempt++) {
        $token = '';
        for ($index = 0; $index < $length; $index++) {
            $token .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        $stmt = $pdo->prepare('SELECT 1 FROM reservations WHERE qr_token = :qr_token LIMIT 1');
        $stmt->execute([':qr_token' => $token]);
        if (!$stmt->fetchColumn()) {
            return $token;
        }
    }

    return strtoupper(bin2hex(random_bytes(8)));
}

function qr_code_url(string $token, int $size = 220): string
{
    $payload = rawurlencode($token);
    $safeSize = max(120, min(420, $size));
    return "https://api.qrserver.com/v1/create-qr-code/?size={$safeSize}x{$safeSize}&data={$payload}";
}

function validate_host_qr_token(string $token, string $ownerEmail): array
{
    global $pdo;
    auto_mark_no_show_reservations($ownerEmail);

    $token = normalize_qr_token_input($token);
    if ($token === '') {
        return ['valid' => false, 'message' => 'QR token boş olamaz.', 'reservation' => null];
    }

    $stmt = $pdo->prepare(
        'SELECT * FROM reservations
         WHERE qr_token IN (:qr_token_raw, :qr_token_upper, :qr_token_lower)
         LIMIT 1'
    );
    $stmt->execute([
        ':qr_token_raw' => $token,
        ':qr_token_upper' => strtoupper($token),
        ':qr_token_lower' => strtolower($token),
    ]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        return ['valid' => false, 'message' => 'Rezervasyon bulunamadı.', 'reservation' => null];
    }

    if ($reservation['owner_email'] !== $ownerEmail) {
        log_event('warning', 'Başka restorana ait QR okutuldu', [
            'owner_email' => $ownerEmail,
            'reservation_id' => $reservation['id'],
        ]);
        return ['valid' => false, 'message' => 'Bu QR kod bu restorana ait değil.', 'reservation' => $reservation];
    }

    if (in_array($reservation['status'], ['completed', 'cancelled', 'no_show'], true)) {
        return ['valid' => false, 'message' => 'Bu rezervasyon artık check-in için kullanılamaz.', 'reservation' => $reservation];
    }

    if ((string) ($reservation['status'] ?? '') === 'pending') {
        return ['valid' => false, 'message' => 'Önce host onayı verilmelidir.', 'reservation' => $reservation];
    }

    $reservationDate = (string) $reservation['reservation_date'];
    $reservationTime = substr((string) $reservation['reservation_time'], 0, 5);
    $reservationStart = strtotime($reservationDate . ' ' . $reservationTime);
    $validUntil = $reservationStart + (4 * 60 * 60);
    $today = date('Y-m-d');

    if ($reservationDate !== $today) {
        return ['valid' => false, 'message' => 'Rezervasyon bugüne ait değil.', 'reservation' => $reservation];
    }

    if (time() > $validUntil) {
        return ['valid' => false, 'message' => 'Rezervasyon süresi geçmiş.', 'reservation' => $reservation];
    }

    return ['valid' => true, 'message' => 'Rezervasyon doğrulandı.', 'reservation' => $reservation];
}

function confirm_host_qr_reservation(string $token, string $ownerEmail): ?array
{
    global $pdo;

    $validation = validate_host_qr_token($token, $ownerEmail);
    if (!$validation['valid'] || empty($validation['reservation'])) {
        return null;
    }

    $reservation = $validation['reservation'];
    $reservationId = (int) ($reservation['id'] ?? 0);
    if ($reservationId <= 0) {
        return null;
    }

    if ((string) ($reservation['status'] ?? '') === 'pending') {
        $updateStmt = $pdo->prepare(
            'UPDATE reservations
             SET status = :new_status
             WHERE id = :id AND owner_email = :owner_email AND status = :current_status'
        );
        $updateStmt->execute([
            ':new_status' => 'confirmed',
            ':id' => $reservationId,
            ':owner_email' => $ownerEmail,
            ':current_status' => 'pending',
        ]);
    }

    $fetchStmt = $pdo->prepare('SELECT * FROM reservations WHERE id = :id AND owner_email = :owner_email LIMIT 1');
    $fetchStmt->execute([
        ':id' => $reservationId,
        ':owner_email' => $ownerEmail,
    ]);
    $updatedReservation = $fetchStmt->fetch();

    return $updatedReservation ?: null;
}

function update_reservation_status_by_qr(string $token, string $ownerEmail, string $status): bool
{
    global $pdo;

    $normalizedToken = normalize_qr_token_input($token);
    if ($normalizedToken === '') {
        return false;
    }

    $allowedStatuses = ['completed'];
    if (!in_array($status, $allowedStatuses, true)) {
        return false;
    }

    $validation = validate_host_qr_token($normalizedToken, $ownerEmail);
    if (!$validation['valid']) {
        return false;
    }

    $reservationId = (int) ($validation['reservation']['id'] ?? 0);
    if ($reservationId <= 0) {
        return false;
    }

    $currentStatus = (string) ($validation['reservation']['status'] ?? '');
    if ($status === 'completed' && $currentStatus !== 'confirmed') {
        return false;
    }

    $stmt = $pdo->prepare('UPDATE reservations SET status = :status WHERE id = :id AND owner_email = :owner_email');
    $stmt->execute([
        ':status' => $status,
        ':id' => $reservationId,
        ':owner_email' => $ownerEmail,
    ]);

    return $stmt->rowCount() > 0;
}

function ensure_table_column(string $table, string $column, string $definition): void
{
    global $pdo;

    $tableStmt = $pdo->prepare(
        'SELECT 1
         FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name
         LIMIT 1'
    );
    $tableStmt->execute([':table_name' => $table]);
    if (!$tableStmt->fetchColumn()) {
        return;
    }

    $stmt = $pdo->prepare(
        'SELECT 1
         FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = :table_name
           AND COLUMN_NAME = :column_name
         LIMIT 1'
    );
    $stmt->execute([
        ':table_name' => $table,
        ':column_name' => $column,
    ]);
    if ($stmt->fetchColumn()) {
        return;
    }

    $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN {$column} {$definition}");
}

function ensure_restaurant_location_columns(): void
{
    static $initialized = false;
    if ($initialized) {
        return;
    }

    global $pdo;

    try {
        ensure_table_column('restaurants', 'district', "VARCHAR(80) NOT NULL DEFAULT '' AFTER city");
        ensure_table_column('restaurants', 'neighborhood', "VARCHAR(120) NOT NULL DEFAULT '' AFTER district");

        $pdo->exec(
            "UPDATE restaurants
             SET
                city = CASE
                    WHEN city = 'Istanbul' THEN 'İstanbul'
                    WHEN city = 'Izmir' THEN 'İzmir'
                    ELSE city
                END,
                district = CASE
                    WHEN district = 'Kadikoy' THEN 'Kadıköy'
                    WHEN district = 'Cankaya' THEN 'Çankaya'
                    WHEN district = 'Muratpasa' THEN 'Muratpaşa'
                    WHEN district = 'Atasehir' THEN 'Ataşehir'
                    WHEN district = 'Besiktas' THEN 'Beşiktaş'
                    WHEN district = 'Sisli' THEN 'Şişli'
                    WHEN district = 'Uskudar' THEN 'Üsküdar'
                    WHEN district = 'Cekmekoy' THEN 'Çekmeköy'
                    ELSE district
                END,
                cuisine_type = CASE
                    WHEN cuisine_type = 'Turk Mutfagi' THEN 'Türk Mutfağı'
                    WHEN cuisine_type = 'Dunya Mutfagi' THEN 'Dünya Mutfağı'
                    WHEN cuisine_type = 'Deniz Urunleri' THEN 'Deniz Ürünleri'
                    ELSE cuisine_type
                END"
        );

        $pdo->exec(
            "UPDATE restaurants
             SET
                district = CASE
                    WHEN district <> '' THEN district
                    WHEN city IN ('Istanbul', 'İstanbul') THEN 'Kadıköy'
                    WHEN city = 'Ankara' THEN 'Çankaya'
                    WHEN city IN ('Izmir', 'İzmir') THEN 'Konak'
                    WHEN city = 'Antalya' THEN 'Muratpaşa'
                    WHEN city = 'Bursa' THEN 'Osmangazi'
                    ELSE 'Merkez'
                END,
                neighborhood = CASE
                    WHEN neighborhood <> '' THEN neighborhood
                    WHEN city IN ('Istanbul', 'İstanbul') THEN 'Merkez'
                    WHEN city = 'Ankara' THEN 'Merkez'
                    WHEN city IN ('Izmir', 'İzmir') THEN 'Merkez'
                    WHEN city = 'Antalya' THEN 'Merkez'
                    WHEN city = 'Bursa' THEN 'Merkez'
                    ELSE 'Merkez'
                END
             WHERE district = '' OR neighborhood = ''"
        );
    } catch (Throwable $exception) {
        error_log('Restaurant location column check failed: ' . $exception->getMessage());
    }

    $initialized = true;
}

function normalize_restaurant_row(array $restaurant): array
{
    foreach (['city', 'district', 'neighborhood', 'cuisine_type'] as $field) {
        if (array_key_exists($field, $restaurant)) {
            $restaurant[$field] = normalize_tr_display_value((string) $restaurant[$field]);
        }
    }

    return $restaurant;
}

function build_restaurant_filter_sql(array $filters, array &$params): string
{
    ensure_restaurant_location_columns();

    $sql = " FROM restaurants WHERE status = 'approved'";

    if (!empty($filters['q'])) {
        $sql .= ' AND name LIKE :search_name';
        $params[':search_name'] = '%' . $filters['q'] . '%';
    }

    if (!empty($filters['city'])) {
        $sql .= ' AND city = :city';
        $params[':city'] = $filters['city'];
    }
    if (!empty($filters['district'])) {
        $sql .= ' AND district = :district';
        $params[':district'] = $filters['district'];
    }
    if (!empty($filters['cuisine_type'])) {
        $sql .= ' AND cuisine_type = :cuisine';
        $params[':cuisine'] = $filters['cuisine_type'];
    }
    if (!empty($filters['guest_count'])) {
        $sql .= ' AND EXISTS (
            SELECT 1 FROM `tables` t
            WHERE t.restaurant_id = restaurants.id
              AND t.is_active = 1
              AND t.capacity >= :guest_count
        )';
        $params[':guest_count'] = (int) $filters['guest_count'];
    }

    return $sql;
}

function get_restaurants(array $filters = [], ?int $limit = null, int $offset = 0): array
{
    global $pdo;

    $params = [];
    $sql = 'SELECT restaurants.*' . build_restaurant_filter_sql($filters, $params);
    $sql .= ' ORDER BY is_featured DESC, rating DESC, name ASC';

    if ($limit !== null) {
        $sql .= ' LIMIT :limit OFFSET :offset';
    }

    $stmt = $pdo->prepare($sql);

    foreach ($params as $key => $value) {
        $type = $key === ':guest_count' ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindValue($key, $value, $type);
    }

    if ($limit !== null) {
        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->bindValue(':offset', max(0, $offset), PDO::PARAM_INT);
    }

    $stmt->execute();
    $rows = $stmt->fetchAll();
    return array_map(static fn (array $row): array => normalize_restaurant_row($row), $rows);
}

function count_restaurants(array $filters = []): int
{
    global $pdo;

    $params = [];
    $sql = 'SELECT COUNT(*)' . build_restaurant_filter_sql($filters, $params);
    $stmt = $pdo->prepare($sql);

    foreach ($params as $key => $value) {
        $type = $key === ':guest_count' ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindValue($key, $value, $type);
    }

    $stmt->execute();
    return (int) $stmt->fetchColumn();
}

function get_restaurant_filter_options(string $column): array
{
    global $pdo;
    ensure_restaurant_location_columns();
    $allowedColumns = ['city', 'cuisine_type'];

    if (!in_array($column, $allowedColumns, true)) {
        return [];
    }

    $stmt = $pdo->prepare("SELECT DISTINCT {$column} FROM restaurants WHERE status = 'approved' AND {$column} <> '' ORDER BY {$column} ASC");
    $stmt->execute();
    $options = array_values(array_filter(array_map(static fn ($value): string => normalize_tr_display_value((string) $value), $stmt->fetchAll(PDO::FETCH_COLUMN))));
    $options = array_values(array_unique($options));
    natcasesort($options);
    return array_values($options);
}

function get_district_filter_options(?string $city = null): array
{
    global $pdo;
    ensure_restaurant_location_columns();

    if ($city !== null && trim($city) !== '') {
        $stmt = $pdo->prepare(
            "SELECT DISTINCT district
             FROM restaurants
             WHERE status = 'approved' AND city = :city AND district <> ''
             ORDER BY district ASC"
        );
        $stmt->execute([':city' => trim($city)]);
    } else {
        $stmt = $pdo->prepare(
            "SELECT DISTINCT district
             FROM restaurants
             WHERE status = 'approved' AND district <> ''
             ORDER BY district ASC"
        );
        $stmt->execute();
    }

    $options = array_values(array_filter(array_map(static fn ($value): string => normalize_tr_display_value((string) $value), $stmt->fetchAll(PDO::FETCH_COLUMN))));
    $options = array_values(array_unique($options));
    natcasesort($options);
    return array_values($options);
}

function get_restaurant_filter_matrix(): array
{
    global $pdo;
    ensure_restaurant_location_columns();

    $stmt = $pdo->prepare(
        "SELECT r.city, r.district, r.cuisine_type,
                COALESCE(MAX(t.capacity), 0) AS max_capacity
         FROM restaurants r
         LEFT JOIN `tables` t ON t.restaurant_id = r.id AND t.is_active = 1
         WHERE r.status = 'approved'
         GROUP BY r.id, r.city, r.district, r.cuisine_type
         ORDER BY r.city ASC, r.district ASC, r.cuisine_type ASC"
    );
    $stmt->execute();

    return array_map(static function (array $row): array {
        return [
            'city' => normalize_tr_display_value((string) $row['city']),
            'district' => normalize_tr_display_value((string) $row['district']),
            'cuisine' => normalize_tr_display_value((string) $row['cuisine_type']),
            'maxGuests' => min(10, max(1, (int) $row['max_capacity'])),
        ];
    }, $stmt->fetchAll());
}

function get_featured_restaurants(int $limit = 6): array
{
    global $pdo;
    $safeLimit = max(1, $limit);

    $stmt = $pdo->prepare(
        "SELECT * FROM restaurants
         WHERE status = 'approved' AND is_featured = 1
         ORDER BY rating DESC, total_reservations DESC, name ASC
         LIMIT :limit"
    );
    $stmt->bindValue(':limit', $safeLimit, PDO::PARAM_INT);
    $stmt->execute();
    $featured = $stmt->fetchAll();

    if (count($featured) >= $safeLimit) {
        return array_map(static fn (array $row): array => normalize_restaurant_row($row), $featured);
    }

    $ids = array_map(static fn (array $restaurant): int => (int) $restaurant['id'], $featured);
    $remaining = $safeLimit - count($featured);

    if ($ids) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $fallbackSql = "SELECT * FROM restaurants
            WHERE status = 'approved' AND id NOT IN ($placeholders)
            ORDER BY rating DESC, total_reservations DESC, name ASC
            LIMIT ?";
        $fallbackStmt = $pdo->prepare($fallbackSql);
        foreach ($ids as $index => $id) {
            $fallbackStmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
        $fallbackStmt->bindValue(count($ids) + 1, $remaining, PDO::PARAM_INT);
    } else {
        $fallbackStmt = $pdo->prepare(
            "SELECT * FROM restaurants
             WHERE status = 'approved'
             ORDER BY rating DESC, total_reservations DESC, name ASC
             LIMIT :limit"
        );
        $fallbackStmt->bindValue(':limit', $remaining, PDO::PARAM_INT);
    }

    $fallbackStmt->execute();
    $merged = array_merge($featured, $fallbackStmt->fetchAll());
    return array_map(static fn (array $row): array => normalize_restaurant_row($row), $merged);
}

function get_restaurant(int $id): ?array
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM restaurants WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $restaurant = $stmt->fetch();

    return $restaurant ? normalize_restaurant_row($restaurant) : null;
}

function restaurant_image_url(array $restaurant): string
{
    $coverImage = trim((string) ($restaurant['cover_image'] ?? ''));
    if (preg_match('/^https?:\/\//i', $coverImage)) {
        return $coverImage;
    }

    $defaultImages = [
        'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=900&q=82',
        'https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=900&q=82',
        'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=900&q=82',
        'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=900&q=82',
        'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&w=900&q=82',
        'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=900&q=82',
        'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?auto=format&fit=crop&w=900&q=82',
        'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=900&q=82',
    ];

    $imageGroups = [
        'Akdeniz' => [
            'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?auto=format&fit=crop&w=900&q=82',
        ],
        'Anadolu' => [
            'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=900&q=82',
        ],
        'Asya' => [
            'https://images.unsplash.com/photo-1533777857889-4be7c70b33f7?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=900&q=82',
        ],
        'Brunch' => [
            'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1445116572660-236099ec97a0?auto=format&fit=crop&w=900&q=82',
        ],
        'Deniz Ürünleri' => [
            'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?auto=format&fit=crop&w=900&q=82',
        ],
        'Dünya Mutfağı' => [
            'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1551218808-94e220e084d2?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=900&q=82',
        ],
        'Ege' => [
            'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=900&q=82',
        ],
        'Italyan' => [
            'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=900&q=82',
        ],
        'Japon' => [
            'https://images.unsplash.com/photo-1533777857889-4be7c70b33f7?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=900&q=82',
        ],
        'Steakhouse' => [
            'https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&w=900&q=82',
        ],
        'Türk Mutfağı' => [
            'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=900&q=82',
        ],
        'Vegan' => [
            'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=900&q=82',
            'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=900&q=82',
        ],
    ];

    $cuisine = normalize_tr_display_value((string) ($restaurant['cuisine_type'] ?? ''));
    $images = $imageGroups[$cuisine] ?? $defaultImages;
    $seed = (string) (($restaurant['name'] ?? '') . '|' . ($restaurant['city'] ?? '') . '|' . $cuisine);
    $index = abs((int) crc32($seed)) % count($images);

    return $images[$index];
}

function restaurant_image_fallback_url(array $restaurant): string
{
    $name = trim((string) ($restaurant['name'] ?? 'Reserve'));
    $city = trim((string) ($restaurant['city'] ?? ''));
    $cuisine = trim((string) ($restaurant['cuisine_type'] ?? 'Restaurant'));
    $seed = abs((int) crc32($name . '|' . $city . '|' . $cuisine));
    $palettes = [
        ['#0f2b24', '#1d6a4f', '#f1c66d'],
        ['#12231f', '#7b4f2c', '#e0b35a'],
        ['#17221f', '#365f51', '#d7a94b'],
        ['#101b18', '#6e263d', '#f0c36a'],
        ['#18211e', '#394f7f', '#dfb25b'],
    ];
    $palette = $palettes[$seed % count($palettes)];
    $subtitle = trim($city . ' / ' . $cuisine, ' /');
    $initial = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name) ?: 'R', 0, 1));

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="900" height="620" viewBox="0 0 900 620">'
        . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop stop-color="' . $palette[0] . '"/><stop offset="1" stop-color="' . $palette[1] . '"/></linearGradient></defs>'
        . '<rect width="900" height="620" fill="url(#g)"/>'
        . '<circle cx="730" cy="120" r="190" fill="' . $palette[2] . '" opacity=".16"/>'
        . '<circle cx="150" cy="520" r="210" fill="#ffffff" opacity=".08"/>'
        . '<rect x="92" y="118" width="716" height="384" rx="32" fill="#ffffff" opacity=".08" stroke="#ffffff" stroke-opacity=".18"/>'
        . '<text x="132" y="240" fill="' . $palette[2] . '" font-family="Inter, Arial, sans-serif" font-size="70" font-weight="700">' . htmlspecialchars($initial, ENT_QUOTES, 'UTF-8') . '</text>'
        . '<text x="132" y="320" fill="#ffffff" font-family="Inter, Arial, sans-serif" font-size="44" font-weight="700">' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</text>'
        . '<text x="132" y="374" fill="#dfe9e4" font-family="Inter, Arial, sans-serif" font-size="24">' . htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') . '</text>'
        . '<line x1="132" y1="420" x2="420" y2="420" stroke="' . $palette[2] . '" stroke-width="5" stroke-linecap="round"/>'
        . '</svg>';

    return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
}

function get_restaurant_menu(int $restaurantId, bool $activeOnly = true): array
{
    global $pdo;

    $sql = 'SELECT c.id AS category_id, c.name AS category_name, c.display_order,
                   i.id AS item_id, i.name AS item_name, i.description, i.price, i.image_url, i.is_active
            FROM menu_categories c
            LEFT JOIN menu_items i ON i.category_id = c.id AND i.restaurant_id = c.restaurant_id';

    if ($activeOnly) {
        $sql .= ' AND i.is_active = 1';
    }

    $sql .= ' WHERE c.restaurant_id = :restaurant_id
              ORDER BY c.display_order ASC, c.name ASC, i.name ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':restaurant_id' => $restaurantId]);
    $rows = $stmt->fetchAll();

    $menu = [];
    foreach ($rows as $row) {
        $categoryId = (int) $row['category_id'];
        if (!isset($menu[$categoryId])) {
            $menu[$categoryId] = [
                'id' => $categoryId,
                'name' => $row['category_name'],
                'display_order' => (int) $row['display_order'],
                'items' => [],
            ];
        }

        if (!empty($row['item_id'])) {
            $menu[$categoryId]['items'][] = [
                'id' => (int) $row['item_id'],
                'name' => $row['item_name'],
                'description' => $row['description'],
                'price' => (float) $row['price'],
                'image_url' => $row['image_url'],
                'is_active' => (int) $row['is_active'],
            ];
        }
    }

    return array_values($menu);
}

function get_menu_categories(int $restaurantId): array
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM menu_categories WHERE restaurant_id = :restaurant_id ORDER BY display_order ASC, name ASC');
    $stmt->execute([':restaurant_id' => $restaurantId]);
    return $stmt->fetchAll();
}

function format_price(float $price): string
{
    return number_format($price, 2, ',', '.') . ' TL';
}

function menu_category_i18n_key(string $categoryName): string
{
    $map = [
        'starters' => 'menu.category.starters',
        'main courses' => 'menu.category.main_courses',
        'desserts' => 'menu.category.desserts',
        'drinks' => 'menu.category.drinks',
    ];

    return $map[mb_strtolower(trim($categoryName))] ?? '';
}

function menu_category_description(string $categoryName, string $cuisineType): string
{
    $category = mb_strtolower(trim($categoryName));
    $cuisine = normalize_tr_display_value(trim($cuisineType));

    $descriptions = [
        'starters' => "{$cuisine} mutfağına uygun hafif başlangıçlar ve paylaşım tabakları.",
        'main courses' => "{$cuisine} karakterini yansıtan ana yemek seçenekleri.",
        'desserts' => 'Yemek sonrası hafif ve dengeli tatlı seçimleri.',
        'drinks' => 'Menüdeki lezzetlerle uyumlu alkolsüz içecekler.',
    ];

    return $descriptions[$category] ?? 'Mekana uygun seçilmiş lezzetler.';
}

function menu_item_i18n_key(string $itemName, string $field): string
{
    $slug = mb_strtolower(trim($itemName));
    $slug = str_replace([' ', '.', "'", '"'], ['_', '', '', ''], $slug);
    $map = [
        'bruschetta' => 'menu.item.bruschetta',
        'chicken_alfredo' => 'menu.item.chicken_alfredo',
        'sea_bass' => 'menu.item.sea_bass',
        'tiramisu' => 'menu.item.tiramisu',
        'fresh_lemonade' => 'menu.item.fresh_lemonade',
        'mercimek_corbasi' => 'menu.item.mercimek_corbasi',
        'izgara_kofte' => 'menu.item.izgara_kofte',
        'sutlac' => 'menu.item.sutlac',
        'ayran' => 'menu.item.ayran',
    ];

    return isset($map[$slug]) ? $map[$slug] . '.' . $field : '';
}

function get_available_tables(int $restaurantId, int $guestCount = 1): array
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM `tables` WHERE restaurant_id = :restaurant_id AND is_active = 1 AND capacity >= :capacity ORDER BY capacity ASC, table_number ASC');
    $stmt->execute([
        ':restaurant_id' => $restaurantId,
        ':capacity' => $guestCount,
    ]);
    return $stmt->fetchAll();
}

function find_table_for_reservation(int $restaurantId, int $guestCount, string $date, string $time, int $durationMinutes = 90): ?array
{
    global $pdo;

    $safeDuration = max(30, min(300, $durationMinutes));
    $durationSeconds = $safeDuration * 60;

    $candidatesStmt = $pdo->prepare(
        "SELECT t.*
         FROM `tables` t
         WHERE t.restaurant_id = :restaurant_id
           AND t.is_active = 1
           AND t.capacity >= :guest_count
         ORDER BY t.capacity ASC, t.table_number ASC"
    );
    $candidatesStmt->execute([
        ':restaurant_id' => $restaurantId,
        ':guest_count' => $guestCount,
    ]);

    $candidates = $candidatesStmt->fetchAll();
    if (empty($candidates)) {
        return null;
    }

    $tableLockStmt = $pdo->prepare('SELECT id FROM `tables` WHERE id = :id FOR UPDATE');
    $conflictStmt = $pdo->prepare(
        "SELECT 1
         FROM reservations r
         WHERE r.table_id = :table_id
           AND r.reservation_date = :reservation_date
           AND r.status IN ('pending', 'confirmed')
           AND r.reservation_time < ADDTIME(:new_reservation_time_end_base, SEC_TO_TIME(:duration_seconds_a))
           AND ADDTIME(r.reservation_time, SEC_TO_TIME(:duration_seconds_b)) > :new_reservation_time_start
         LIMIT 1
         FOR UPDATE"
    );

    foreach ($candidates as $table) {
        $tableId = (int) ($table['id'] ?? 0);
        if ($tableId <= 0) {
            continue;
        }

        $tableLockStmt->execute([':id' => $tableId]);

        $conflictStmt->execute([
            ':table_id' => $tableId,
            ':reservation_date' => $date,
            ':new_reservation_time_end_base' => $time,
            ':duration_seconds_a' => $durationSeconds,
            ':duration_seconds_b' => $durationSeconds,
            ':new_reservation_time_start' => $time,
        ]);

        if (!$conflictStmt->fetchColumn()) {
            return $table;
        }
    }

    return null;
}

function get_user_reservations(string $email): array
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM reservations WHERE customer_email = :email ORDER BY reservation_date DESC, reservation_time DESC');
    $stmt->execute([':email' => $email]);
    return $stmt->fetchAll();
}

function ensure_restaurant_reviews_table(): void
{
    global $pdo;
    static $initialized = false;

    if ($initialized) {
        return;
    }

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS restaurant_reviews (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            reservation_id INT UNSIGNED NOT NULL,
            restaurant_id INT UNSIGNED NOT NULL,
            user_email VARCHAR(190) NOT NULL,
            rating TINYINT UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_reviews_reservation
                FOREIGN KEY (reservation_id) REFERENCES reservations(id)
                ON UPDATE CASCADE ON DELETE CASCADE,
            CONSTRAINT fk_reviews_restaurant
                FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
                ON UPDATE CASCADE ON DELETE CASCADE,
            CONSTRAINT fk_reviews_user_email
                FOREIGN KEY (user_email) REFERENCES users(email)
                ON UPDATE CASCADE ON DELETE CASCADE,
            UNIQUE KEY uq_review_reservation (reservation_id),
            INDEX idx_reviews_restaurant (restaurant_id),
            INDEX idx_reviews_user_email (user_email),
            CHECK (rating BETWEEN 1 AND 5)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $initialized = true;
}

function get_reservation_review(int $reservationId, string $email): ?array
{
    global $pdo;
    ensure_restaurant_reviews_table();

    $stmt = $pdo->prepare('SELECT * FROM restaurant_reviews WHERE reservation_id = :reservation_id AND user_email = :user_email LIMIT 1');
    $stmt->execute([
        ':reservation_id' => $reservationId,
        ':user_email' => $email,
    ]);
    $review = $stmt->fetch();
    return $review ?: null;
}

function get_restaurant_review_summary(int $restaurantId): array
{
    global $pdo;
    ensure_restaurant_reviews_table();

    $stmt = $pdo->prepare(
        'SELECT COUNT(*) AS review_count, COALESCE(AVG(rating), 0) AS average_rating
         FROM restaurant_reviews
         WHERE restaurant_id = :restaurant_id'
    );
    $stmt->execute([':restaurant_id' => $restaurantId]);
    $summary = $stmt->fetch();

    return [
        'count' => (int) ($summary['review_count'] ?? 0),
        'average' => round((float) ($summary['average_rating'] ?? 0), 2),
    ];
}

function refresh_restaurant_rating(int $restaurantId): void
{
    global $pdo;
    ensure_restaurant_reviews_table();

    $stmt = $pdo->prepare('SELECT COALESCE(AVG(rating), 0) FROM restaurant_reviews WHERE restaurant_id = :restaurant_id');
    $stmt->execute([':restaurant_id' => $restaurantId]);
    $rating = round((float) $stmt->fetchColumn(), 2);

    $update = $pdo->prepare('UPDATE restaurants SET rating = :rating WHERE id = :restaurant_id');
    $update->execute([
        ':rating' => $rating,
        ':restaurant_id' => $restaurantId,
    ]);
}

function get_host_restaurant(string $ownerEmail): ?array
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM restaurants WHERE owner_email = :owner_email LIMIT 1');
    $stmt->execute([':owner_email' => $ownerEmail]);
    $restaurant = $stmt->fetch();
    return $restaurant ? normalize_restaurant_row($restaurant) : null;
}

function get_host_tables(int $restaurantId): array
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM `tables` WHERE restaurant_id = :restaurant_id ORDER BY is_active DESC, table_number ASC');
    $stmt->execute([':restaurant_id' => $restaurantId]);
    return $stmt->fetchAll();
}

function get_host_daily_reservations(string $ownerEmail, ?string $date = null): array
{
    global $pdo;
    auto_mark_no_show_reservations($ownerEmail);

    $date = $date ?: date('Y-m-d');
    $stmt = $pdo->prepare('SELECT * FROM reservations WHERE owner_email = :owner_email AND reservation_date = :reservation_date ORDER BY reservation_time ASC');
    $stmt->execute([
        ':owner_email' => $ownerEmail,
        ':reservation_date' => $date,
    ]);
    return $stmt->fetchAll();
}

function auto_mark_no_show_reservations(?string $ownerEmail = null): int
{
    global $pdo;

    $sql = "UPDATE reservations
            SET status = 'no_show'
            WHERE status IN ('pending', 'confirmed')
              AND reservation_date < CURDATE()";

    if ($ownerEmail !== null && $ownerEmail !== '') {
        $sql .= ' AND owner_email = :owner_email';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':owner_email' => $ownerEmail]);
        return $stmt->rowCount();
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount();
}

function get_admin_stats(): array
{
    global $pdo;

    $users = $pdo->prepare('SELECT COUNT(*) FROM users');
    $users->execute();
    $restaurants = $pdo->prepare('SELECT COUNT(*) FROM restaurants');
    $restaurants->execute();
    $reservations = $pdo->prepare('SELECT COUNT(*) FROM reservations');
    $reservations->execute();
    $pendingRestaurants = $pdo->prepare("SELECT COUNT(*) FROM restaurants WHERE status = 'pending'");
    $pendingRestaurants->execute();

    return [
        'users' => (int) $users->fetchColumn(),
        'restaurants' => (int) $restaurants->fetchColumn(),
        'reservations' => (int) $reservations->fetchColumn(),
        'pending_restaurants' => (int) $pendingRestaurants->fetchColumn(),
    ];
}

function ensure_partner_applications_table(): void
{
    global $pdo;
    static $initialized = false;

    if ($initialized) {
        return;
    }

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS restaurant_partner_applications (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            restaurant_name VARCHAR(160) NOT NULL,
            contact_name VARCHAR(150) NOT NULL,
            restaurant_email VARCHAR(190) NOT NULL,
            phone VARCHAR(30) NOT NULL,
            city VARCHAR(80) NOT NULL,
            district VARCHAR(80) NOT NULL DEFAULT '',
            neighborhood VARCHAR(120) NOT NULL DEFAULT '',
            address VARCHAR(255) NOT NULL,
            cuisine_type VARCHAR(80) NOT NULL,
            description TEXT NOT NULL,
            opening_time TIME NOT NULL,
            closing_time TIME NOT NULL,
            image_url VARCHAR(255) NULL,
            password_hash VARCHAR(255) NOT NULL,
            status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
            linked_restaurant_id INT UNSIGNED NULL,
            review_notes VARCHAR(500) NULL,
            reviewed_by_email VARCHAR(190) NULL,
            reviewed_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_partner_application_status_created (status, created_at),
            INDEX idx_partner_application_email (restaurant_email),
            CONSTRAINT fk_partner_application_restaurant
                FOREIGN KEY (linked_restaurant_id) REFERENCES restaurants(id)
                ON UPDATE CASCADE ON DELETE SET NULL,
            CONSTRAINT fk_partner_application_reviewer
                FOREIGN KEY (reviewed_by_email) REFERENCES users(email)
                ON UPDATE CASCADE ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    ensure_table_column('restaurant_partner_applications', 'district', "VARCHAR(80) NOT NULL DEFAULT '' AFTER city");
    ensure_table_column('restaurant_partner_applications', 'neighborhood', "VARCHAR(120) NOT NULL DEFAULT '' AFTER district");

    $initialized = true;
}

function get_partner_applications(?string $status = null): array
{
    global $pdo;
    ensure_partner_applications_table();

    if ($status === null) {
        $stmt = $pdo->prepare('SELECT * FROM restaurant_partner_applications ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    $stmt = $pdo->prepare(
        'SELECT * FROM restaurant_partner_applications
         WHERE status = :status
         ORDER BY created_at DESC'
    );
    $stmt->execute([':status' => $status]);
    return $stmt->fetchAll();
}
