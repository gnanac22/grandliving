<?php
/**
 * Process General Enquiry
 * Grand Furniture Vizag
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/db.php';
require_once 'includes/functions.php';

// ============================================================
// REQUEST METHOD GUARD
// ============================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

// ============================================================
// AJAX / JSON DETECTION
// ============================================================
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$wants_json = $is_ajax || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

// ============================================================
// HELPER FUNCTIONS
// ============================================================
function sendJsonResponse(bool $success, string $message, array $data = []): void {
    header('Content-Type: application/json');
    http_response_code($success ? 200 : 400);
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}

function redirectWithFlash(string $url, string $type, string $message): void {
    $_SESSION['flash_' . $type] = $message;
    header('Location: ' . $url);
    exit;
}

// ============================================================
// CSRF VALIDATION
// ============================================================
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    if ($wants_json) sendJsonResponse(false, 'Invalid request. Please refresh the page and try again.');
    redirectWithFlash('/', 'error', 'Invalid request. Please try again.');
}

// ============================================================
// RATE LIMITING — max 5 enquiries per 5 minutes per IP
// ============================================================
$ip  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$now = time();

if (!isset($_SESSION['rate_limits'])) {
    $_SESSION['rate_limits'] = [];
}

// Purge entries older than 5 minutes
$_SESSION['rate_limits'] = array_filter(
    $_SESSION['rate_limits'],
    fn($entry) => ($now - $entry['time']) < 300
);

$ip_submissions = array_filter(
    $_SESSION['rate_limits'],
    fn($e) => $e['ip'] === $ip && $e['type'] === 'enquiry'
);

if (count($ip_submissions) >= 5) {
    if ($wants_json) sendJsonResponse(false, 'Too many requests. Please wait a few minutes before trying again.');
    redirectWithFlash('/', 'error', 'Too many requests. Please wait a moment.');
}

// ============================================================
// INPUT VALIDATION
// ============================================================
$errors = [];

// --- name: required, 2-100 chars ---
$name = trim(strip_tags($_POST['name'] ?? ''));
if ($name === '') {
    $errors['name'] = 'Name is required.';
} elseif (mb_strlen($name) < 2) {
    $errors['name'] = 'Name must be at least 2 characters.';
} elseif (mb_strlen($name) > 100) {
    $errors['name'] = 'Name must not exceed 100 characters.';
}

// --- phone: required, Indian mobile regex (normalized) ---
$phone_raw = trim($_POST['phone'] ?? '');
$phone = preg_replace('/[^0-9]/', '', $phone_raw);
if (strlen($phone) === 12 && substr($phone, 0, 2) === '91') {
    $phone = substr($phone, 2);
} elseif (strlen($phone) === 11 && $phone[0] === '0') {
    $phone = substr($phone, 1);
}

if ($phone === '') {
    $errors['phone'] = 'Phone number is required.';
} elseif (!preg_match('/^[6-9][0-9]{9}$/', $phone)) {
    $errors['phone'] = 'Please enter a valid 10-digit Indian mobile number.';
}

// --- email: optional, validate if present ---
$email = trim($_POST['email'] ?? '');
if ($email !== '') {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
} else {
    $email = null;
}

// --- product_id: optional, positive integer ---
$product_id = null;
if (!empty($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    if ($product_id <= 0) {
        $product_id = null;
    }
}

// --- product_name: optional, max 200 chars ---
$product_name = trim(strip_tags($_POST['product_name'] ?? ''));
if ($product_name === '') {
    $product_name = null;
} elseif (mb_strlen($product_name) > 200) {
    $errors['product_name'] = 'Product name must not exceed 200 characters.';
}

// --- message: required, 10-1000 chars ---
$message = trim(strip_tags($_POST['message'] ?? ''));
$subject = trim(strip_tags($_POST['subject'] ?? ''));
if ($subject !== '' && strpos($message, $subject) === false) {
    $message = "Subject: " . $subject . "\n\n" . $message;
}

if ($message === '') {
    $errors['message'] = 'Message is required.';
} elseif (mb_strlen($message) < 5) {
    $errors['message'] = 'Message must be at least 5 characters.';
} elseif (mb_strlen($message) > 1000) {
    $errors['message'] = 'Message must not exceed 1000 characters.';
}

// --- enquiry_type: optional, whitelist ---
$allowed_enquiry_types = ['product', 'general', 'custom', 'delivery', 'warranty'];
$enquiry_type = trim($_POST['enquiry_type'] ?? 'general');
if (!in_array($enquiry_type, $allowed_enquiry_types, true)) {
    $enquiry_type = 'general';
}

// ============================================================
// RETURN ERRORS IF ANY
// ============================================================
if (!empty($errors)) {
    if ($wants_json) {
        sendJsonResponse(false, 'Please fix the errors.', ['errors' => $errors]);
    }
    $_SESSION['form_errors']  = $errors;
    $_SESSION['form_data']    = $_POST;
    redirectWithFlash(
        $_SERVER['HTTP_REFERER'] ?? '/',
        'error',
        'Please fix the errors and try again.'
    );
}

// ============================================================
// DATABASE INSERT
// ============================================================
try {
    $stmt = $pdo->prepare("
        INSERT INTO enquiries 
            (name, phone, email, subject, product_id, product_name, message, ip_address, status, created_at)
        VALUES 
            (:name, :phone, :email, :subject, :product_id, :product_name, :message, :ip, 'pending', NOW())
    ");
    $stmt->execute([
        ':name'         => $name,
        ':phone'        => $phone,
        ':email'        => $email ?? null,
        ':subject'      => $subject ?: 'General Enquiry',
        ':product_id'   => $product_id ?? null,
        ':product_name' => $product_name ?? null,
        ':message'      => $message,
        ':ip'           => $ip,
    ]);
    $enquiry_id = $pdo->lastInsertId();
} catch (PDOException $e) {
    error_log('Enquiry DB error: ' . $e->getMessage());
    if ($wants_json) {
        sendJsonResponse(false, 'We encountered a technical issue. Please call us directly or try again later.');
    }
    redirectWithFlash(
        $_SERVER['HTTP_REFERER'] ?? '/',
        'error',
        'Technical error. Please try again.'
    );
}

// ============================================================
// RECORD RATE LIMIT ENTRY
// ============================================================
$_SESSION['rate_limits'][] = ['ip' => $ip, 'type' => 'enquiry', 'time' => $now];

// ============================================================
// SUCCESS RESPONSE
// ============================================================
$success_message = 'Thank you! Your enquiry has been received. Our team will contact you within 24 hours.';

if ($wants_json) {
    sendJsonResponse(true, $success_message, ['enquiry_id' => $enquiry_id]);
}

redirectWithFlash(
    $_SERVER['HTTP_REFERER'] ?? '/',
    'success',
    $success_message
);
