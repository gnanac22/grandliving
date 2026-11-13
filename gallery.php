<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/frontend-functions.php';

$categoryFilter = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$gallery = getActiveGallery(!empty($categoryFilter) ? $categoryFilter : null, $pdo);

$page_title       = "Artisanal Gallery & Showroom Inspiration — Grand Furniture Vizag";
$page_description = "Browse our luxury furniture showroom gallery and curated room inspirations in Visakhapatnam.";

include 'includes/header.php';
?>

<div style="padding-top:120px;background:#171614;min-height:100vh;color:#F5F0E8;">
  <header style="padding:60px 0 40px;text-align:center;border-bottom:1px solid rgba(213,190,154,0.15);">
    <div class="container">
      <span style="font-family:'Manrope',sans-serif;font-size:0.75rem;font-weight:700;letter-spacing:0.25em;text-transform:uppercase;color:#D5BE9A;display:block;margin-bottom:12px;">Visual Archives</span>
      <h1 style="font-family:'Cormorant Garamond',serif;font-size:clamp(2.5rem, 5vw, 4rem);font-weight:300;color:#F5F0E8;margin-bottom:16px;">
        Showroom &amp; Architectural Spaces
      </h1>
      <p style="font-family:'Manrope',sans-serif;font-size:0.95rem;color:#B7AA98;max-width:640px;margin:0 auto;line-height:1.8;">
        Immerse yourself in spaces shaped by our bespoke masterworks across Andhra Pradesh.
      </p>

      <!-- Category Filter Tabs -->
      <div style="display:flex;justify-content:center;gap:12px;margin-top:32px;flex-wrap:wrap;">
        <a href="/gallery.php" style="padding:8px 20px;border-radius:2px;font-size:0.75rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;text-decoration:none;
           <?= empty($categoryFilter) ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">All Works</a>
        <a href="/gallery.php?category=showroom" style="padding:8px 20px;border-radius:2px;font-size:0.75rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;text-decoration:none;
           <?= $categoryFilter === 'showroom' ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">Showroom Experience</a>
        <a href="/gallery.php?category=furniture" style="padding:8px 20px;border-radius:2px;font-size:0.75rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;text-decoration:none;
           <?= $categoryFilter === 'furniture' ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">Masterpiece Furniture</a>
        <a href="/gallery.php?category=room-inspiration" style="padding:8px 20px;border-radius:2px;font-size:0.75rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;text-decoration:none;
           <?= $categoryFilter === 'room-inspiration' ? 'background:#D5BE9A;color:#171614;' : 'border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;' ?>">Room Inspirations</a>
      </div>
    </div>
  </header>

  <!-- Gallery Grid -->
  <section style="padding:60px 0 100px;">
    <div class="container">
      <div style="columns: 3 300px; column-gap: 24px;">
        <?php foreach ($gallery as $g): ?>
          <figure style="margin-bottom:24px;break-inside:avoid;position:relative;overflow:hidden;border:1px solid rgba(213,190,154,0.15);border-radius:2px;background:#1E1A17;">
            <?php if (!empty($g['image_path']) && file_exists(__DIR__ . $g['image_path'])): ?>
              <img src="<?= htmlspecialchars($g['image_path']) ?>" alt="<?= htmlspecialchars($g['alt_text'] ?: $g['title']) ?>" style="width:100%;display:block;transition:transform 0.5s ease;">
            <?php else: ?>
              <div style="height:280px;background:linear-gradient(135deg, #29221D 0%, #76543B 50%, #171614 100%);display:flex;align-items:center;justify-content:center;padding:20px;text-align:center;">
                <span style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:#D5BE9A;"><?= htmlspecialchars($g['title']) ?></span>
              </div>
            <?php endif; ?>
            <figcaption style="padding:16px 20px;background:rgba(23,22,20,0.9);border-top:1px solid rgba(213,190,154,0.1);">
              <span style="font-size:0.65rem;letter-spacing:0.18em;text-transform:uppercase;color:#D5BE9A;display:block;margin-bottom:4px;"><?= htmlspecialchars($g['category']) ?></span>
              <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.2rem;color:#F5F0E8;font-weight:400;"><?= htmlspecialchars($g['title']) ?></h3>
            </figcaption>
          </figure>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
