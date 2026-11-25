<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/frontend-functions.php';

$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';
if (empty($slug)) {
    redirect('/catalogue.php');
}

$product = getProduct($slug, $pdo);
if (!$product) {
    http_response_code(404);
    $page_title = "Piece Not Found — Grand Furniture Vizag";
    include 'includes/header.php';
    echo '<div style="padding:180px 20px 100px;text-align:center;background:#171614;min-height:80vh;">
            <h1 style="font-family:\'Cormorant Garamond\',serif;font-size:3rem;color:#F5F0E8;">Piece Not Found</h1>
            <p style="color:#B7AA98;margin:20px 0 30px;">The requested furniture item may have been moved or archived.</p>
            <a href="/catalogue.php" class="btn btn-primary">Return to Collection</a>
          </div>';
    include 'includes/footer.php';
    exit;
}

$relatedProducts = getRelatedProducts($product['id'], $product['category_id'], 4, $pdo);
$whatsapp_default = getSetting('whatsapp_number', $pdo) ?: '919052751666';
$waMsg = "Hello Grand Furniture Vizag! I would like to inquire about the {$product['name']} (SKU: {$product['sku']}).";
$waLink = "https://wa.me/{$whatsapp_default}?text=" . urlencode($waMsg);

$page_title = $product['meta_title'] ?: "{$product['name']} — Grand Furniture Vizag";
$page_description = $product['meta_description'] ?: $product['short_description'];

include 'includes/header.php';
?>

