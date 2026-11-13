<?php
/**
 * Process Custom Request
 * Grand Furniture Vizag
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/custom-request.php');
}

$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$wants_json = $is_ajax || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

function sendJson(bool $success, string $message, array $extra = []): never {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!validateCSRFToken($token)) {
    if ($wants_json) sendJson(false, 'Security verification failed. Please refresh.');
    setFlash('error', 'Security token invalid. Please retry.');
    redirect('/custom-request.php');
}

$name        = sanitize($_POST['name'] ?? '');
$phone       = sanitize($_POST['phone'] ?? '');
$email       = sanitize($_POST['email'] ?? '');
$category    = sanitize($_POST['category'] ?? '');
$dimensions  = sanitize($_POST['dimensions'] ?? '');
$material    = sanitize($_POST['material_preference'] ?? '');
$budget      = sanitize($_POST['budget_range'] ?? '');
$description = sanitize($_POST['description'] ?? '');

if (empty($name) || empty($phone) || empty($description)) {
    if ($wants_json) sendJson(false, 'Please provide your name, phone number, and brief description.');
    setFlash('error', 'Please fill in all required fields.');
    redirect('/custom-request.php');
}

// Handle optional reference image upload
$refImage = null;
if (!empty($_FILES['reference_image']['tmp_name'])) {
    $uploaded = uploadImage($_FILES['reference_image'], 'custom-requests');
    if ($uploaded) {
        $refImage = $uploaded;
    }
}

try {
    $stmt = $pdo->prepare('
        INSERT INTO custom_requests 
            (name, phone, email, category, dimensions, material_preference, budget_range, description, reference_image, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "pending", NOW(), NOW())
    ');
    $stmt->execute([
        $name, $phone, $email, $category, $dimensions, $material, $budget, $description, $refImage
    ]);

    $reqId = $pdo->lastInsertId();
    logActivity($pdo, 'New Custom Request', "Client: {$name} (#{$reqId})");

    $successMsg = 'Thank you! Your custom furniture commission brief has been received. Our master architect will connect with you via WhatsApp/phone shortly.';

    if ($wants_json) {
        sendJson(true, $successMsg, ['id' => $reqId]);
    } else {
        setFlash('success', $successMsg);
        redirect('/custom-request.php?success=1');
    }

} catch (Exception $e) {
    error_log('Custom request error: ' . $e->getMessage());
    if ($wants_json) sendJson(false, 'An unexpected error occurred. Please contact us directly via WhatsApp.');
    setFlash('error', 'Failed to submit request. Please try again.');
    redirect('/custom-request.php');
}
