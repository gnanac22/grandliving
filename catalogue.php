<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/frontend-functions.php';

$categorySlug = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$search       = isset($_GET['search'])   ? sanitize($_GET['search'])   : '';
$sort         = isset($_GET['sort'])     ? sanitize($_GET['sort'])     : 'newest';
$page         = max(1, (int)($_GET['page'] ?? 1));
$perPage      = 12;

$categories = getAllCategories($pdo);

// Find active category
$activeCategory = null;
if (!empty($categorySlug)) {
    foreach ($categories as $cat) {
        if ($cat['slug'] === $categorySlug) {
            $activeCategory = $cat;
            break;
        }
    }
}

// Build query
$where = ["p.is_active = 1"];
$params = [];

if ($activeCategory) {
    $where[] = "p.category_id = :cat_id";
    $params[':cat_id'] = $activeCategory['id'];
}

if (!empty($search)) {
    $where[] = "(p.name LIKE :search OR p.description LIKE :search OR p.materials LIKE :search)";
    $params[':search'] = "%{$search}%";
}

$orderBy = "p.sort_order ASC, p.created_at DESC";
if ($sort === 'name-asc') {
    $orderBy = "p.name ASC";
} elseif ($sort === 'name-desc') {
    $orderBy = "p.name DESC";
} elseif ($sort === 'featured') {
    $orderBy = "p.is_featured DESC, p.sort_order ASC";
}

$whereSql = implode(' AND ', $where);

// Count total
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products p WHERE {$whereSql}");
foreach ($params as $k => $v) {
    $countStmt->bindValue($k, $v);
}
$countStmt->execute();
$totalProducts = (int) $countStmt->fetchColumn();

