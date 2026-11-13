<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$pageTitle = 'Manage Furniture Collection';

// Handle deletion
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$delId]);
    logActivity($pdo, 'Delete Product', "Deleted product ID: {$delId}");
    setFlash('success', 'Product was successfully removed.');
    redirect(ADMIN_URL . '/products.php');
}

// Fetch all products
$products = $pdo->query('
    SELECT p.*, c.name AS category_name,
           (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image
    FROM products p
    JOIN categories c ON c.id = p.category_id
    ORDER BY p.sort_order ASC, p.created_at DESC
')->fetchAll();

include __DIR__ . '/includes/admin-header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:2rem;color:#F5F0E8;font-weight:400;">Furniture Collection</h2>
    <span style="font-size:0.8rem;color:#B7AA98;"><?= count($products) ?> pieces currently listed</span>
  </div>
  <a href="<?= ADMIN_URL ?>/product-edit.php" class="btn btn-primary" style="padding:10px 20px;font-size:0.75rem;">+ Add New Piece</a>
</div>

<div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);border-radius:3px;overflow-x:auto;">
  <table style="width:100%;border-collapse:collapse;font-size:0.85rem;color:#F5F0E8;text-align:left;">
    <thead>
      <tr style="border-bottom:1px solid rgba(213,190,154,0.15);background:#171614;">
        <th style="padding:16px 20px;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#D5BE9A;">Piece Name</th>
        <th style="padding:16px 20px;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#D5BE9A;">Category</th>
        <th style="padding:16px 20px;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#D5BE9A;">SKU</th>
        <th style="padding:16px 20px;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#D5BE9A;">Price Range</th>
        <th style="padding:16px 20px;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#D5BE9A;">Status</th>
        <th style="padding:16px 20px;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#D5BE9A;text-align:right;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($products)): ?>
        <tr>
          <td colspan="6" style="padding:40px 20px;text-align:center;color:#B7AA98;">No furniture pieces found. Add your first piece above.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($products as $p): ?>
          <tr style="border-bottom:1px solid rgba(213,190,154,0.08);transition:background 0.2s;" onmouseover="this.style.background='rgba(213,190,154,0.04)'" onmouseout="this.style.background='transparent'">
            <td style="padding:14px 20px;">
              <strong style="display:block;color:#F5F0E8;font-size:0.95rem;"><?= htmlspecialchars($p['name']) ?></strong>
              <?php if ($p['is_featured']): ?>
                <span style="font-size:0.65rem;color:#D5BE9A;letter-spacing:0.1em;text-transform:uppercase;">★ Featured</span>
              <?php endif; ?>
            </td>
            <td style="padding:14px 20px;color:#B7AA98;"><?= htmlspecialchars($p['category_name']) ?></td>
            <td style="padding:14px 20px;color:#D5BE9A;font-family:monospace;"><?= htmlspecialchars($p['sku'] ?: '—') ?></td>
            <td style="padding:14px 20px;color:#F5F0E8;"><?= htmlspecialchars($p['price_range'] ?: 'Custom Quote') ?></td>
            <td style="padding:14px 20px;">
              <span style="padding:4px 8px;border-radius:2px;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;
                           <?= $p['is_active'] ? 'background:rgba(74,124,89,0.2);color:#7AC493;' : 'background:rgba(163,59,42,0.2);color:#FFAAA0;' ?>">
                <?= $p['is_active'] ? 'Published' : 'Draft' ?>
              </span>
            </td>
            <td style="padding:14px 20px;text-align:right;">
              <a href="<?= ADMIN_URL ?>/product-edit.php?id=<?= $p['id'] ?>" style="color:#D5BE9A;text-decoration:none;margin-right:14px;font-weight:600;">Edit</a>
              <a href="<?= SITE_URL ?>/product.php?slug=<?= urlencode($p['slug']) ?>" target="_blank" style="color:#B7AA98;text-decoration:none;margin-right:14px;">View</a>
              <a href="<?= ADMIN_URL ?>/products.php?delete=<?= $p['id'] ?>" data-confirm="Are you sure you want to delete '<?= htmlspecialchars($p['name']) ?>'?" style="color:#FFAAA0;text-decoration:none;">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