<div style="padding-top:120px;background:#171614;min-height:100vh;color:#F5F0E8;">
  <!-- Breadcrumb -->
  <div style="background:#1E1A17;padding:16px 0;border-bottom:1px solid rgba(213,190,154,0.1);">
    <div class="container" style="display:flex;gap:8px;font-size:0.75rem;letter-spacing:0.1em;text-transform:uppercase;color:#B7AA98;">
      <a href="/" style="color:#B7AA98;text-decoration:none;">Home</a>
      <span>/</span>
      <a href="/catalogue.php" style="color:#B7AA98;text-decoration:none;">Catalogue</a>
      <span>/</span>
      <a href="/catalogue.php?category=<?= urlencode($product['category_slug']) ?>" style="color:#B7AA98;text-decoration:none;"><?= htmlspecialchars($product['category_name']) ?></a>
      <span>/</span>
      <span style="color:#D5BE9A;"><?= htmlspecialchars($product['name']) ?></span>
    </div>
  </div>

  <!-- Product Main Section -->
  <section style="padding:60px 0 100px;">
    <div class="container">
      <div class="product-main-grid" style="display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,1fr);gap:60px;align-items:start;">
        
        <!-- Gallery Left -->
        <div>
          <div id="mainImageContainer" style="aspect-ratio:4/3;background:linear-gradient(145deg, #29221D, #171614);border:1px solid rgba(213,190,154,0.2);border-radius:2px;overflow:hidden;position:relative;">
            <?php if (!empty($product['images'])): ?>
              <img id="mainImage" src="<?= htmlspecialchars($product['images'][0]['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:100%;height:100%;object-fit:cover;">
            <?php else: ?>
              <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, #29221D 0%, #76543B 50%, #171614 100%);">
                <span style="font-family:'Cormorant Garamond',serif;font-size:2.5rem;color:rgba(213,190,154,0.4);font-style:italic;">Grand Furniture Vizag</span>
              </div>
            <?php endif; ?>
          </div>

          <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
            <div style="display:flex;gap:12px;margin-top:16px;">
              <?php foreach ($product['images'] as $img): ?>
                <button type="button" onclick="document.getElementById('mainImage').src = '<?= htmlspecialchars($img['image_path']) ?>'"
                        style="width:80px;height:60px;border:1px solid rgba(213,190,154,0.3);background:#1E1A17;overflow:hidden;cursor:pointer;padding:0;">
                  <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="thumbnail" style="width:100%;height:100%;object-fit:cover;">
                </button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Product Details Right -->
        <div>
          <span style="font-size:0.75rem;letter-spacing:0.25em;text-transform:uppercase;color:#D5BE9A;display:block;margin-bottom:8px;font-weight:700;">
            <?= htmlspecialchars($product['category_name']) ?> &bull; SKU: <?= htmlspecialchars($product['sku'] ?: 'GF-BESPOKE') ?>
          </span>
          <h1 style="font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem, 4vw, 3.5rem);font-weight:300;line-height:1.15;color:#F5F0E8;margin-bottom:16px;">
            <?= htmlspecialchars($product['name']) ?>
          </h1>

          <div style="margin-bottom:24px;padding:16px 20px;background:#1E1A17;border-left:3px solid #D5BE9A;">
            <span style="display:block;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.15em;color:#B7AA98;">Pricing</span>
            <span style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;color:#D5BE9A;font-style:italic;">Price on Request</span>
            <span style="display:block;font-size:0.75rem;color:rgba(183,170,152,0.7);margin-top:4px;">Tailored to your bespoke dimensions, wood selection, and fabric finishes. Direct consultation on WhatsApp.</span>
          </div>

          <div style="font-size:0.95rem;line-height:1.8;color:#B7AA98;margin-bottom:32px;">
            <p><?= nl2br(htmlspecialchars($product['description'] ?: $product['short_description'])) ?></p>
          </div>

          <!-- Specifications Table -->
          <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.15);padding:24px;margin-bottom:32px;border-radius:2px;">
            <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:#D5BE9A;margin-bottom:16px;border-bottom:1px solid rgba(213,190,154,0.15);padding-bottom:8px;">Artisanal Specifications</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;font-size:0.85rem;">
              <?php if (!empty($product['dimensions'])): ?>
                <div>
                  <span style="color:#D5BE9A;font-weight:600;display:block;">Dimensions</span>
                  <span style="color:#F5F0E8;"><?= htmlspecialchars($product['dimensions']) ?></span>
                </div>
              <?php endif; ?>
              <?php if (!empty($product['materials'])): ?>
                <div>
                  <span style="color:#D5BE9A;font-weight:600;display:block;">Materials</span>
                  <span style="color:#F5F0E8;"><?= htmlspecialchars($product['materials']) ?></span>
                </div>
              <?php endif; ?>
              <?php if (!empty($product['colors'])): ?>
                <div>
                  <span style="color:#D5BE9A;font-weight:600;display:block;">Available Palettes</span>
                  <span style="color:#F5F0E8;"><?= htmlspecialchars($product['colors']) ?></span>
                </div>
              <?php endif; ?>
              <?php if (!empty($product['finishes'])): ?>
                <div>
                  <span style="color:#D5BE9A;font-weight:600;display:block;">Finishes</span>
                  <span style="color:#F5F0E8;"><?= htmlspecialchars($product['finishes']) ?></span>
                </div>
              <?php endif; ?>
              <div>
                <span style="color:#D5BE9A;font-weight:600;display:block;">Status</span>
                <span style="color:#F5F0E8;"><?= $product['availability'] === 'in-stock' ? 'Showroom Ready' : 'Handcrafted Made-to-Order' ?></span>
              </div>
            </div>
          </div>

                    <!-- CTAs -->
          <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:40px;">
            <a href="<?= $waLink ?>" target="_blank" rel="noopener" 
               style="flex:1.5;min-width:220px;padding:16px 28px;background:#25D366;color:#fff;text-align:center;font-weight:700;font-size:0.9rem;letter-spacing:0.08em;text-transform:uppercase;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:10px;border-radius:2px;box-shadow:0 6px 20px rgba(37,211,102,0.25);">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.374 0 0 5.373 0 12c0 2.113.554 4.121 1.523 5.868L.057 23.887l6.187-1.44A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.822 9.822 0 01-5.009-1.368l-.36-.214-3.724.868.9-3.63-.234-.373A9.806 9.806 0 012.182 12C2.182 6.564 6.563 2.182 12 2.182S21.818 6.564 21.818 12 17.437 21.818 12 21.818z"/></svg>
              Enquire on WhatsApp
            </a>
            <a href="/custom-request.php?product=<?= urlencode($product['slug']) ?>" 
               class="btn btn-primary" style="flex:1;min-width:200px;text-align:center;">
              Request Custom Dimensions
            </a>
          </div>

          <!-- Quick Enquiry Direct Form -->
          <div style="background:#1E1A17;padding:24px;border:1px solid rgba(213,190,154,0.15);border-radius:2px;">
            <h4 style="font-family:'Cormorant Garamond',serif;font-size:1.3rem;color:#F5F0E8;margin-bottom:12px;">Quick Showroom Enquiry</h4>
            <form action="/process-enquiry.php" method="POST" style="display:flex;flex-direction:column;gap:12px;">
              <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
              <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
              <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
              <input type="hidden" name="subject" value="Enquiry for <?= htmlspecialchars($product['name']) ?>">
              
              <div class="product-form-row" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;">
                <input type="text" name="name" required placeholder="Your Name" style="background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px 14px;font-size:0.85rem;">
                <input type="tel" name="phone" required placeholder="Phone Number" style="background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px 14px;font-size:0.85rem;">
              </div>
              <input type="email" name="email" placeholder="Email Address (Optional)" style="background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px 14px;font-size:0.85rem;">
              <textarea name="message" rows="3" placeholder="Any questions regarding dimensions, fabrics, or delivery timeline?" style="background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px 14px;font-size:0.85rem;resize:vertical;"></textarea>
              <button type="submit" class="btn btn-champagne" style="padding:12px;font-size:0.8rem;">Send Showroom Request</button>
            </form>
            <script>
              (function() {
                var qForm = document.currentScript ? document.currentScript.previousElementSibling : null;
                if (!qForm || qForm.tagName !== "FORM") qForm = document.querySelector("form[action*=\"process-enquiry\"]");
                if (!qForm) return;
                qForm.addEventListener("submit", function(e) {
                  var name = (qForm.elements["name"] && qForm.elements["name"].value) || "";
                  var phone = (qForm.elements["phone"] && qForm.elements["phone"].value) || "";
                  if (!name || !phone) return;
                  e.preventDefault();
                  var email = (qForm.elements["email"] && qForm.elements["email"].value) || "";
                  var msg = (qForm.elements["message"] && qForm.elements["message"].value) || "";
                  var prod = (qForm.elements["product_name"] && qForm.elements["product_name"].value) || (document.querySelector("h1") ? document.querySelector("h1").textContent : "");
                  var text = encodeURIComponent("🏛️ *Grand Living Vizag — Quick Showroom Enquiry*\n\n" +
                    "• *Piece:* " + prod.trim() + "\n" +
                    "• *Client Name:* " + name.trim() + "\n" +
                    "• *Phone:* " + phone.trim() + "\n" +
                    (email ? "• *Email:* " + email.trim() + "\n" : "") +
                    (msg ? "• *Questions / Notes:* " + msg.trim() + "\n" : ""));
                  window.open("https://wa.me/919052751666?text=" + text, "_blank");
                });
              })();
            </script>
          </div>

        </div>
      </div>
    </div>
  </section>

  <!-- Related Products -->
  <?php if (!empty($relatedProducts)): ?>
    <section style="padding:60px 0 100px;background:#1E1A17;border-top:1px solid rgba(213,190,154,0.15);">
      <div class="container">
        <div style="text-align:center;margin-bottom:40px;">
          <span style="font-size:0.75rem;letter-spacing:0.25em;text-transform:uppercase;color:#D5BE9A;display:block;margin-bottom:8px;font-weight:700;">Harmonious Complements</span>
          <h2 style="font-family:'Cormorant Garamond',serif;font-size:2.4rem;color:#F5F0E8;font-weight:300;">You May Also Admire</h2>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:24px;">
          <?php foreach ($relatedProducts as $rel): ?>
            <div style="background:#171614;border:1px solid rgba(213,190,154,0.1);padding:16px;">
              <a href="/product.php?slug=<?= urlencode($rel['slug']) ?>" style="text-decoration:none;">
                <h4 style="font-family:'Cormorant Garamond',serif;font-size:1.3rem;color:#F5F0E8;margin-bottom:8px;"><?= htmlspecialchars($rel['name']) ?></h4>
                <p style="font-size:0.8rem;color:#B7AA98;"><?= htmlspecialchars($rel['short_description']) ?></p>
                <span style="color:#D5BE9A;font-size:0.75rem;font-weight:700;display:inline-block;margin-top:12px;">Discover &rarr;</span>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
