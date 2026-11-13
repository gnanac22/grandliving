<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$pageTitle = 'Manage Collections & Categories';

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $token = $_POST['csrf_token'] ?? '';
    if (validateCSRFToken($token)) {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '') ?: generateSlug($name);
        $desc = trim($_POST['description'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 0);

        if (!empty($name)) {
            $stmt = $pdo->prepare('INSERT INTO categories (name, slug, description, sort_order, is_active, created_at) VALUES (?, ?, ?, ?, 1, NOW())');
            $stmt->execute([$name, $slug, $desc, $sort]);
            logActivity($pdo, 'Add Category', "Added category: {$name}");
            setFlash('success', "Collection '{$name}' created.");
            redirect(ADMIN_URL . '/categories.php');
        }
    }
}

// Fetch categories with product counts
$categories = $pdo->query('
    SELECT c.*, COUNT(p.id) AS product_count 
    FROM categories c 
    LEFT JOIN products p ON p.category_id = c.id 
    GROUP BY c.id 
    ORDER BY c.sort_order ASC, c.name ASC
')->fetchAll();

include __DIR__ . '/includes/admin-header.php';
?>

<div style="display:grid;grid-template-columns:1fr 1.6fr;gap:36px;align-items:start;">
  
  <!-- Add Category Form -->
  <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);padding:28px;border-radius:3px;">
    <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;color:#D5BE9A;margin-bottom:20px;">Add New Collection</h3>
    <form method="POST" action="" style="display:flex;flex-direction:column;gap:16px;">
      <input type="hidden" name="action" value="add">
      <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Collection Name *</label>
        <input type="text" name="name" required placeholder="e.g. Architectural Accents" data-slug-source="catSlug" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>

      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">URL Slug</label>
        <input type="text" id="catSlug" name="slug" placeholder="architectural-accents" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>

      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Editorial Description</label>
        <textarea name="description" rows="3" placeholder="Brief design summary of the collection..." style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.9rem;resize:vertical;"></textarea>
      </div>

      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Sort Order</label>
        <input type="number" name="sort_order" value="10" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>

      <button type="submit" class="btn btn-primary" style="padding:14px;font-size:0.8rem;margin-top:8px;">Create Collection</button>
    </form>
  </div>

  <!-- Categories Table -->
  <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);border-radius:3px;overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:0.85rem;color:#F5F0E8;text-align:left;">
      <thead>
        <tr style="border-bottom:1px solid rgba(213,190,154,0.15);background:#171614;">
          <th style="padding:16px 20px;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#D5BE9A;">Name</th>
          <th style="padding:16px 20px;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#D5BE9A;">Slug</th>
          <th style="padding:16px 20px;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#D5BE9A;">Pieces</th>
          <th style="padding:16px 20px;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#D5BE9A;">Order</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $cat): ?>
          <tr style="border-bottom:1px solid rgba(213,190,154,0.08);">
            <td style="padding:14px 20px;">
              <strong style="color:#F5F0E8;font-size:0.95rem;display:block;"><?= htmlspecialchars($cat['name']) ?></strong>
              <span style="color:#B7AA98;font-size:0.75rem;"><?= htmlspecialchars(substr($cat['description'] ?? '', 0, 48)) ?>...</span>
            </td>
            <td style="padding:14px 20px;color:#D5BE9A;font-family:monospace;"><?= htmlspecialchars($cat['slug']) ?></td>
            <td style="padding:14px 20px;">
              <span style="background:rgba(213,190,154,0.2);color:#D5BE9A;padding:3px 10px;border-radius:12px;font-size:0.75rem;font-weight:600;">
                <?= $cat['product_count'] ?>
              </span>
            </td>
            <td style="padding:14px 20px;color:#B7AA98;"><?= $cat['sort_order'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
