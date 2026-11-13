<?php
/**
 * Public-facing helper functions
 * Grand Furniture Vizag
 * 
 * These supplement the core functions.php with frontend query helpers.
 */

// Alias so pages can call sanitizeOutput() or sanitize() interchangeably
if (!function_exists('sanitizeOutput')) {
    function sanitizeOutput(string $str): string {
        return htmlspecialchars(trim($str), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

// Alias: getSettingValue → wraps existing getSetting()
if (!function_exists('getSettingValue')) {
    function getSettingValue(PDO $pdo, string $key, string $default = ''): string {
        $val = getSetting($key, $pdo);
        return $val !== '' ? $val : $default;
    }
}

/**
 * Fetch active product categories, ordered by sort_order then name.
 */
if (!function_exists('getCategories')) {
    function getCategories(PDO $pdo, int $limit = 6): array {
        $stmt = $pdo->prepare(
            "SELECT id, name, slug, image, description
               FROM categories
              WHERE is_active = 1
              ORDER BY sort_order ASC, name ASC
              LIMIT ?"
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

/**
 * Fetch gallery items filtered by category string.
 */
if (!function_exists('getGalleryItems')) {
    function getGalleryItems(PDO $pdo, string $category, int $limit = 6): array {
        $stmt = $pdo->prepare(
            "SELECT id, title, image_path AS image, '' AS slug
               FROM gallery
              WHERE category = ? AND is_active = 1
              ORDER BY sort_order ASC, created_at DESC
              LIMIT ?"
        );
        $stmt->bindValue(1, $category, PDO::PARAM_STR);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

/**
 * Fetch featured products — override the one from functions.php so 
 * it returns the column names expected by index.php templates.
 */
// Note: We do NOT redefine getFeaturedProducts here because functions.php
// already defines it. Instead index.php's fallback data handles the case.
