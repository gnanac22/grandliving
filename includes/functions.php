<?php
/**
 * Grand Furniture Vizag — Utility / Helper Functions
 * Include this file after config/db.php on every page.
 */

// ─── Input sanitisation ──────────────────────────────────────

/**
 * Sanitise a user-supplied string.
 */
function sanitize(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Generate a URL-safe slug from any string.
 */
function generateSlug(string $string): string
{
    if (function_exists('iconv')) {
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
    }
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9\s\-]/', '', $string);
    $string = preg_replace('/[\s\-]+/', '-', $string);
    return trim($string, '-');
}

// ─── CSRF ────────────────────────────────────────────────────

function generateCSRFToken(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function validateCSRFToken(string $token): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $stored = $_SESSION[CSRF_TOKEN_NAME] ?? '';
    return $stored !== '' && hash_equals($stored, $token);
}

// ─── Auth ────────────────────────────────────────────────────

function isLoggedIn(): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    return !empty($_SESSION['admin_id'])
        && !empty($_SESSION['admin_username'])
        && isset($_SESSION['admin_logged_in'])
        && $_SESSION['admin_logged_in'] === true;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        $loginUrl = '/admin/login.php';
        if (!empty($_SERVER['REQUEST_URI'])) {
            $loginUrl .= '?redirect=' . urlencode($_SERVER['REQUEST_URI']);
        }
        header('Location: ' . $loginUrl);
        exit;
    }
    if (empty($_SESSION['last_regen']) || (time() - $_SESSION['last_regen']) > 300) {
        session_regenerate_id(true);
        $_SESSION['last_regen'] = time();
    }
}

// ─── Settings ────────────────────────────────────────────────

function getSetting(string $key, PDO $pdo): string
{
    static $cache = [];
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    $stmt = $pdo->prepare('SELECT setting_value FROM website_settings WHERE setting_key = ? LIMIT 1');
    $stmt->execute([$key]);
    $value = $stmt->fetchColumn();
    $cache[$key] = $value !== false ? (string) $value : '';
    return $cache[$key];
}

