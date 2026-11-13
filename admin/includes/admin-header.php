<?php
/**
 * Admin Header Partial — Grand Furniture Vizag
 * @var string $pageTitle
 */
requireLogin();
$admin    = getCurrentAdmin();
$flash    = displayFlash();
$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — Grand Furniture Vizag</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap">
<link rel="stylesheet" href="<?= ADMIN_URL ?>/assets/admin.css">
<meta name="csrf-token" content="<?= htmlspecialchars($csrfToken) ?>">
</head>
<body class="admin-body">

<div class="admin-layout">
<!-- Sidebar -->
<?php include __DIR__ . '/admin-sidebar.php'; ?>

<!-- Main Content -->
<div class="admin-main">
  <!-- Top Bar -->
  <header class="admin-topbar">
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
      <span></span><span></span><span></span>
    </button>
    <div class="topbar-title">
      <h1 class="page-heading"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
    </div>
    <div class="topbar-actions">
      <a href="<?= SITE_URL ?>" target="_blank" class="btn btn-ghost btn-sm">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        View Site
      </a>
      <div class="admin-user-menu">
        <button class="user-avatar" id="userMenuBtn">
          <span class="avatar-initials"><?= strtoupper(substr($admin['name'], 0, 2)) ?></span>
          <span class="admin-name"><?= htmlspecialchars($admin['name']) ?></span>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="user-dropdown" id="userDropdown">
          <a href="<?= ADMIN_URL ?>/profile.php">Profile</a>
          <a href="<?= ADMIN_URL ?>/settings.php">Settings</a>
          <hr>
          <a href="<?= ADMIN_URL ?>/logout.php" class="text-danger">Logout</a>
        </div>
      </div>
    </div>
  </header>

  <!-- Flash Message -->
  <?php if ($flash): ?>
  <div class="flash-container"><?= $flash ?></div>
  <?php endif; ?>

  <!-- Page Content -->
  <main class="admin-content">
