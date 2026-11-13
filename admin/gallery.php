<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$pageTitle = 'Showroom & Inspiration Gallery Manager';

// Handle delete
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM gallery WHERE id = ?');
    $stmt->execute([$delId]);
    logActivity($pdo, 'Delete Gallery Item', "Deleted gallery image #{$delId}");
    setFlash('success', 'Gallery item deleted successfully.');
    redirect(ADMIN_URL . '/gallery.php');
}

// Handle add / edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId    = (int)($_POST['id'] ?? 0);
    $title     = trim($_POST['title'] ?? '');
    $category  = $_POST['category'] ?? 'showroom';
    $altText   = trim($_POST['alt_text'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $isActive  = isset($_POST['is_active']) ? 1 : 0;
    $imagePath = trim($_POST['image_path_manual'] ?? '');

    // Handle File Upload if provided
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $file     = $_FILES['image_file'];
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed)) {
            $destDir = dirname(__DIR__) . '/uploads/gallery';
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            $cleanName = preg_replace('/[^a-z0-9\-_]/', '', strtolower(pathinfo($file['name'], PATHINFO_FILENAME)));
            $fileName  = $cleanName . '-' . time() . '.' . $ext;
            $target    = $destDir . '/' . $fileName;
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $imagePath = '/uploads/gallery/' . $fileName;
            }
        }
    }

    if (!empty($title)) {
        if ($itemId > 0) {
            if (!empty($imagePath)) {
                $stmt = $pdo->prepare('UPDATE gallery SET title = ?, category = ?, image_path = ?, alt_text = ?, sort_order = ?, is_active = ? WHERE id = ?');
                $stmt->execute([$title, $category, $imagePath, $altText, $sortOrder, $isActive, $itemId]);
            } else {
                $stmt = $pdo->prepare('UPDATE gallery SET title = ?, category = ?, alt_text = ?, sort_order = ?, is_active = ? WHERE id = ?');
                $stmt->execute([$title, $category, $altText, $sortOrder, $isActive, $itemId]);
            }
            logActivity($pdo, 'Update Gallery Item', "Updated gallery item #{$itemId} ({$title})");
            setFlash('success', 'Gallery item updated successfully.');
        } else {
            if (empty($imagePath)) {
                $imagePath = '/uploads/gallery/showroom-entrance.jpg';
            }
            $stmt = $pdo->prepare('INSERT INTO gallery (title, category, image_path, alt_text, sort_order, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$title, $category, $imagePath, $altText, $sortOrder, $isActive]);
            logActivity($pdo, 'Create Gallery Item', "Added new gallery item {$title}");
            setFlash('success', 'New gallery item created.');
        }
        redirect(ADMIN_URL . '/gallery.php');
    } else {
        setFlash('danger', 'Title is required.');
    }
}

// Fetch edit item if requested
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM gallery WHERE id = ?');
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch();
}

$categoryFilter = $_GET['cat'] ?? '';
$where = [];
$params = [];
if (!empty($categoryFilter)) {
    $where[] = "category = ?";
    $params[] = $categoryFilter;
}
$whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $pdo->prepare("SELECT * FROM gallery {$whereSql} ORDER BY sort_order ASC, id DESC");
$stmt->execute($params);
$galleryItems = $stmt->fetchAll();