function getSettings(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT setting_key, setting_value FROM website_settings');
    $rows = $stmt->fetchAll();
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

// ─── Categories ──────────────────────────────────────────────

function getAllCategories(PDO $pdo): array
{
    $stmt = $pdo->query(
        'SELECT id, name, slug, description, image, sort_order
         FROM categories
         WHERE is_active = 1
         ORDER BY sort_order ASC, name ASC'
    );
    return $stmt->fetchAll();
}

// ─── Products ────────────────────────────────────────────────

function getFeaturedProducts(int $limit = 8, ?PDO $pdo = null): array
{
    if ($pdo === null) {
        global $pdo;
    }
    if (!$pdo) {
        return [];
    }
    $stmt = $pdo->prepare(
        'SELECT p.id, p.name, p.slug, p.short_description, p.price_range,
                p.availability, p.materials,
                pi.image_path AS primary_image, pi.alt_text AS image_alt,
                c.name AS category_name, c.slug AS category_slug
         FROM products p
         LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
         LEFT JOIN categories c ON c.id = p.category_id
         WHERE p.is_featured = 1 AND p.is_active = 1 AND c.is_active = 1
         ORDER BY p.sort_order ASC, p.created_at DESC
         LIMIT :lim'
    );
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getProductsByCategory(string $slug, int $page, int $perPage, PDO $pdo): array
{
    $catStmt = $pdo->prepare(
        'SELECT id, name, slug, description, image FROM categories WHERE slug = ? AND is_active = 1 LIMIT 1'
    );
    $catStmt->execute([$slug]);
    $category = $catStmt->fetch();
    if (!$category) {
        return ['products' => [], 'total' => 0, 'category' => null];
    }

    $offset = ($page - 1) * $perPage;

    $countStmt = $pdo->prepare(
        'SELECT COUNT(*) FROM products WHERE category_id = ? AND is_active = 1'
    );
    $countStmt->execute([$category['id']]);
    $total = (int) $countStmt->fetchColumn();

    $stmt = $pdo->prepare(
        'SELECT p.id, p.name, p.slug, p.short_description, p.price_range,
                p.availability, p.materials, p.sku,
                pi.image_path AS primary_image, pi.alt_text AS image_alt
         FROM products p
         LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
         WHERE p.category_id = :cat_id AND p.is_active = 1
         ORDER BY p.sort_order ASC, p.created_at DESC
         LIMIT :lim OFFSET :off'
    );
    $stmt->bindValue(':cat_id', $category['id'], PDO::PARAM_INT);
    $stmt->bindValue(':lim',    $perPage,         PDO::PARAM_INT);
    $stmt->bindValue(':off',    $offset,          PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll();

    return ['products' => $products, 'total' => $total, 'category' => $category];
}

function getProduct(string $slug, PDO $pdo): ?array
{
    $stmt = $pdo->prepare(
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug
         FROM products p
         LEFT JOIN categories c ON c.id = p.category_id
         WHERE p.slug = ? AND p.is_active = 1
         LIMIT 1'
    );
    $stmt->execute([$slug]);
    $product = $stmt->fetch();
    if (!$product) {
        return null;
    }

    $imgStmt = $pdo->prepare(
        'SELECT image_path, alt_text, is_primary, sort_order
         FROM product_images
         WHERE product_id = ?
         ORDER BY is_primary DESC, sort_order ASC'
    );
    $imgStmt->execute([$product['id']]);
    $product['images'] = $imgStmt->fetchAll();

    return $product;
}

function getRelatedProducts(int $productId, int $categoryId, int $limit = 4, ?PDO $pdo = null): array
{
    if ($pdo === null) {
        global $pdo;
    }
    if (!$pdo) {
        return [];
    }
    $stmt = $pdo->prepare(
        'SELECT p.id, p.name, p.slug, p.short_description, p.price_range,
                p.availability,
                pi.image_path AS primary_image, pi.alt_text AS image_alt
         FROM products p
         LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
         WHERE p.category_id = :cat AND p.id != :pid AND p.is_active = 1
         ORDER BY p.is_featured DESC, p.sort_order ASC
         LIMIT :lim'
    );
    $stmt->bindValue(':cat', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':pid', $productId,  PDO::PARAM_INT);
    $stmt->bindValue(':lim', $limit,      PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// ─── Gallery ─────────────────────────────────────────────────

function getActiveGallery(?string $category, PDO $pdo): array
{
    if ($category) {
        $stmt = $pdo->prepare(
            'SELECT id, title, image_path, category, alt_text
             FROM gallery
             WHERE is_active = 1 AND category = ?
             ORDER BY sort_order ASC, created_at DESC'
        );
        $stmt->execute([$category]);
    } else {
        $stmt = $pdo->query(
            'SELECT id, title, image_path, category, alt_text
             FROM gallery
             WHERE is_active = 1
             ORDER BY sort_order ASC, created_at DESC'
        );
    }
    return $stmt->fetchAll();
}

// ─── Testimonials ────────────────────────────────────────────

function getActiveTestimonials(PDO $pdo): array
{
    $stmt = $pdo->query(
        'SELECT id, name, location, rating, review
         FROM testimonials
         WHERE is_active = 1
         ORDER BY is_verified DESC, created_at DESC'
    );
    return $stmt->fetchAll();
}

// ─── Date formatting ─────────────────────────────────────────

function formatDate(string $date): string
{
    return date('d F Y', strtotime($date));
}

function formatDateTime(string $date): string
{
    return date('d M Y, g:i A', strtotime($date));
}

// ─── Image upload ────────────────────────────────────────────

function uploadImage(array $file, string $subDir): string|false
{
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return false;
    }

    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, ALLOWED_MIME_TYPES, true)) {
        return false;
    }

    $extMap = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
    $ext = $extMap[$mimeType];

    $destDir = rtrim(UPLOAD_DIR, '/') . '/' . trim($subDir, '/') . '/';
    if (!is_dir($destDir) && !mkdir($destDir, 0755, true)) {
        return false;
    }

    $filename = bin2hex(random_bytes(16)) . '_' . time() . '.' . $ext;
    $destPath = $destDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return false;
    }

    return '/' . ltrim(UPLOAD_URL, '/') . trim($subDir, '/') . '/' . $filename;
}

function deleteImage(string $path): bool
{
    $absPath = realpath(dirname(__DIR__) . $path);
    if (!$absPath) {
        return false;
    }
    $uploadDir = realpath(UPLOAD_DIR);
    if (!$uploadDir || strpos($absPath, $uploadDir) !== 0) {
        return false;
    }
    if (is_file($absPath)) {
        return unlink($absPath);
    }
    return false;
}

// ─── Pagination ──────────────────────────────────────────────

function paginate(int $totalItems, int $perPage, int $currentPage): array
{
    $totalPages  = $perPage > 0 ? (int) ceil($totalItems / $perPage) : 1;
    $currentPage = max(1, min($currentPage, $totalPages));
    $window = 2;
    $start  = max(1, $currentPage - $window);
    $end    = min($totalPages, $currentPage + $window);
    $pages  = range($start, $end);

    return [
        'total_items'  => $totalItems,
        'per_page'     => $perPage,
        'current_page' => $currentPage,
        'total_pages'  => $totalPages,
        'has_prev'     => $currentPage > 1,
        'has_next'     => $currentPage < $totalPages,
        'prev_page'    => $currentPage > 1            ? $currentPage - 1 : null,
        'next_page'    => $currentPage < $totalPages  ? $currentPage + 1 : null,
        'pages'        => $pages,
    ];
}

// ─── Flash messages ──────────────────────────────────────────

function setFlash(string $type, string $message): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function displayFlash(): string
{
    $flash = getFlash();
    if (!$flash) {
        return '';
    }
    $type = htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8');
    $msg  = htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8');
    return "<div class=\"flash-message flash-{$type}\" style=\"padding:12px 20px;margin-bottom:20px;border-radius:4px;background:#D5BE9A;color:#171614;font-weight:600;\">{$msg}</div>";
}

function getCurrentAdmin(): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }
    return [
        'id'       => $_SESSION['admin_id'] ?? 1,
        'username' => $_SESSION['admin_username'] ?? 'Admin',
        'name'     => $_SESSION['admin_username'] ?? 'Admin',
        'email'    => $_SESSION['admin_email'] ?? 'admin@grandfurniturevizag.com',
        'role'     => $_SESSION['admin_role'] ?? 'super_admin'
    ];
}

