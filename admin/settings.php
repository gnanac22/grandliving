<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$pageTitle = 'Showroom & Website Settings';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (validateCSRFToken($token)) {
        $fields = [
            'site_name', 'tagline', 'phone_primary', 'phone_secondary',
            'email', 'address', 'whatsapp_number', 'business_hours', 'sunday_hours',
            'maps_link', 'google_maps_embed', 'creator_credit',
            'facebook_url', 'instagram_url', 'youtube_url',
            'meta_title', 'meta_description', 'meta_keywords'
        ];

        foreach ($fields as $f) {
            if (isset($_POST[$f])) {
                $val = trim($_POST[$f]);
                $stmt = $pdo->prepare('INSERT INTO website_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?');
                $stmt->execute([$f, $val, $val]);
            }
        }

        logActivity($pdo, 'Update Settings', 'Updated website showroom settings & credentials');
        setFlash('success', 'Showroom settings have been updated.');
        redirect(ADMIN_URL . '/settings.php');
    }
}

$settings = getSettings($pdo);

include __DIR__ . '/includes/admin-header.php';
?>

<div style="max-width:880px;background:#1E1A17;border:1px solid rgba(213,190,154,0.18);padding:36px;border-radius:3px;margin-bottom:40px;">
  <div style="margin-bottom:28px;border-bottom:1px solid rgba(213,190,154,0.15);padding-bottom:16px;">
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:2rem;color:#F5F0E8;font-weight:400;margin:0 0 6px;">Website &amp; Showroom Details</h2>
    <span style="font-size:0.82rem;color:#B7AA98;">Control contact details, maps location, operating hours, partner attribution, and SEO</span>
  </div>

  <form method="POST" action="" style="display:flex;flex-direction:column;gap:24px;">
    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

    <!-- Brand Identity -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Brand Name</label>
        <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? 'Grand Furniture Vizag') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Brand Tagline</label>
        <input type="text" name="tagline" value="<?= htmlspecialchars($settings['tagline'] ?? 'The Art of Living Beautifully') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
    </div>

    <!-- Communication Lines -->
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Primary Phone</label>
        <input type="text" name="phone_primary" value="<?= htmlspecialchars($settings['phone_primary'] ?? '09052751666') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Secondary Phone</label>
        <input type="text" name="phone_secondary" value="<?= htmlspecialchars($settings['phone_secondary'] ?? '') ?>" placeholder="Optional phone" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">WhatsApp (with Country Code)</label>
        <input type="text" name="whatsapp_number" value="<?= htmlspecialchars($settings['whatsapp_number'] ?? '919052751666') ?>" placeholder="919052751666" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
    </div>

    <div>
      <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Showroom Email Address</label>
      <input type="email" name="email" value="<?= htmlspecialchars($settings['email'] ?? 'hello@grandfurniturevizag.com') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
    </div>

    <!-- Address & Maps -->
    <div>
      <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Showroom Physical Address</label>
      <textarea name="address" rows="2" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;resize:vertical;"><?= htmlspecialchars($settings['address'] ?? 'Grand Living, Dwaraka Nagar Main Road, Visakhapatnam, Andhra Pradesh – 530 016') ?></textarea>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Direct Google Maps Share Link</label>
        <input type="url" name="maps_link" value="<?= htmlspecialchars($settings['maps_link'] ?? 'https://maps.app.goo.gl/4H7RJMT3GGWLZR7R7') ?>" placeholder="https://maps.app.goo.gl/..." style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.85rem;">
      </div>
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Google Maps Embed Iframe URL (Optional)</label>
        <input type="text" name="google_maps_embed" value="<?= htmlspecialchars($settings['google_maps_embed'] ?? '') ?>" placeholder="https://www.google.com/maps/embed?..." style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.85rem;">
      </div>
    </div>

    <!-- Operating Hours -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Weekday Hours (Mon – Sat)</label>
        <input type="text" name="business_hours" value="<?= htmlspecialchars($settings['business_hours'] ?? 'Mon–Sat: 10:00 AM – 8:00 PM') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Sunday Hours</label>
        <input type="text" name="sunday_hours" value="<?= htmlspecialchars($settings['sunday_hours'] ?? 'Sunday: 11:00 AM – 6:00 PM') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.95rem;">
      </div>
    </div>

    <!-- Partner Attribution Credit -->
    <div>
      <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Experience Design Partner Credit</label>
      <input type="text" name="creator_credit" value="<?= htmlspecialchars($settings['creator_credit'] ?? 'Done by Gnanachandra • Gnanacreats') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#D5BE9A;padding:12px;font-size:0.95rem;font-weight:600;">
    </div>

    <!-- Social Channels -->
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Instagram URL</label>
        <input type="text" name="instagram_url" value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/..." style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.85rem;">
      </div>
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Facebook URL</label>
        <input type="text" name="facebook_url" value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>" placeholder="https://facebook.com/..." style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.85rem;">
      </div>
      <div>
        <label style="display:block;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">YouTube URL</label>
        <input type="text" name="youtube_url" value="<?= htmlspecialchars($settings['youtube_url'] ?? '') ?>" placeholder="https://youtube.com/..." style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px;font-size:0.85rem;">
      </div>
    </div>

    <!-- SEO Meta Tags -->
    <div style="background:#171614;border:1px solid rgba(213,190,154,0.2);padding:20px;border-radius:2px;">
      <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.3rem;color:#D5BE9A;margin:0 0 16px;">Search Engine Optimization (SEO)</h3>
      <div style="display:flex;flex-direction:column;gap:14px;">
        <div>
          <label style="display:block;font-size:0.7rem;text-transform:uppercase;color:#D5BE9A;margin-bottom:4px;">Default Meta Title</label>
          <input type="text" name="meta_title" value="<?= htmlspecialchars($settings['meta_title'] ?? 'Grand Furniture Vizag – The Art of Living Beautifully') ?>" style="width:100%;box-sizing:border-box;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.88rem;">
        </div>
        <div>
          <label style="display:block;font-size:0.7rem;text-transform:uppercase;color:#D5BE9A;margin-bottom:4px;">Default Meta Description</label>
          <textarea name="meta_description" rows="2" style="width:100%;box-sizing:border-box;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.88rem;resize:vertical;"><?= htmlspecialchars($settings['meta_description'] ?? 'Grand Furniture Vizag offers luxury, handcrafted furniture for every room. Visit our showroom in Visakhapatnam and discover pieces that transform your living spaces.') ?></textarea>
        </div>
        <div>
          <label style="display:block;font-size:0.7rem;text-transform:uppercase;color:#D5BE9A;margin-bottom:4px;">Meta Keywords</label>
          <input type="text" name="meta_keywords" value="<?= htmlspecialchars($settings['meta_keywords'] ?? 'luxury furniture vizag, handcrafted furniture visakhapatnam, teak wood sofas, custom interiors vizag, grand living dwaraka nagar') ?>" style="width:100%;box-sizing:border-box;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.88rem;">
        </div>
      </div>
    </div>

    <div>
      <button type="submit" style="padding:14px 36px;background:#D5BE9A;color:#171614;border:none;font-size:0.85rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;cursor:pointer;border-radius:2px;">
        Save Showroom Settings &rarr;
      </button>
    </div>
  </form>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