// Fetch products
$offset = ($page - 1) * $perPage;
$query = "
    SELECT p.*, c.name AS category_name, c.slug AS category_slug,
           (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image
    FROM products p
    JOIN categories c ON c.id = p.category_id
    WHERE {$whereSql}
    ORDER BY {$orderBy}
    LIMIT :offset, :perPage
";

$stmt = $pdo->prepare($query);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

$pagination = paginate($totalProducts, $perPage, $page);

$page_title       = $activeCategory ? "{$activeCategory['name']} Collection — Grand Furniture Vizag" : "Luxury Furniture Collection — Grand Furniture Vizag";
$page_description = "Explore Grand Furniture's handcrafted designer furniture collections in Visakhapatnam. Made from teak, sheesham, marble and premium Italian leathers.";
$whatsapp_default = getSetting('whatsapp_number', $pdo) ?: '919052751666';

include 'includes/header.php';
?>

<div style="padding-top: 120px; background: #171614; min-height: 100vh;">
  <!-- Catalogue Hero -->
  <header style="padding: 60px 0 40px; text-align: center; border-bottom: 1px solid rgba(213,190,154,0.15);">
    <div class="container">
      <span style="font-family:'Manrope',sans-serif;font-size:0.75rem;font-weight:700;letter-spacing:0.25em;text-transform:uppercase;color:#D5BE9A;display:block;margin-bottom:12px;">The Grand Collection</span>
      <h1 style="font-family:'Cormorant Garamond',serif;font-size:clamp(2.5rem, 5vw, 4rem);font-weight:300;color:#F5F0E8;margin-bottom:16px;">
        <?= $activeCategory ? htmlspecialchars($activeCategory['name']) : 'Curated Living Spaces' ?>
      </h1>
      <p style="font-family:'Manrope',sans-serif;font-size:0.95rem;color:#B7AA98;max-width:640px;margin:0 auto;line-height:1.8;">
        <?= $activeCategory ? htmlspecialchars($activeCategory['description']) : 'Every piece is an enduring statement of refined architecture, artisanal craftsmanship, and uncompromising comfort.' ?>
      </p>
    </div>
  </header>

  <!-- Filter & Search Bar -->
  <div style="background:#1A1816;border-bottom:1px solid rgba(213,190,154,0.12);padding:24px 0;position:sticky;top:80px;z-index:90;backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);">
    <div class="container">
      <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:18px;">
        <!-- Category Pills -->
        <div class="cat-pills-wrap" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
          <a href="/catalogue.php" class="cat-pill <?= empty($categorySlug) ? 'is-active' : '' ?>"
             style="<?= empty($categorySlug) ? 'background:#D5BE9A;color:#171614;border:1px solid #D5BE9A;' : 'background:rgba(23,22,20,0.6);border:1px solid rgba(213,190,154,0.25);color:#F5F0E8;' ?>">
            All (<?= $totalProducts ?>)
          </a>
          <?php foreach ($categories as $cat): 
            $isActive = ($categorySlug === $cat['slug']);
          ?>
            <a href="/catalogue.php?category=<?= urlencode($cat['slug']) ?>" class="cat-pill <?= $isActive ? 'is-active' : '' ?>"
               style="<?= $isActive ? 'background:#D5BE9A;color:#171614;border:1px solid #D5BE9A;' : 'background:rgba(23,22,20,0.6);border:1px solid rgba(213,190,154,0.25);color:#F5F0E8;' ?>">
              <?= htmlspecialchars($cat['name']) ?>
            </a>
          <?php endforeach; ?>
        </div>

        <!-- Search & Sort -->
        <form method="GET" action="/catalogue.php" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
          <?php if (!empty($categorySlug)): ?>
            <input type="hidden" name="category" value="<?= htmlspecialchars($categorySlug) ?>">
          <?php endif; ?>
          <div style="position:relative;">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search pieces..." 
                   style="background:rgba(23,22,20,0.9);border:1px solid rgba(213,190,154,0.25);color:#F5F0E8;padding:9px 16px;border-radius:2px;font-size:0.82rem;font-family:'Manrope',sans-serif;width:clamp(160px, 20vw, 220px);outline:none;transition:border-color 0.3s, box-shadow 0.3s;">
          </div>
          <select name="sort" onchange="this.form.submit()" 
                  style="background:rgba(23,22,20,0.9);border:1px solid rgba(213,190,154,0.25);color:#F5F0E8;padding:9px 14px;border-radius:2px;font-size:0.82rem;font-family:'Manrope',sans-serif;outline:none;cursor:pointer;">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
            <option value="featured" <?= $sort === 'featured' ? 'selected' : '' ?>>Featured Pieces</option>
            <option value="name-asc" <?= $sort === 'name-asc' ? 'selected' : '' ?>>Name: A to Z</option>
            <option value="name-desc" <?= $sort === 'name-desc' ? 'selected' : '' ?>>Name: Z to A</option>
          </select>
        </form>
      </div>
    </div>
  </div>

  <style>
    .cat-pill {
      display: inline-flex;
      align-items: center;
      padding: 8px 18px;
      border-radius: 2px;
      font-size: 0.74rem;
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      text-decoration: none;
      transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .cat-pill:hover {
      border-color: #D5BE9A !important;
      color: #D5BE9A !important;
      transform: translateY(-2px);
    }
    .cat-pill.is-active {
      box-shadow: 0 4px 15px rgba(213, 190, 154, 0.3);
    }
    .catalogue-card {
      background: #1C1916;
      border: 1px solid rgba(213, 190, 154, 0.15);
      border-radius: 3px;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.45s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.45s ease;
    }
    .catalogue-card:hover {
      transform: translateY(-8px);
      border-color: rgba(213, 190, 154, 0.45);
      box-shadow: 0 24px 50px rgba(0, 0, 0, 0.65), 0 0 25px rgba(213, 190, 154, 0.12);
    }
    .catalogue-card:hover .catalogue-card__img {
      transform: scale(1.08);
    }
    .catalogue-card__img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .catalogue-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 32px;
    }
    @media (max-width: 768px) {
      .cat-pills-wrap {
        overflow-x: auto;
        white-space: nowrap;
        flex-wrap: nowrap !important;
        width: 100%;
        padding-bottom: 6px;
        -webkit-overflow-scrolling: touch;
      }
      .cat-pills-wrap::-webkit-scrollbar {
        display: none;
      }
      .cat-pill {
        flex-shrink: 0;
      }
      .catalogue-grid {
        grid-template-columns: 1fr;
        gap: 24px;
      }
    }
  </style>

  <!-- Products Grid -->
  <section style="padding: 60px 0 100px;">
    <div class="container">
      <?php if (empty($products)): ?>
        <div style="text-align:center;padding:80px 20px;border:1px dashed rgba(213,190,154,0.2);border-radius:4px;">
          <p style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;color:#F5F0E8;margin-bottom:12px;">No pieces found in this category.</p>
          <p style="color:#B7AA98;margin-bottom:24px;">Looking for something bespoke? Commission a custom piece crafted to your exact specifications.</p>
          <a href="/custom-request.php" class="btn btn-primary">Custom Furniture Enquiry</a>
        </div>
      <?php else: ?>
        <div class="catalogue-grid">
          <?php foreach ($products as $p): 
            $waMsg = "Hello Grand Living Vizag! I would like to enquire about the {$p['name']} (SKU: {$p['sku']}).";
            $waLink = "https://wa.me/{$whatsapp_default}?text=" . urlencode($waMsg);
          ?>
            <article class="catalogue-card">
              <a href="/product.php?slug=<?= urlencode($p['slug']) ?>" style="display:block;position:relative;aspect-ratio:4/3;background:linear-gradient(145deg, #29221D, #171614);overflow:hidden;">
                <?php if (!empty($p['primary_image'])): ?>
                  <img src="<?= htmlspecialchars($p['primary_image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="catalogue-card__img" loading="lazy">
                <?php else: ?>
                  <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, #29221D 0%, #76543B 50%, #171614 100%);">
                    <span style="font-family:'Cormorant Garamond',serif;font-size:2rem;color:rgba(213,190,154,0.4);font-style:italic;">Grand Living</span>
                  </div>
                <?php endif; ?>
                
                <?php if ($p['is_featured']): ?>
                  <span style="position:absolute;top:16px;left:16px;background:#D5BE9A;color:#171614;padding:4px 10px;font-size:0.65rem;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;border-radius:2px;">Featured</span>
                <?php endif; ?>
                <span style="position:absolute;bottom:16px;right:16px;background:rgba(23,22,20,0.88);color:#D5BE9A;padding:4px 10px;font-size:0.65rem;letter-spacing:0.1em;text-transform:uppercase;border:1px solid rgba(213,190,154,0.3);border-radius:2px;backdrop-filter:blur(4px);">
                  <?= $p['availability'] === 'in-stock' ? 'In Stock' : 'Made to Order' ?>
                </span>
              </a>

              <div style="padding:26px;display:flex;flex-direction:column;flex-grow:1;">
                <span style="font-size:0.72rem;letter-spacing:0.18em;text-transform:uppercase;color:#D5BE9A;margin-bottom:8px;font-weight:700;">
                  <?= htmlspecialchars($p['category_name']) ?>
                </span>
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.65rem;color:#F5F0E8;margin-bottom:8px;line-height:1.2;font-weight:400;">
                  <a href="/product.php?slug=<?= urlencode($p['slug']) ?>" style="color:#F5F0E8;text-decoration:none;transition:color 0.3s ease;">
                    <?= htmlspecialchars($p['name']) ?>
                  </a>
                </h3>
                <p style="font-size:0.86rem;color:#B7AA98;line-height:1.65;margin-bottom:18px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                  <?= htmlspecialchars($p['short_description']) ?>
                </p>

                  <div style="margin-top:auto;padding-top:18px;border-top:1px solid rgba(213,190,154,0.12);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div>
                      <span style="display:block;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.15em;color:rgba(183,170,152,0.65);font-weight:600;">Pricing</span>
                      <span style="font-family:'Cormorant Garamond',serif;font-size:1.15rem;color:#D5BE9A;font-style:italic;letter-spacing:0.02em;">Price on Request</span>
                    </div>
                    <div style="display:flex;gap:8px;">
                      <a href="<?= $waLink ?>" target="_blank" rel="noopener" style="padding:9px 14px;background:#25D366;color:#fff;border-radius:2px;font-size:0.74rem;font-weight:700;display:inline-flex;align-items:center;gap:6px;text-decoration:none;transition:all 0.3s ease;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.374 0 0 5.373 0 12c0 2.113.554 4.121 1.523 5.868L.057 23.887l6.187-1.44A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.822 9.822 0 01-5.009-1.368l-.36-.214-3.724.868.9-3.63-.234-.373A9.806 9.806 0 012.182 12C2.182 6.564 6.563 2.182 12 2.182S21.818 6.564 21.818 12 17.437 21.818 12 21.818z"/>
                        </svg>
                        Enquire
                      </a>
                      <a href="/product.php?slug=<?= urlencode($p['slug']) ?>" class="btn btn-ghost-champ" style="padding:9px 14px;font-size:0.74rem;">
                        View Piece &rarr;
                      </a>
                    </div>
                  </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
          <nav style="display:flex;justify-content:center;gap:8px;margin-top:60px;">
            <?php foreach ($pagination['pages'] as $pNum): 
              $q = $_GET;
              $q['page'] = $pNum;
              $pageUrl = '/catalogue.php?' . http_build_query($q);
            ?>
              <a href="<?= htmlspecialchars($pageUrl) ?>" 
                 style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(213,190,154,0.3);color:<?= $pNum === $page ? '#171614' : '#F5F0E8' ?>;background:<?= $pNum === $page ? '#D5BE9A' : 'transparent' ?>;text-decoration:none;font-weight:600;font-size:0.85rem;">
                <?= $pNum ?>
              </a>
            <?php endforeach; ?>
          </nav>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