// ─── Email notifications ─────────────────────────────────────

function sendEnquiryNotification(array $enquiry, array $settings): bool
{
    $to       = $settings['enquiry_email_to'] ?? ($settings['email'] ?? '');
    $siteName = $settings['site_name'] ?? SITE_NAME;
    if (empty($to)) {
        return false;
    }

    $subject  = "[{$siteName}] New Enquiry from " . ($enquiry['name'] ?? 'Unknown');
    $body     = "A new enquiry has been submitted on {$siteName}.\r\n\r\n";
    $body    .= "Name:    " . ($enquiry['name']         ?? '') . "\r\n";
    $body    .= "Phone:   " . ($enquiry['phone']        ?? '') . "\r\n";
    $body    .= "Email:   " . ($enquiry['email']        ?? '') . "\r\n";
    $body    .= "Subject: " . ($enquiry['subject']      ?? '') . "\r\n";
    $body    .= "Product: " . ($enquiry['product_name'] ?? 'N/A') . "\r\n\r\n";
    $body    .= "Message:\r\n" . ($enquiry['message']   ?? '') . "\r\n\r\n";
    $body    .= "---\r\nDate: " . date('d M Y, g:i A') . "\r\n";
    $body    .= "IP:   " . ($enquiry['ip_address'] ?? 'unknown') . "\r\n";
    $body    .= "Admin panel: " . SITE_URL . "/admin/\r\n";

    $fromEmail = $settings['smtp_from_email'] ?? ('noreply@' . parse_url(SITE_URL, PHP_URL_HOST));
    $fromName  = $settings['smtp_from_name']  ?? $siteName;
    $headers   = "From: {$fromName} <{$fromEmail}>\r\n";
    $headers  .= "Reply-To: " . ($enquiry['email'] ?? $fromEmail) . "\r\n";
    $headers  .= "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";

    return @mail($to, $subject, $body, $headers);
}

