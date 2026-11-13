<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$pageTitle = 'Customer Testimonials & Verified Reviews';

// Handle Delete
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM testimonials WHERE id = ?');
    $stmt->execute([$delId]);
    logActivity($pdo, 'Delete Testimonial', "Deleted testimonial #{$delId}");
    setFlash('success', 'Testimonial removed successfully.');
    redirect(ADMIN_URL . '/testimonials.php');
}

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = (int)($_POST['id'] ?? 0);
    $name       = trim($_POST['name'] ?? '');
    $location   = trim($_POST['location'] ?? 'Visakhapatnam');
    $rating     = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $review     = trim($_POST['review'] ?? '');
    $isVerified = isset($_POST['is_verified']) ? 1 : 0;
    $isActive   = isset($_POST['is_active']) ? 1 : 0;

    if (!empty($name) && !empty($review)) {
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE testimonials SET name = ?, location = ?, rating = ?, review = ?, is_verified = ?, is_active = ? WHERE id = ?');
            $stmt->execute([$name, $location, $rating, $review, $isVerified, $isActive, $id]);
            logActivity($pdo, 'Update Testimonial', "Updated testimonial #{$id} by {$name}");
            setFlash('success', 'Testimonial updated.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO testimonials (name, location, rating, review, is_verified, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$name, $location, $rating, $review, $isVerified, $isActive]);
            logActivity($pdo, 'Add Testimonial', "Added testimonial by {$name}");
            setFlash('success', 'Testimonial created.');
        }
        redirect(ADMIN_URL . '/testimonials.php');
    } else {
        setFlash('danger', 'Name and Review text are required.');
    }
}

// Fetch edit item if set
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM testimonials WHERE id = ?');
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch();
}

$testimonials = $pdo->query('SELECT * FROM testimonials ORDER BY created_at DESC')->fetchAll();

include __DIR__ . '/includes/admin-header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;flex-wrap:wrap;gap:16px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:2rem;color:#F5F0E8;font-weight:400;">Customer Testimonials &amp; Verified Reviews</h2>
    <span style="font-size:0.82rem;color:#B7AA98;">Control reviews displayed on the homepage showcase (Section 7)</span>
  </div>
  <div>
    <span style="font-size:0.8rem;color:#D5BE9A;"><?= count($testimonials) ?> verified testimonials stored</span>
  </div>
</div>