include __DIR__ . '/includes/admin-header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;flex-wrap:wrap;gap:16px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:2rem;color:#F5F0E8;font-weight:400;">Visual Archives &amp; Gallery</h2>
    <span style="font-size:0.82rem;color:#B7AA98;">Control showroom suites, masterpiece pieces, and room inspirations</span>
  </div>

  <div style="display:flex;gap:8px;flex-wrap:wrap;">
    <a href="<?= ADMIN_URL ?>/gallery.php" style="padding:6px 14px;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;border-radius:2px;text-decoration:none;
       <?= empty($categoryFilter) ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">All (<?= count($galleryItems) ?>)</a>
    <a href="<?= ADMIN_URL ?>/gallery.php?cat=showroom" style="padding:6px 14px;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;border-radius:2px;text-decoration:none;
       <?= $categoryFilter === 'showroom' ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">Showroom</a>
    <a href="<?= ADMIN_URL ?>/gallery.php?cat=furniture" style="padding:6px 14px;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;border-radius:2px;text-decoration:none;
       <?= $categoryFilter === 'furniture' ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">Furniture</a>
    <a href="<?= ADMIN_URL ?>/gallery.php?cat=room-inspiration" style="padding:6px 14px;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;border-radius:2px;text-decoration:none;
       <?= $categoryFilter === 'room-inspiration' ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">Room Inspiration</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:360px 1fr;gap:28px;align-items:start;">

  <!-- Form Column (Add / Edit) -->
  <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.2);padding:24px;border-radius:3px;">
    <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:#D5BE9A;margin-bottom:16px;">
      <?= $editItem ? 'Edit Gallery Piece #' . $editItem['id'] : 'Upload New Gallery Piece' ?>
    </h3>

    <form method="POST" action="" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:16px;">
      <input type="hidden" name="id" value="<?= $editItem['id'] ?? 0 ?>">

      <div>
        <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Display Title *</label>
        <input type="text" name="title" required value="<?= htmlspecialchars($editItem['title'] ?? '') ?>" placeholder="e.g. Master Bedroom Suite" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.9rem;">
      </div>

      <div>
        <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Category *</label>
        <select name="category" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.9rem;">
          <option value="showroom" <?= ($editItem['category'] ?? '') === 'showroom' ? 'selected' : '' ?>>Showroom Experience</option>
          <option value="furniture" <?= ($editItem['category'] ?? '') === 'furniture' ? 'selected' : '' ?>>Masterpiece Furniture</option>
          <option value="room-inspiration" <?= ($editItem['category'] ?? '') === 'room-inspiration' ? 'selected' : '' ?>>Room Inspiration</option>
        </select>
      </div>

      <div>
        <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Upload Image File</label>
        <input type="file" name="image_file" accept="image/*" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:8px;font-size:0.8rem;">
      </div>

      <div>
        <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Or Existing Image Path</label>
        <input type="text" name="image_path_manual" value="<?= htmlspecialchars($editItem['image_path'] ?? '') ?>" placeholder="/uploads/gallery/your-photo.jpg" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.85rem;">
      </div>

      <div>
        <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Alt Text (SEO)</label>
        <input type="text" name="alt_text" value="<?= htmlspecialchars($editItem['alt_text'] ?? '') ?>" placeholder="Descriptive architectural text" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.85rem;">
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
          <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Sort Order</label>
          <input type="number" name="sort_order" value="<?= htmlspecialchars($editItem['sort_order'] ?? 0) ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.85rem;">
        </div>
        <div style="display:flex;align-items:center;margin-top:22px;">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;color:#F5F0E8;font-size:0.85rem;">
            <input type="checkbox" name="is_active" value="1" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>>
            Active on Site
          </label>
        </div>
      </div>

      <div style="display:flex;gap:10px;margin-top:10px;">
        <button type="submit" style="flex:1;padding:12px;background:#D5BE9A;color:#171614;border:none;font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;cursor:pointer;border-radius:2px;">
          <?= $editItem ? 'Save Changes' : 'Upload Piece' ?>
        </button>
        <?php if ($editItem): ?>
          <a href="<?= ADMIN_URL ?>/gallery.php" style="padding:12px 16px;background:rgba(213,190,154,0.15);color:#F5F0E8;text-decoration:none;font-size:0.8rem;border-radius:2px;">Cancel</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Items List Grid -->
  <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(240px, 1fr));gap:20px;">
    <?php foreach ($galleryItems as $item): ?>
      <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);border-radius:3px;overflow:hidden;display:flex;flex-direction:column;">
        <div style="height:160px;position:relative;background:#12100E;overflow:hidden;">
          <?php if (!empty($item['image_path'])): ?>
            <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
          <?php else: ?>
            <div style="height:100%;display:flex;align-items:center;justify-content:center;color:#B7AA98;font-size:0.8rem;">No Image</div>
          <?php endif; ?>
          <span style="position:absolute;top:8px;left:8px;padding:3px 8px;font-size:0.6rem;text-transform:uppercase;letter-spacing:0.12em;background:rgba(23,22,20,0.85);color:#D5BE9A;border:1px solid rgba(213,190,154,0.3);border-radius:2px;">
            <?= htmlspecialchars($item['category']) ?>
          </span>
          <?php if (!$item['is_active']): ?>
            <span style="position:absolute;top:8px;right:8px;padding:3px 8px;font-size:0.6rem;text-transform:uppercase;background:rgba(180,40,40,0.9);color:#fff;border-radius:2px;">
              Hidden
            </span>
          <?php endif; ?>
        </div>

        <div style="padding:14px;flex:1;display:flex;flex-direction:column;justify-content:space-between;">
          <div>
            <h4 style="font-family:'Cormorant Garamond',serif;font-size:1.15rem;color:#F5F0E8;font-weight:400;margin:0 0 4px;"><?= htmlspecialchars($item['title']) ?></h4>
            <span style="font-size:0.7rem;color:#B7AA98;display:block;word-break:break-all;"><?= htmlspecialchars($item['image_path']) ?></span>
          </div>

          <div style="display:flex;justify-content:space-between;align-items:center;margin-top:14px;padding-top:10px;border-top:1px solid rgba(213,190,154,0.1);">
            <span style="font-size:0.7rem;color:#D5BE9A;">Order: #<?= (int)$item['sort_order'] ?></span>
            <div style="display:flex;gap:8px;">
              <a href="<?= ADMIN_URL ?>/gallery.php?edit=<?= $item['id'] ?>" style="color:#D5BE9A;font-size:0.75rem;text-decoration:none;padding:4px 8px;border:1px solid rgba(213,190,154,0.3);border-radius:2px;">Edit</a>
              <a href="<?= ADMIN_URL ?>/gallery.php?delete=<?= $item['id'] ?>" onclick="return confirm('Delete this gallery piece?');" style="color:#e06c75;font-size:0.75rem;text-decoration:none;padding:4px 8px;border:1px solid rgba(224,108,117,0.3);border-radius:2px;">Delete</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