function sendCustomRequestNotification(array $request, array $settings): bool
{
    $to       = $settings['enquiry_email_to'] ?? ($settings['email'] ?? '');
    $siteName = $settings['site_name'] ?? SITE_NAME;
    if (empty($to)) {
        return false;
    }

    $subject  = "[{$siteName}] New Custom Request from " . ($request['name'] ?? 'Unknown');
    $body     = "A new custom furniture request has been submitted on {$siteName}.\r\n\r\n";
    $body    .= "Name:           " . ($request['name']                ?? '') . "\r\n";
    $body    .= "Phone:          " . ($request['phone']               ?? '') . "\r\n";
    $body    .= "Email:          " . ($request['email']               ?? '') . "\r\n";
    $body    .= "Category:       " . ($request['category']            ?? '') . "\r\n";
    $body    .= "Dimensions:     " . ($request['dimensions']          ?? '') . "\r\n";
    $body    .= "Material Pref.: " . ($request['material_preference'] ?? '') . "\r\n";
    $body    .= "Budget Range:   " . ($request['budget_range']        ?? '') . "\r\n\r\n";
    $body    .= "Description:\r\n" . ($request['description']         ?? '') . "\r\n\r\n";
    $body    .= "---\r\nDate: " . date('d M Y, g:i A') . "\r\n";
    $body    .= "Admin panel: " . SITE_URL . "/admin/custom-requests.php\r\n";

    $fromEmail = $settings['smtp_from_email'] ?? ('noreply@' . parse_url(SITE_URL, PHP_URL_HOST));
    $fromName  = $settings['smtp_from_name']  ?? $siteName;
    $headers   = "From: {$fromName} <{$fromEmail}>\r\n";
    $headers  .= "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";

    return @mail($to, $subject, $body, $headers);
}

// ─── Rate limiting ───────────────────────────────────────────

function rateLimitCheck(string $key, int $limit, int $window): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $sessionKey = 'rl_' . md5($key);
    $now        = time();

    if (!isset($_SESSION[$sessionKey])) {
        $_SESSION[$sessionKey] = ['count' => 0, 'start' => $now];
    }
    $rl = &$_SESSION[$sessionKey];
    if (($now - $rl['start']) >= $window) {
        $rl = ['count' => 0, 'start' => $now];
    }
    $rl['count']++;
    return $rl['count'] > $limit;
}

function rateLimitReset(string $key): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    unset($_SESSION['rl_' . md5($key)]);
}

// ─── Activity logging ────────────────────────────────────────

function logActivity(PDO $pdo, string $action, string $details = '', ?int $adminId = null): void
{
    try {
        $ip   = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown')[0]);
        $stmt = $pdo->prepare(
            'INSERT INTO activity_logs (admin_id, action, details, ip_address, created_at)
             VALUES (?, ?, ?, ?, NOW())'
        );
        $stmt->execute([$adminId, $action, $details, $ip]);
    } catch (PDOException $e) {
        error_log('logActivity failed: ' . $e->getMessage());
    }
}

// ─── Helpers ─────────────────────────────────────────────────

function getClientIP(): string
{
    $keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(explode(',', $_SERVER[$key])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return 'unknown';
}

function redirect(string $url, int $code = 302): never
{
    header('Location: ' . $url, true, $code);
    exit;
}
