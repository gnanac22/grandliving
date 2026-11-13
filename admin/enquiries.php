<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$pageTitle = 'Showroom & Product Enquiries (CRM)';

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=grand-furniture-enquiries-' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Phone', 'Email', 'Subject', 'Message', 'Product', 'Status', 'Admin Notes', 'Date']);
    $all = $pdo->query('SELECT id, name, phone, email, subject, message, product_name, status, admin_notes, created_at FROM enquiries ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($all as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

// Handle status & notes updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enquiry_id'])) {
    $enqId  = (int)$_POST['enquiry_id'];
    $status = $_POST['status'] ?? 'pending';
    $notes  = trim($_POST['admin_notes'] ?? '');

    $stmt = $pdo->prepare('UPDATE enquiries SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([$status, $notes, $enqId]);
    logActivity($pdo, 'Update Enquiry', "Updated enquiry #{$enqId} status to {$status}");
    setFlash('success', "Enquiry #{$enqId} updated.");
    redirect(ADMIN_URL . '/enquiries.php');
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
    $where[] = "(name LIKE ? OR phone LIKE ? OR email LIKE ? OR message LIKE ? OR subject LIKE ?)";
    $params[] = "%{$searchQuery}%";
    $params[] = "%{$searchQuery}%";
    $params[] = "%{$searchQuery}%";
    $params[] = "%{$searchQuery}%";
    $params[] = "%{$searchQuery}%";
}
$whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT * FROM enquiries {$whereSql} ORDER BY created_at DESC");
$stmt->execute($params);
$enquiries = $stmt->fetchAll();

// Count badges
$pendingCount = $pdo->query("SELECT COUNT(*) FROM enquiries WHERE status = 'pending'")->fetchColumn();
$inProgCount  = $pdo->query("SELECT COUNT(*) FROM enquiries WHERE status = 'in-progress'")->fetchColumn();
$resolvedCount = $pdo->query("SELECT COUNT(*) FROM enquiries WHERE status = 'resolved'")->fetchColumn();

include __DIR__ . '/includes/admin-header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:16px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:2rem;color:#F5F0E8;font-weight:400;margin:0 0 4px;">Client Enquiries &amp; Showroom Leads (CRM)</h2>
    <span style="font-size:0.8rem;color:#B7AA98;"><?= count($enquiries) ?> records found &bull; <?= $pendingCount ?> pending action</span>
  </div>

  <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
    <!-- Search Form -->
    <form method="GET" action="" style="display:flex;gap:6px;">
      <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Search client name, phone..." style="background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:8px 12px;font-size:0.8rem;border-radius:2px;width:220px;">
      <button type="submit" style="padding:8px 14px;background:#D5BE9A;color:#171614;border:none;font-weight:700;font-size:0.75rem;cursor:pointer;border-radius:2px;">Search</button>
      <?php if (!empty($searchQuery)): ?>
        <a href="<?= ADMIN_URL ?>/enquiries.php" style="padding:8px 10px;background:rgba(213,190,154,0.15);color:#F5F0E8;text-decoration:none;font-size:0.75rem;border-radius:2px;">Clear</a>
      <?php endif; ?>
    </form>

    <!-- Export CSV -->
    <a href="<?= ADMIN_URL ?>/enquiries.php?export=csv" style="padding:8px 14px;background:#171614;border:1px solid #D5BE9A;color:#D5BE9A;text-decoration:none;font-size:0.75rem;font-weight:700;border-radius:2px;display:inline-flex;align-items:center;gap:6px;">
      <span>📥</span> Export CSV
    </a>
  </div>
</div>

<!-- Filter Tabs -->
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
  <a href="<?= ADMIN_URL ?>/enquiries.php" style="padding:6px 14px;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;border-radius:2px;text-decoration:none;
     <?= empty($statusFilter) ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">All (<?= $pendingCount + $inProgCount + $resolvedCount ?>)</a>
  <a href="<?= ADMIN_URL ?>/enquiries.php?status=pending" style="padding:6px 14px;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;border-radius:2px;text-decoration:none;
     <?= $statusFilter === 'pending' ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">Pending (<?= $pendingCount ?>)</a>
  <a href="<?= ADMIN_URL ?>/enquiries.php?status=in-progress" style="padding:6px 14px;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;border-radius:2px;text-decoration:none;
     <?= $statusFilter === 'in-progress' ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">In Progress (<?= $inProgCount ?>)</a>
  <a href="<?= ADMIN_URL ?>/enquiries.php?status=resolved" style="padding:6px 14px;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;border-radius:2px;text-decoration:none;
     <?= $statusFilter === 'resolved' ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">Resolved (<?= $resolvedCount ?>)</a>
</div>

<div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);border-radius:3px;">
  <?php if (empty($enquiries)): ?>
    <div style="padding:48px 24px;text-align:center;color:#B7AA98;">No enquiries found matching criteria.</div>
  <?php else: ?>
    <div style="display:flex;flex-direction:column;">
      <?php foreach ($enquiries as $e): ?>
        <div style="padding:24px;border-bottom:1px solid rgba(213,190,154,0.1);display:flex;flex-direction:column;gap:14px;">
          <div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:12px;">
            <div>
              <strong style="color:#F5F0E8;font-size:1.15rem;display:inline-block;margin-right:12px;"><?= htmlspecialchars($e['name']) ?></strong>
              <a href="tel:<?= htmlspecialchars($e['phone']) ?>" style="color:#D5BE9A;font-size:0.9rem;font-weight:700;text-decoration:none;"><?= htmlspecialchars($e['phone']) ?></a>
              <?php if (!empty($e['email'])): ?>
                <span style="color:#B7AA98;font-size:0.85rem;margin-left:12px;">&bull; <a href="mailto:<?= htmlspecialchars($e['email']) ?>" style="color:#B7AA98;text-decoration:none;"><?= htmlspecialchars($e['email']) ?></a></span>
              <?php endif; ?>
              <div style="color:rgba(183,170,152,0.6);font-size:0.75rem;margin-top:4px;">
                Received: <?= formatDate($e['created_at']) ?> &bull; Lead #<?= $e['id'] ?>
              </div>
            </div>

            <!-- Status & Internal Notes Form -->
            <form method="POST" action="" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
              <input type="hidden" name="enquiry_id" value="<?= $e['id'] ?>">
              <select name="status" style="background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:7px 10px;font-size:0.75rem;border-radius:2px;">
                <option value="pending" <?= $e['status'] === 'pending' ? 'selected' : '' ?>>⏳ Pending</option>
                <option value="in-progress" <?= $e['status'] === 'in-progress' ? 'selected' : '' ?>>⚡ In Progress</option>
                <option value="resolved" <?= $e['status'] === 'resolved' ? 'selected' : '' ?>>✓ Resolved</option>
              </select>
              <input type="text" name="admin_notes" value="<?= htmlspecialchars($e['admin_notes'] ?? '') ?>" placeholder="Internal CRM note..." style="background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:7px 10px;font-size:0.75rem;width:180px;border-radius:2px;">
              <button type="submit" style="padding:7px 14px;background:#D5BE9A;color:#171614;border:none;font-size:0.75rem;font-weight:700;cursor:pointer;border-radius:2px;">Update</button>
            </form>
          </div>

          <div style="background:#171614;padding:16px;border-left:3px solid #D5BE9A;border-radius:2px;">
            <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;display:block;margin-bottom:6px;">
              Subject: <?= htmlspecialchars($e['subject'] ?: $e['product_name'] ?: 'Showroom Consultation Request') ?>
            </span>
            <p style="color:#F5F0E8;font-size:0.92rem;line-height:1.6;margin:0;"><?= nl2br(htmlspecialchars($e['message'])) ?></p>
          </div>

          <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $e['phone']) ?>?text=<?= urlencode("Hello {$e['name']}, thank you for connecting with Grand Furniture Vizag regarding your request. How may our design atelier assist you today?") ?>" 
               target="_blank" style="padding:7px 14px;background:#25D366;color:#fff;text-decoration:none;font-size:0.75rem;font-weight:700;border-radius:2px;display:inline-flex;align-items:center;gap:6px;">
              💬 Direct WhatsApp Chat &rarr;
            </a>
            <a href="tel:<?= htmlspecialchars($e['phone']) ?>" style="padding:7px 14px;background:rgba(213,190,154,0.15);color:#D5BE9A;text-decoration:none;font-size:0.75rem;font-weight:700;border-radius:2px;display:inline-flex;align-items:center;gap:6px;">
              📞 Call Client Directly &rarr;
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
