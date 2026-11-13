<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;
$pageTitle = $isEdit ? 'Edit Furniture Piece' : 'Add New Furniture Piece';

$categories = getAllCategories($pdo);

$product = [
    'name' => '',
    'slug' => '',
    'sku' => '',
    'category_id' => $categories[0]['id'] ?? 1,
    'short_description' => '',
    'description' => '',
    'price_range' => '',
    'dimensions' => '',
    'materials' => '',
    'colors' => '',
    'finishes' => '',
    'availability' => 'made-to-order',
    'is_featured' => 0,
    'is_active' => 1,
    'sort_order' => 0,
    'meta_title' => '',
    'meta_description' => ''
];

if ($isEdit) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if (!$existing) {
        setFlash('error', 'Product not found.');
        redirect(ADMIN_URL . '/products.php');
    }
    $product = array_merge($product, $existing);
}

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($token)) {
        setFlash('error', 'Invalid security token.');
    } else {
        $name        = trim($_POST['name'] ?? '');
        $slug        = trim($_POST['slug'] ?? '') ?: generateSlug($name);
        $sku         = trim($_POST['sku'] ?? '');
        $categoryId  = (int)($_POST['category_id'] ?? 1);
        $shortDesc   = trim($_POST['short_description'] ?? '');
        $desc        = trim($_POST['description'] ?? '');
        $priceRange  = trim($_POST['price_range'] ?? '');
        $dimensions  = trim($_POST['dimensions'] ?? '');
        $materials   = trim($_POST['materials'] ?? '');
        $colors      = trim($_POST['colors'] ?? '');
        $finishes    = trim($_POST['finishes'] ?? '');
        $availability= $_POST['availability'] ?? 'made-to-order';
        $isFeatured  = isset($_POST['is_featured']) ? 1 : 0;
        $isActive    = isset($_POST['is_active']) ? 1 : 0;
        $sortOrder   = (int)($_POST['sort_order'] ?? 0);
        $metaTitle   = trim($_POST['meta_title'] ?? '');
        $metaDesc    = trim($_POST['meta_description'] ?? '');

        if (empty($name)) {
            setFlash('error', 'Product name is required.');
        } else {
            if ($isEdit) {
                $stmt = $pdo->prepare('
                    UPDATE products SET 
                        category_id = ?, name = ?, slug = ?, sku = ?, description = ?, short_description = ?,
                        price_range = ?, dimensions = ?, materials = ?, colors = ?, finishes = ?,
                        availability = ?, is_featured = ?, is_active = ?, sort_order = ?,
                        meta_title = ?, meta_description = ?, updated_at = NOW()
                    WHERE id = ?
                ');
                $stmt->execute([
                    $categoryId, $name, $slug, $sku, $desc, $shortDesc,
                    $priceRange, $dimensions, $materials, $colors, $finishes,
                    $availability, $isFeatured, $isActive, $sortOrder,
                    $metaTitle, $metaDesc, $id
                ]);
                logActivity($pdo, 'Edit Product', "Updated product: {$name} (#{$id})");
                setFlash('success', "Piece '{$name}' was successfully updated.");
            } else {
                $stmt = $pdo->prepare('
                    INSERT INTO products 
                        (category_id, name, slug, sku, description, short_description,
                         price_range, dimensions, materials, colors, finishes,
                         availability, is_featured, is_active, sort_order,
                         meta_title, meta_description, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ');
                $stmt->execute([
                    $categoryId, $name, $slug, $sku, $desc, $shortDesc,
                    $priceRange, $dimensions, $materials, $colors, $finishes,
                    $availability, $isFeatured, $isActive, $sortOrder,
                    $metaTitle, $metaDesc
                ]);
                $id = (int)$pdo->lastInsertId();
                logActivity($pdo, 'Create Product', "Created product: {$name} (#{$id})");
                setFlash('success', "New piece '{$name}' has been added to the catalogue.");
            }

            // Handle optional image upload
            if (!empty($_FILES['image']['tmp_name'])) {
                $imgPath = uploadImage($_FILES['image'], 'products');
                if ($imgPath) {
                    $imgStmt = $pdo->prepare('INSERT INTO product_images (product_id, image_path, alt_text, is_primary, sort_order) VALUES (?, ?, ?, 1, 0)');
                    $imgStmt->execute([$id, $imgPath, $name]);
                }
            }

            redirect(ADMIN_URL . '/products.php');
        }
    }
}

include __DIR__ . '/includes/admin-header.php';
?>

<div style="max-width:900px;background:#1E1A17;border:1px solid rgba(213,190,154,0.18);padding:36px;border-radius:3px;">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;">
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:2rem;color:#F5F0E8;font-weight:400;"><?= $pageTitle ?></h2>
    <a href="<?= ADMIN_URL ?>/products.php" style="color:#B7AA98;text-decoration:none;font-size:0.85rem;">&larr; Back to Catalogue</a>
  </div>

  <form method="POST" action="" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:20px;">
    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Piece Name *</label>
        <input type="text" name="name" data-slug-source="slugInput" required value="<?= htmlspecialchars($product['name']) ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Category *</label>
        <select name="category_id" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Slug (URL identifier)</label>
        <input type="text" id="slugInput" name="slug" value="<?= htmlspecialchars($product['slug']) ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">SKU Code</label>
        <input type="text" name="sku" value="<?= htmlspecialchars($product['sku']) ?>" placeholder="GF-LR-001" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Price Range</label>
        <input type="text" name="price_range" value="<?= htmlspecialchars($product['price_range']) ?>" placeholder="₹1,20,000 – ₹1,80,000" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
    </div>

    <div>
      <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Short Editorial Teaser (Cards &amp; Previews)</label>
      <input type="text" name="short_description" value="<?= htmlspecialchars($product['short_description']) ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
    </div>

    <div>
      <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Full Architectural Description</label>
      <textarea name="description" rows="5" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;resize:vertical;"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Dimensions</label>
        <input type="text" name="dimensions" value="<?= htmlspecialchars($product['dimensions']) ?>" placeholder="W 320 × D 230 × H 85 cm" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Materials Sourced</label>
        <input type="text" name="materials" value="<?= htmlspecialchars($product['materials']) ?>" placeholder="Solid Teak, Full-Grain Italian Leather" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Color Palettes</label>
        <input type="text" name="colors" value="<?= htmlspecialchars($product['colors']) ?>" placeholder="Cognac, Ivory, Charcoal" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Finishes</label>
        <input type="text" name="finishes" value="<?= htmlspecialchars($product['finishes']) ?>" placeholder="Hand-stitched leather, Matte teak oil" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Availability</label>
        <select name="availability" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
          <option value="made-to-order" <?= $product['availability'] === 'made-to-order' ? 'selected' : '' ?>>Made to Order</option>
          <option value="in-stock" <?= $product['availability'] === 'in-stock' ? 'selected' : '' ?>>In Stock</option>
          <option value="out-of-stock" <?= $product['availability'] === 'out-of-stock' ? 'selected' : '' ?>>Out of Stock</option>
        </select>
      </div>
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Upload Primary Photography</label>
        <input type="file" name="image" accept="image/*" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.85rem;">
      </div>
    </div>

    <div style="display:flex;gap:30px;padding:16px 0;">
      <label style="display:inline-flex;align-items:center;gap:8px;font-size:0.85rem;color:#F5F0E8;cursor:pointer;">
        <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?>>
        Feature on Homepage
      </label>
      <label style="display:inline-flex;align-items:center;gap:8px;font-size:0.85rem;color:#F5F0E8;cursor:pointer;">
        <input type="checkbox" name="is_active" value="1" <?= $product['is_active'] ? 'checked' : '' ?>>
        Published &amp; Active
      </label>
    </div>

    <div style="display:flex;gap:16px;margin-top:10px;">
      <button type="submit" class="btn btn-primary" style="padding:14px 28px;font-size:0.85rem;">Save Furniture Piece</button>
      <a href="<?= ADMIN_URL ?>/products.php" class="btn btn-ghost" style="padding:14px 28px;font-size:0.85rem;border:1px solid rgba(213,190,154,0.3);">Cancel</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
