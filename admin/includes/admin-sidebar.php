<?php
/**
 * Admin Sidebar — Grand Furniture Vizag
 */
$currentFile = basename($_SERVER['PHP_SELF']);
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));

// Quick count queries for badges
$sidePendingEnquiries = 0;
$sidePendingRequests  = 0;
if (isset($pdo)) {
    try {
        $sidePendingEnquiries = (int)$pdo->query("SELECT COUNT(*) FROM enquiries WHERE status = 'pending'")->fetchColumn();
        $sidePendingRequests  = (int)$pdo->query("SELECT COUNT(*) FROM custom_requests WHERE status = 'pending'")->fetchColumn();
    } catch (\Throwable $e) {}
}

function navItem(string $href, string $icon, string $label, string $current, array $aliases = [], int $badge = 0): string
{
    $file    = basename($href);
    $active  = ($file === $current || in_array($current, $aliases)) ? ' active' : '';
    $badgeHtml = $badge > 0 ? "<span style=\"margin-left:auto;background:#D5BE9A;color:#171614;font-size:0.65rem;font-weight:700;padding:2px 7px;border-radius:10px;\">{$badge}</span>" : '';
    return "<a href=\"{$href}\" class=\"nav-item{$active}\"><span class=\"nav-icon\">{$icon}</span><span class=\"nav-label\">{$label}</span>{$badgeHtml}</a>";
}
?>
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-brand">
    <div class="brand-logo">
      <span class="brand-g">G</span>
    </div>
    <div class="brand-text">
      <span class="brand-name">Grand Living</span>
      <span class="brand-sub">CMS &bull; CRM Atelier</span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">
      <span class="nav-section-label">Main</span>
      <?= navItem(ADMIN_URL . '/index.php', '⊞', 'Dashboard', $currentFile) ?>
    </div>

    <div class="nav-section">
      <span class="nav-section-label">Catalogue &amp; Media</span>
      <?= navItem(ADMIN_URL . '/products.php',     '🛋', 'Products',      $currentFile, ['product-edit.php']) ?>
      <?= navItem(ADMIN_URL . '/categories.php',   '📁', 'Categories',    $currentFile, ['category-edit.php']) ?>
      <?= navItem(ADMIN_URL . '/gallery.php',      '🖼', 'Visual Gallery', $currentFile) ?>
      <?= navItem(ADMIN_URL . '/testimonials.php', '★', 'Testimonials',   $currentFile) ?>
    </div>

    <div class="nav-section">
      <span class="nav-section-label">Site Content (CMS)</span>
      <?= navItem(ADMIN_URL . '/content.php', '📝', 'Page Content CMS', $currentFile) ?>
    </div>

    <div class="nav-section">
      <span class="nav-section-label">Client Relations (CRM)</span>
      <?= navItem(ADMIN_URL . '/enquiries.php',       '💬', 'Client Enquiries', $currentFile, ['enquiry-view.php'], $sidePendingEnquiries) ?>
      <?= navItem(ADMIN_URL . '/custom-requests.php', '✏️', 'Custom Requests',  $currentFile, ['custom-request-view.php'], $sidePendingRequests) ?>
    </div>

    <div class="nav-section">
      <span class="nav-section-label">Configuration</span>
      <?= navItem(ADMIN_URL . '/settings.php', '⚙', 'Website Settings', $currentFile) ?>
      <a href="/" target="_blank" class="nav-item">
        <span class="nav-icon">↗</span>
        <span class="nav-label">View Live Website</span>
      </a>
      <?= navItem(ADMIN_URL . '/logout.php', '⏻', 'Logout', $currentFile) ?>
    </div>
  </nav>

  <div class="sidebar-footer">
    <span class="sidebar-version">Grand Living Atelier v2.0</span>
  </div>
</aside>