<div style="display:grid;grid-template-columns:380px 1fr;gap:28px;align-items:start;">

  <!-- Form Column -->
  <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.2);padding:24px;border-radius:3px;">
    <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:#D5BE9A;margin-bottom:16px;">
      <?= $editItem ? 'Edit Review #' . $editItem['id'] : 'Add Verified Review' ?>
    </h3>

    <form method="POST" action="" style="display:flex;flex-direction:column;gap:16px;">
      <input type="hidden" name="id" value="<?= $editItem['id'] ?? 0 ?>">

      <div>
        <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Client Name *</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($editItem['name'] ?? '') ?>" placeholder="e.g. Smt. Lakshmi Devi" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.9rem;">
      </div>

      <div>
        <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">City / Neighborhood *</label>
        <input type="text" name="location" required value="<?= htmlspecialchars($editItem['location'] ?? 'Visakhapatnam') ?>" placeholder="e.g. MVP Colony, Vizag" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.9rem;">
      </div>

      <div>
        <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Rating (1 to 5 Stars)</label>
        <select name="rating" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.9rem;">
          <option value="5" <?= ($editItem['rating'] ?? 5) == 5 ? 'selected' : '' ?>>★★★★★ (5 Stars - Exceptional)</option>
          <option value="4" <?= ($editItem['rating'] ?? 5) == 4 ? 'selected' : '' ?>>★★★★☆ (4 Stars - Great)</option>
          <option value="3" <?= ($editItem['rating'] ?? 5) == 3 ? 'selected' : '' ?>>★★★☆☆ (3 Stars - Good)</option>
          <option value="2" <?= ($editItem['rating'] ?? 5) == 2 ? 'selected' : '' ?>>★★☆☆☆ (2 Stars)</option>
          <option value="1" <?= ($editItem['rating'] ?? 5) == 1 ? 'selected' : '' ?>>★☆☆☆☆ (1 Star)</option>
        </select>
      </div>

      <div>
        <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Review Text *</label>
        <textarea name="review" rows="4" required placeholder="Client feedback on craftsmanship, quality, or delivery..." style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.9rem;resize:vertical;line-height:1.6;"><?= htmlspecialchars($editItem['review'] ?? '') ?></textarea>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;color:#F5F0E8;font-size:0.85rem;">
          <input type="checkbox" name="is_verified" value="1" <?= ($editItem['is_verified'] ?? 1) ? 'checked' : '' ?>>
          Verified Client
        </label>
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;color:#F5F0E8;font-size:0.85rem;">
          <input type="checkbox" name="is_active" value="1" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>>
          Active on Site
        </label>
      </div>

      <div style="display:flex;gap:10px;margin-top:10px;">
        <button type="submit" style="flex:1;padding:12px;background:#D5BE9A;color:#171614;border:none;font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;cursor:pointer;border-radius:2px;">
          <?= $editItem ? 'Save Review' : 'Publish Review' ?>
        </button>
        <?php if ($editItem): ?>
          <a href="<?= ADMIN_URL ?>/testimonials.php" style="padding:12px 16px;background:rgba(213,190,154,0.15);color:#F5F0E8;text-decoration:none;font-size:0.8rem;border-radius:2px;">Cancel</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Testimonials List -->
  <div style="display:flex;flex-direction:column;gap:16px;">
    <?php foreach ($testimonials as $t): ?>
      <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);padding:20px;border-radius:3px;display:flex;flex-direction:column;gap:12px;">
        <div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:10px;">
          <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
              <strong style="color:#F5F0E8;font-size:1.1rem;"><?= htmlspecialchars($t['name']) ?></strong>
              <?php if ($t['is_verified']): ?>
                <span style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.1em;background:rgba(213,190,154,0.2);color:#D5BE9A;border:1px solid rgba(213,190,154,0.3);padding:2px 6px;border-radius:2px;">
                  ✓ Verified Buyer
                </span>
              <?php endif; ?>
              <?php if (!$t['is_active']): ?>
                <span style="font-size:0.65rem;text-transform:uppercase;background:rgba(180,40,40,0.85);color:#fff;padding:2px 6px;border-radius:2px;">
                  Hidden
                </span>
              <?php endif; ?>
            </div>
            <span style="color:#D5BE9A;font-size:0.8rem;"><?= htmlspecialchars($t['location']) ?></span>
          </div>

          <div style="display:flex;align-items:center;gap:12px;">
            <span style="color:#D5BE9A;font-size:1rem;letter-spacing:2px;">
              <?= str_repeat('★', (int)$t['rating']) . str_repeat('☆', 5 - (int)$t['rating']) ?>
            </span>
            <a href="<?= ADMIN_URL ?>/testimonials.php?edit=<?= $t['id'] ?>" style="color:#D5BE9A;font-size:0.75rem;text-decoration:none;padding:4px 10px;border:1px solid rgba(213,190,154,0.3);border-radius:2px;">Edit</a>
            <a href="<?= ADMIN_URL ?>/testimonials.php?delete=<?= $t['id'] ?>" onclick="return confirm('Delete this testimonial?');" style="color:#e06c75;font-size:0.75rem;text-decoration:none;padding:4px 10px;border:1px solid rgba(224,108,117,0.3);border-radius:2px;">Delete</a>
          </div>
        </div>

        <p style="color:#D2C8BC;font-size:0.92rem;line-height:1.65;margin:0;font-style:italic;">
          &ldquo;<?= htmlspecialchars($t['review']) ?>&rdquo;
        </p>

        <span style="color:rgba(183,170,152,0.6);font-size:0.72rem;">
          Recorded: <?= formatDate($t['created_at']) ?>
        </span>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
