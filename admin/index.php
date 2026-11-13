<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$pageTitle = 'Atelier CMS & CRM Command Center';

// Metric Counts
$totalProducts  = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$activeCats     = (int)$pdo->query('SELECT COUNT(*) FROM categories WHERE is_active = 1')->fetchColumn();
$totalGallery   = (int)$pdo->query('SELECT COUNT(*) FROM gallery WHERE is_active = 1')->fetchColumn();
$totalReviews   = (int)$pdo->query('SELECT COUNT(*) FROM testimonials WHERE is_active = 1')->fetchColumn();
$newEnquiries   = (int)$pdo->query("SELECT COUNT(*) FROM enquiries WHERE status = 'pending'")->fetchColumn();
$newCustom      = (int)$pdo->query("SELECT COUNT(*) FROM custom_requests WHERE status = 'pending'")->fetchColumn();

// Recent enquiries & custom briefs
$recentEnquiries = $pdo->query('SELECT * FROM enquiries ORDER BY created_at DESC LIMIT 5')->fetchAll();
$recentCustom    = $pdo->query('SELECT * FROM custom_requests ORDER BY created_at DESC LIMIT 5')->fetchAll();

include __DIR__ . '/includes/admin-header.php';
?>

<!-- KPI Metric Cards Grid -->
<div class="dashboard-stats" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:16px;margin-bottom:28px;">
  
  <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);padding:20px;border-radius:3px;">
    <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#B7AA98;display:block;margin-bottom:6px;">Catalogue</span>
    <span style="font-family:'Cormorant Garamond',serif;font-size:2.4rem;color:#D5BE9A;font-weight:600;line-height:1;"><?= $totalProducts ?></span>
    <span style="font-size:0.75rem;color:#D5BE9A;display:block;margin-top:4px;">Furniture Pieces</span>
  </div>

  <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);padding:20px;border-radius:3px;">
    <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#B7AA98;display:block;margin-bottom:6px;">Collections</span>
    <span style="font-family:'Cormorant Garamond',serif;font-size:2.4rem;color:#D5BE9A;font-weight:600;line-height:1;"><?= $activeCats ?></span>
    <span style="font-size:0.75rem;color:#D5BE9A;display:block;margin-top:4px;">Active Categories</span>
  </div>

  <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);padding:20px;border-radius:3px;">
    <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#B7AA98;display:block;margin-bottom:6px;">Visual Gallery</span>
    <span style="font-family:'Cormorant Garamond',serif;font-size:2.4rem;color:#D5BE9A;font-weight:600;line-height:1;"><?= $totalGallery ?></span>
    <span style="font-size:0.75rem;color:#D5BE9A;display:block;margin-top:4px;">Showroom Photos</span>
  </div>

  <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);padding:20px;border-radius:3px;">
    <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#B7AA98;display:block;margin-bottom:6px;">Client Reviews</span>
    <span style="font-family:'Cormorant Garamond',serif;font-size:2.4rem;color:#D5BE9A;font-weight:600;line-height:1;"><?= $totalReviews ?></span>
    <span style="font-size:0.75rem;color:#D5BE9A;display:block;margin-top:4px;">Verified Stories</span>
  </div>

  <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);border-left:3px solid #D5BE9A;padding:20px;border-radius:3px;">
    <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#B7AA98;display:block;margin-bottom:6px;">Pending Enquiries</span>
    <span style="font-family:'Cormorant Garamond',serif;font-size:2.4rem;color:<?= $newEnquiries > 0 ? '#F5F0E8' : '#D5BE9A' ?>;font-weight:600;line-height:1;"><?= $newEnquiries ?></span>
    <span style="font-size:0.75rem;color:<?= $newEnquiries > 0 ? '#D5BE9A' : '#B7AA98' ?>;display:block;margin-top:4px;">Awaiting response</span>
  </div>

  <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);border-left:3px solid #D5BE9A;padding:20px;border-radius:3px;">
    <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#B7AA98;display:block;margin-bottom:6px;">Custom Commissions</span>
    <span style="font-family:'Cormorant Garamond',serif;font-size:2.4rem;color:<?= $newCustom > 0 ? '#F5F0E8' : '#D5BE9A' ?>;font-weight:600;line-height:1;"><?= $newCustom ?></span>
    <span style="font-size:0.75rem;color:<?= $newCustom > 0 ? '#D5BE9A' : '#B7AA98' ?>;display:block;margin-top:4px;">Bespoke briefs</span>
  </div>

</div>

