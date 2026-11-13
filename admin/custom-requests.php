<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$pageTitle = 'Bespoke Custom Furniture Commissions (CRM)';

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=grand-furniture-custom-commissions-' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Phone', 'Email', 'Category', 'Dimensions', 'Materials', 'Budget', 'Description', 'Status', 'Admin Notes', 'Date']);
    $all = $pdo->query('SELECT id, name, phone, email, category, dimensions, material_preference, budget_range, description, status, admin_notes, created_at FROM custom_requests ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($all as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $reqId  = (int)$_POST['request_id'];
    $status = $_POST['status'] ?? 'pending';
    $notes  = trim($_POST['admin_notes'] ?? '');

    $stmt = $pdo->prepare('UPDATE custom_requests SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([$status, $notes, $reqId]);
    logActivity($pdo, 'Update Custom Request', "Updated custom request #{$reqId} status to {$status}");
    setFlash('success', "Bespoke commission #{$reqId} updated.");
    redirect(ADMIN_URL . '/custom-requests.php');
}

$statusFilter = $_GET['status'] ?? '';
$searchQuery  = trim($_GET['q'] ?? '');

$where = [];
$params = [];
if (!empty($statusFilter)) {
    $where[] = "status = ?";
    $params[] = $statusFilter;
}
if (!empty($searchQuery)) {
    $where[] = "(name LIKE ? OR phone LIKE ? OR email LIKE ? OR category LIKE ? OR description LIKE ?)";
    $params[] = "%{$searchQuery}%";
    $params[] = "%{$searchQuery}%";
    $params[] = "%{$searchQuery}%";
    $params[] = "%{$searchQuery}%";
    $params[] = "%{$searchQuery}%";
}
$whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT * FROM custom_requests {$whereSql} ORDER BY created_at DESC");
$stmt->execute($params);
$requests = $stmt->fetchAll();

// Count badges
$pendingCount = $pdo->query("SELECT COUNT(*) FROM custom_requests WHERE status = 'pending'")->fetchColumn();
$reviewCount  = $pdo->query("SELECT COUNT(*) FROM custom_requests WHERE status = 'reviewing'")->fetchColumn();
$quotedCount  = $pdo->query("SELECT COUNT(*) FROM custom_requests WHERE status = 'quoted'")->fetchColumn();
$prodCount    = $pdo->query("SELECT COUNT(*) FROM custom_requests WHERE status = 'in-production'")->fetchColumn();
$doneCount    = $pdo->query("SELECT COUNT(*) FROM custom_requests WHERE status = 'completed'")->fetchColumn();

include __DIR__ . '/includes/admin-header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:16px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:2rem;color:#F5F0E8;font-weight:400;margin:0 0 4px;">Bespoke Architectural Commissions (CRM)</h2>
    <span style="font-size:0.8rem;color:#B7AA98;"><?= count($requests) ?> custom briefs &bull; <?= $pendingCount ?> pending initial review</span>
  </div>

  <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
    <!-- Search -->
    <form method="GET" action="" style="display:flex;gap:6px;">
      <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Search client name, phone..." style="background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:8px 12px;font-size:0.8rem;border-radius:2px;width:220px;">
      <button type="submit" style="padding:8px 14px;background:#D5BE9A;color:#171614;border:none;font-weight:700;font-size:0.75rem;cursor:pointer;border-radius:2px;">Search</button>
      <?php if (!empty($searchQuery)): ?>
        <a href="<?= ADMIN_URL ?>/custom-requests.php" style="padding:8px 10px;background:rgba(213,190,154,0.15);color:#F5F0E8;text-decoration:none;font-size:0.75rem;border-radius:2px;">Clear</a>
      <?php endif; ?>
    </form>

    <!-- Export CSV -->
    <a href="<?= ADMIN_URL ?>/custom-requests.php?export=csv" style="padding:8px 14px;background:#171614;border:1px solid #D5BE9A;color:#D5BE9A;text-decoration:none;font-size:0.75rem;font-weight:700;border-radius:2px;display:inline-flex;align-items:center;gap:6px;">
      <span>📥</span> Export CSV
    </a>
  </div>
</div>

<!-- Filter Tabs -->
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
  <a href="<?= ADMIN_URL ?>/custom-requests.php" style="padding:6px 14px;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;border-radius:2px;text-decoration:none;
     <?= empty($statusFilter) ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">All (<?= count($requests) ?>)</a>
  <a href="<?= ADMIN_URL ?>/custom-requests.php?status=pending" style="padding:6px 14px;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;border-radius:2px;text-decoration:none;
     <?= $statusFilter === 'pending' ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">Pending (<?= $pendingCount ?>)</a>
  <a href="<?= ADMIN_URL ?>/custom-requests.php?status=reviewing" style="padding:6px 14px;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;border-radius:2px;text-decoration:none;
     <?= $statusFilter === 'reviewing' ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">Reviewing (<?= $reviewCount ?>)</a>
  <a href="<?= ADMIN_URL ?>/custom-requests.php?status=quoted" style="padding:6px 14px;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;border-radius:2px;text-decoration:none;
     <?= $statusFilter === 'quoted' ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">Quoted (<?= $quotedCount ?>)</a>
  <a href="<?= ADMIN_URL ?>/custom-requests.php?status=in-production" style="padding:6px 14px;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;border-radius:2px;text-decoration:none;
     <?= $statusFilter === 'in-production' ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">In Production (<?= $prodCount ?>)</a>
  <a href="<?= ADMIN_URL ?>/custom-requests.php?status=completed" style="padding:6px 14px;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;border-radius:2px;text-decoration:none;
     <?= $statusFilter === 'completed' ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">Completed (<?= $doneCount ?>)</a>
</div>

<div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);border-radius:3px;">
  <?php if (empty($requests)): ?>
    <div style="padding:48px 24px;text-align:center;color:#B7AA98;">No bespoke commission requests matching criteria.</div>
  <?php else: ?>
    <div style="display:flex;flex-direction:column;">
      <?php foreach ($requests as $r): ?>
        <div style="padding:24px;border-bottom:1px solid rgba(213,190,154,0.1);display:flex;flex-direction:column;gap:14px;">
          <div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:12px;">
            <div>
              <strong style="color:#F5F0E8;font-size:1.15rem;display:inline-block;margin-right:12px;"><?= htmlspecialchars($r['name']) ?></strong>
              <a href="tel:<?= htmlspecialchars($r['phone']) ?>" style="color:#D5BE9A;font-size:0.9rem;font-weight:700;text-decoration:none;"><?= htmlspecialchars($r['phone']) ?></a>
              <span style="color:#B7AA98;font-size:0.85rem;margin-left:12px;">&bull; Category: <strong style="color:#F5F0E8;"><?= htmlspecialchars($r['category'] ?: 'Bespoke Item') ?></strong></span>
              <div style="color:rgba(183,170,152,0.6);font-size:0.75rem;margin-top:4px;">
                Commission #<?= $r['id'] ?> &bull; Received: <?= formatDate($r['created_at']) ?>
              </div>
            </div>

            <!-- Status Form -->
            <form method="POST" action="" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
              <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
              <select name="status" style="background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:7px 10px;font-size:0.75rem;border-radius:2px;">
                <option value="pending" <?= $r['status'] === 'pending' ? 'selected' : '' ?>>⏳ Pending</option>
                <option value="reviewing" <?= $r['status'] === 'reviewing' ? 'selected' : '' ?>>🔍 Reviewing</option>
                <option value="quoted" <?= $r['status'] === 'quoted' ? 'selected' : '' ?>>💰 Quoted</option>
                <option value="in-production" <?= $r['status'] === 'in-production' ? 'selected' : '' ?>>🔨 In Production</option>
                <option value="completed" <?= $r['status'] === 'completed' ? 'selected' : '' ?>>✓ Completed</option>
                <option value="cancelled" <?= $r['status'] === 'cancelled' ? 'selected' : '' ?>>✕ Cancelled</option>
              </select>
              <input type="text" name="admin_notes" value="<?= htmlspecialchars($r['admin_notes'] ?? '') ?>" placeholder="Production / pricing note..." style="background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:7px 10px;font-size:0.75rem;width:180px;border-radius:2px;">
              <button type="submit" style="padding:7px 14px;background:#D5BE9A;color:#171614;border:none;font-size:0.75rem;font-weight:700;cursor:pointer;border-radius:2px;">Update</button>
            </form>
          </div>

          <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:12px;background:#171614;padding:16px;border-radius:2px;font-size:0.85rem;">
            <div>
              <span style="color:#D5BE9A;display:block;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;">Desired Dimensions</span>
              <span style="color:#F5F0E8;"><?= htmlspecialchars($r['dimensions'] ?: 'Not Specified') ?></span>
            </div>
            <div>
              <span style="color:#D5BE9A;display:block;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;">Material Preference</span>
              <span style="color:#F5F0E8;"><?= htmlspecialchars($r['material_preference'] ?: 'Designer Recommendation') ?></span>
            </div>
            <div>
              <span style="color:#D5BE9A;display:block;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;">Budget Scope</span>
              <span style="color:#F5F0E8;"><?= htmlspecialchars($r['budget_range'] ?: 'Custom Quote') ?></span>
            </div>
          </div>

          <div style="background:#171614;padding:16px;border-left:3px solid #D5BE9A;border-radius:2px;">
            <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;display:block;margin-bottom:6px;">Design Brief</span>
            <p style="color:#F5F0E8;font-size:0.92rem;line-height:1.6;margin:0;"><?= nl2br(htmlspecialchars($r['description'])) ?></p>
          </div>

          <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $r['phone']) ?>?text=<?= urlencode("Hello {$r['name']}, regarding your bespoke furniture commission for {$r['category']} with Grand Furniture Vizag:") ?>" 
               target="_blank" style="padding:7px 14px;background:#25D366;color:#fff;text-decoration:none;font-size:0.75rem;font-weight:700;border-radius:2px;display:inline-flex;align-items:center;gap:6px;">
              💬 Chat on WhatsApp &rarr;
            </a>
            <a href="tel:<?= htmlspecialchars($r['phone']) ?>" style="padding:7px 14px;background:rgba(213,190,154,0.15);color:#D5BE9A;text-decoration:none;font-size:0.75rem;font-weight:700;border-radius:2px;display:inline-flex;align-items:center;gap:6px;">
              📞 Call Client &rarr;
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