<!-- Quick Action Command Shortcuts -->
<div style="margin-bottom:32px;display:flex;gap:10px;flex-wrap:wrap;">
  <a href="<?= ADMIN_URL ?>/product-edit.php" style="padding:10px 18px;background:#D5BE9A;color:#171614;text-decoration:none;font-size:0.75rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;border-radius:2px;">
    + Add Furniture Piece
  </a>
  <a href="<?= ADMIN_URL ?>/gallery.php" style="padding:10px 18px;background:rgba(213,190,154,0.15);color:#F5F0E8;border:1px solid rgba(213,190,154,0.3);text-decoration:none;font-size:0.75rem;font-weight:600;border-radius:2px;">
    🖼 Upload Gallery Photo
  </a>
  <a href="<?= ADMIN_URL ?>/testimonials.php" style="padding:10px 18px;background:rgba(213,190,154,0.15);color:#F5F0E8;border:1px solid rgba(213,190,154,0.3);text-decoration:none;font-size:0.75rem;font-weight:600;border-radius:2px;">
    ★ Add Testimonial
  </a>
  <a href="<?= ADMIN_URL ?>/content.php" style="padding:10px 18px;background:rgba(213,190,154,0.15);color:#F5F0E8;border:1px solid rgba(213,190,154,0.3);text-decoration:none;font-size:0.75rem;font-weight:600;border-radius:2px;">
    📝 Edit Core Values &amp; CMS
  </a>
  <a href="<?= ADMIN_URL ?>/settings.php" style="padding:10px 18px;background:rgba(213,190,154,0.15);color:#F5F0E8;border:1px solid rgba(213,190,154,0.3);text-decoration:none;font-size:0.75rem;font-weight:600;border-radius:2px;">
    ⚙ Showroom Settings &amp; Maps
  </a>
  <a href="/" target="_blank" style="padding:10px 18px;background:#171614;color:#D5BE9A;border:1px solid #D5BE9A;text-decoration:none;font-size:0.75rem;font-weight:700;border-radius:2px;">
    ↗ View Live Website
  </a>
</div>

<!-- CRM Lead Management Dual Columns -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:28px;">
  
  <!-- Recent Enquiries -->
  <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);padding:24px;border-radius:3px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
      <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:#F5F0E8;font-weight:400;margin:0;">Recent Client Enquiries</h3>
      <a href="<?= ADMIN_URL ?>/enquiries.php" style="color:#D5BE9A;font-size:0.75rem;text-decoration:none;letter-spacing:0.1em;text-transform:uppercase;">View All CRM &rarr;</a>
    </div>

    <?php if (empty($recentEnquiries)): ?>
      <p style="color:#B7AA98;font-size:0.85rem;">No enquiries recorded yet.</p>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:12px;">
        <?php foreach ($recentEnquiries as $enq): ?>
          <div style="padding:14px;background:#171614;border:1px solid rgba(213,190,154,0.1);display:flex;justify-content:space-between;align-items:center;border-radius:2px;">
            <div>
              <strong style="color:#F5F0E8;font-size:0.92rem;display:block;"><?= htmlspecialchars($enq['name']) ?></strong>
              <span style="color:#B7AA98;font-size:0.78rem;"><?= htmlspecialchars($enq['subject'] ?: $enq['product_name'] ?: 'Showroom Request') ?> &bull; <a href="tel:<?= htmlspecialchars($enq['phone']) ?>" style="color:#D5BE9A;text-decoration:none;"><?= htmlspecialchars($enq['phone']) ?></a></span>
            </div>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $enq['phone']) ?>" target="_blank" style="padding:4px 10px;background:#25D366;color:#fff;font-size:0.68rem;font-weight:700;text-decoration:none;border-radius:2px;">
              WhatsApp
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Recent Custom Requests -->
  <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.18);padding:24px;border-radius:3px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
      <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:#F5F0E8;font-weight:400;margin:0;">Bespoke Commissions</h3>
      <a href="<?= ADMIN_URL ?>/custom-requests.php" style="color:#D5BE9A;font-size:0.75rem;text-decoration:none;letter-spacing:0.1em;text-transform:uppercase;">View All Orders &rarr;</a>
    </div>

    <?php if (empty($recentCustom)): ?>
      <p style="color:#B7AA98;font-size:0.85rem;">No bespoke commissions recorded yet.</p>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:12px;">
        <?php foreach ($recentCustom as $cr): ?>
          <div style="padding:14px;background:#171614;border:1px solid rgba(213,190,154,0.1);display:flex;justify-content:space-between;align-items:center;border-radius:2px;">
            <div>
              <strong style="color:#F5F0E8;font-size:0.92rem;display:block;"><?= htmlspecialchars($cr['name']) ?></strong>
              <span style="color:#B7AA98;font-size:0.78rem;">Category: <strong style="color:#D5BE9A;"><?= htmlspecialchars($cr['category'] ?: 'Custom') ?></strong> &bull; <?= htmlspecialchars($cr['budget_range'] ?: 'Bespoke') ?></span>
            </div>
            <a href="<?= ADMIN_URL ?>/custom-requests.php" style="padding:4px 10px;background:rgba(213,190,154,0.2);color:#D5BE9A;font-size:0.68rem;font-weight:700;text-decoration:none;border-radius:2px;">
              Manage
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
