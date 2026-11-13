<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$pageTitle = 'Homepage & Brand Content CMS';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (validateCSRFToken($token)) {
        $textFields = [
            // Hero
            'hero_eyebrow', 'hero_headline_1', 'hero_headline_2', 'hero_subheadline',
            'hero_btn1_text', 'hero_btn1_link', 'hero_btn2_text', 'hero_btn2_link',
            // Brand Intro / Philosophy
            'intro_eyebrow', 'intro_title', 'intro_lead', 'intro_body',
            // 30 Years Legacy
            'legacy_years', 'legacy_title', 'legacy_tagline',
            'legacy_hallmark_1', 'legacy_hallmark_2', 'legacy_hallmark_3',
            // Core Values
            'values_label', 'values_title', 'values_sub',
            'value_1_title', 'value_1_desc',
            'value_2_title', 'value_2_desc',
            'value_3_title', 'value_3_desc',
            'value_4_title', 'value_4_desc',
            // Stats
            'stat_1_number', 'stat_1_suffix', 'stat_1_label',
            'stat_2_number', 'stat_2_suffix', 'stat_2_label',
            'stat_3_number', 'stat_3_suffix', 'stat_3_label',
            'stat_4_number', 'stat_4_suffix', 'stat_4_label',
            // Final CTA
            'cta_title', 'cta_sub', 'cta_btn1_text', 'cta_btn2_text'
        ];

        foreach ($textFields as $field) {
            if (isset($_POST[$field])) {
                $val = trim($_POST[$field]);
                $stmt = $pdo->prepare('INSERT INTO website_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?');
                $stmt->execute([$field, $val, $val]);
            }
        }

        logActivity($pdo, 'Update Content CMS', 'Updated homepage & brand CMS content');
        setFlash('success', 'Website content & brand copy updated successfully.');
        redirect(ADMIN_URL . '/content.php');
    }
}

$settings = getSettings($pdo);

include __DIR__ . '/includes/admin-header.php';
?>

<div style="max-width:1000px;margin-bottom:40px;">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;">
    <div>
      <h2 style="font-family:'Cormorant Garamond',serif;font-size:2rem;color:#F5F0E8;font-weight:400;">Brand &amp; Website Content CMS</h2>
      <span style="font-size:0.82rem;color:#B7AA98;">Live copy management for Hero, Core Values, 30-Year Legacy, and Brand Stats</span>
    </div>
    <a href="/" target="_blank" style="padding:8px 16px;background:rgba(213,190,154,0.15);color:#D5BE9A;border:1px solid rgba(213,190,154,0.3);text-decoration:none;font-size:0.75rem;font-weight:700;border-radius:2px;">
      View Live Website &rarr;
    </a>
  </div>

  <form method="POST" action="" style="display:flex;flex-direction:column;gap:32px;">
    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

    <!-- SECTION 1: HERO -->
    <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.2);padding:28px;border-radius:3px;">
      <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:#D5BE9A;margin-bottom:20px;border-bottom:1px solid rgba(213,190,154,0.15);padding-bottom:10px;">
        1. Cinematic Hero Section
      </h3>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:16px;">
        <div>
          <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Hero Eyebrow</label>
          <input type="text" name="hero_eyebrow" value="<?= htmlspecialchars($settings['hero_eyebrow'] ?? 'HAUTE-COUTURE FURNITURE • VISAKHAPATNAM') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.88rem;">
        </div>
        <div>
          <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Headline (Line 1)</label>
          <input type="text" name="hero_headline_1" value="<?= htmlspecialchars($settings['hero_headline_1'] ?? 'THE ART OF') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.88rem;">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:16px;">
        <div>
          <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Headline (Line 2 / Highlight)</label>
          <input type="text" name="hero_headline_2" value="<?= htmlspecialchars($settings['hero_headline_2'] ?? 'LIVING BEAUTIFULLY.') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.88rem;">
        </div>
        <div>
          <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Supporting Subheading</label>
          <input type="text" name="hero_subheadline" value="<?= htmlspecialchars($settings['hero_subheadline'] ?? 'Discover furniture that transforms everyday spaces into extraordinary experiences.') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.88rem;">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px;">
        <div>
          <label style="display:block;font-size:0.7rem;text-transform:uppercase;color:#D5BE9A;margin-bottom:4px;">Button 1 Label</label>
          <input type="text" name="hero_btn1_text" value="<?= htmlspecialchars($settings['hero_btn1_text'] ?? 'Explore Collection') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:8px;font-size:0.85rem;">
        </div>
        <div>
          <label style="display:block;font-size:0.7rem;text-transform:uppercase;color:#D5BE9A;margin-bottom:4px;">Button 1 Link</label>
          <input type="text" name="hero_btn1_link" value="<?= htmlspecialchars($settings['hero_btn1_link'] ?? '/catalogue.php') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:8px;font-size:0.85rem;">
        </div>
        <div>
          <label style="display:block;font-size:0.7rem;text-transform:uppercase;color:#D5BE9A;margin-bottom:4px;">Button 2 Label</label>
          <input type="text" name="hero_btn2_text" value="<?= htmlspecialchars($settings['hero_btn2_text'] ?? 'Our Story') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:8px;font-size:0.85rem;">
        </div>
        <div>
          <label style="display:block;font-size:0.7rem;text-transform:uppercase;color:#D5BE9A;margin-bottom:4px;">Button 2 Link</label>
          <input type="text" name="hero_btn2_link" value="<?= htmlspecialchars($settings['hero_btn2_link'] ?? '/about.php') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:8px;font-size:0.85rem;">
        </div>
      </div>
    </div>

    <!-- SECTION 2: 30 YEARS OF LEGACY -->
    <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.2);padding:28px;border-radius:3px;">
      <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:#D5BE9A;margin-bottom:20px;border-bottom:1px solid rgba(213,190,154,0.15);padding-bottom:10px;">
        2. Signature Legacy &amp; Hallmarks Centerpiece
      </h3>

      <div style="display:grid;grid-template-columns:140px 1fr 1fr;gap:20px;margin-bottom:16px;">
        <div>
          <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Legacy Years</label>
          <input type="text" name="legacy_years" value="<?= htmlspecialchars($settings['legacy_years'] ?? '30') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#D5BE9A;padding:10px;font-size:1.1rem;font-weight:700;">
        </div>
        <div>
          <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Legacy Heading</label>
          <input type="text" name="legacy_title" value="<?= htmlspecialchars($settings['legacy_title'] ?? 'Years of Master Artistry') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.9rem;">
        </div>
        <div>
          <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Legacy Tagline</label>
          <input type="text" name="legacy_tagline" value="<?= htmlspecialchars($settings['legacy_tagline'] ?? 'Three Decades of Curating Extraordinary Living Spaces & Master Artistry') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.9rem;">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
        <div>
          <label style="display:block;font-size:0.7rem;text-transform:uppercase;color:#D5BE9A;margin-bottom:4px;">Hallmark 1</label>
          <input type="text" name="legacy_hallmark_1" value="<?= htmlspecialchars($settings['legacy_hallmark_1'] ?? '100% Solid Teak') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:8px;font-size:0.85rem;">
        </div>
        <div>
          <label style="display:block;font-size:0.7rem;text-transform:uppercase;color:#D5BE9A;margin-bottom:4px;">Hallmark 2</label>
          <input type="text" name="legacy_hallmark_2" value="<?= htmlspecialchars($settings['legacy_hallmark_2'] ?? 'Master Artisans') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:8px;font-size:0.85rem;">
        </div>
        <div>
          <label style="display:block;font-size:0.7rem;text-transform:uppercase;color:#D5BE9A;margin-bottom:4px;">Hallmark 3</label>
          <input type="text" name="legacy_hallmark_3" value="<?= htmlspecialchars($settings['legacy_hallmark_3'] ?? 'Handcrafted in AP') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:8px;font-size:0.85rem;">
        </div>
      </div>
    </div>

    <!-- SECTION 3: OUR CORE VALUES (THE 4 PILLARS) -->
    <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.2);padding:28px;border-radius:3px;">
      <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:#D5BE9A;margin-bottom:20px;border-bottom:1px solid rgba(213,190,154,0.15);padding-bottom:10px;">
        3. Our Core Values (The 4 Pillars)
      </h3>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
        <div>
          <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Section Label</label>
          <input type="text" name="values_label" value="<?= htmlspecialchars($settings['values_label'] ?? 'What We Stand For') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.9rem;">
        </div>
        <div>
          <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Section Title</label>
          <input type="text" name="values_title" value="<?= htmlspecialchars($settings['values_title'] ?? 'Our Core Values') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.9rem;">
        </div>
      </div>

      <div style="margin-bottom:24px;">
        <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Section Subtitle / Description</label>
        <input type="text" name="values_sub" value="<?= htmlspecialchars($settings['values_sub'] ?? 'Four foundational pillars guiding three decades of master woodworking and architectural living.') ?>" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px;font-size:0.9rem;">
      </div>

      <!-- Pillar 1 & 2 -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:20px;">
        <div style="background:#171614;border:1px solid rgba(213,190,154,0.25);border-top:2px solid #D5BE9A;padding:20px;border-radius:2px;">
          <span style="font-size:0.7rem;color:#D5BE9A;font-weight:700;">PILLAR 01</span>
          <input type="text" name="value_1_title" value="<?= htmlspecialchars($settings['value_1_title'] ?? 'Quality') ?>" style="width:100%;margin:8px 0;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:8px;font-size:1.1rem;font-family:'Cormorant Garamond',serif;">
          <textarea name="value_1_desc" rows="3" style="width:100%;box-sizing:border-box;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#D2C8BC;padding:8px;font-size:0.85rem;line-height:1.6;"><?= htmlspecialchars($settings['value_1_desc'] ?? 'We refuse to compromise on materials, finish, or detail. Every product leaves our workshop meeting the highest standards.') ?></textarea>
        </div>

        <div style="background:#171614;border:1px solid rgba(213,190,154,0.25);border-top:2px solid #D5BE9A;padding:20px;border-radius:2px;">
          <span style="font-size:0.7rem;color:#D5BE9A;font-weight:700;">PILLAR 02</span>
          <input type="text" name="value_2_title" value="<?= htmlspecialchars($settings['value_2_title'] ?? 'Craftsmanship') ?>" style="width:100%;margin:8px 0;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:8px;font-size:1.1rem;font-family:'Cormorant Garamond',serif;">
          <textarea name="value_2_desc" rows="3" style="width:100%;box-sizing:border-box;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#D2C8BC;padding:8px;font-size:0.85rem;line-height:1.6;"><?= htmlspecialchars($settings['value_2_desc'] ?? 'Our artisans bring generations of woodworking knowledge to every piece. No two items are ever truly identical.') ?></textarea>
        </div>
      </div>

      <!-- Pillar 3 & 4 -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
        <div style="background:#171614;border:1px solid rgba(213,190,154,0.25);border-top:2px solid #D5BE9A;padding:20px;border-radius:2px;">
          <span style="font-size:0.7rem;color:#D5BE9A;font-weight:700;">PILLAR 03</span>
          <input type="text" name="value_3_title" value="<?= htmlspecialchars($settings['value_3_title'] ?? 'Integrity') ?>" style="width:100%;margin:8px 0;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:8px;font-size:1.1rem;font-family:'Cormorant Garamond',serif;">
          <textarea name="value_3_desc" rows="3" style="width:100%;box-sizing:border-box;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#D2C8BC;padding:8px;font-size:0.85rem;line-height:1.6;"><?= htmlspecialchars($settings['value_3_desc'] ?? 'We price fairly, deliver on time, and stand behind everything we sell. Your trust is our most valuable asset.') ?></textarea>
        </div>

        <div style="background:#171614;border:1px solid rgba(213,190,154,0.25);border-top:2px solid #D5BE9A;padding:20px;border-radius:2px;">
          <span style="font-size:0.7rem;color:#D5BE9A;font-weight:700;">PILLAR 04</span>
          <input type="text" name="value_4_title" value="<?= htmlspecialchars($settings['value_4_title'] ?? 'Service') ?>" style="width:100%;margin:8px 0;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:8px;font-size:1.1rem;font-family:'Cormorant Garamond',serif;">
          <textarea name="value_4_desc" rows="3" style="width:100%;box-sizing:border-box;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#D2C8BC;padding:8px;font-size:0.85rem;line-height:1.6;"><?= htmlspecialchars($settings['value_4_desc'] ?? 'From your first visit to our showroom through years of after-sales care, we are committed to your satisfaction.') ?></textarea>
        </div>
      </div>
    </div>

    <!-- SECTION 4: BRAND STATS COUNTERS -->
    <div style="background:#1E1A17;border:1px solid rgba(213,190,154,0.2);padding:28px;border-radius:3px;">
      <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:#D5BE9A;margin-bottom:20px;border-bottom:1px solid rgba(213,190,154,0.15);padding-bottom:10px;">
        4. Brand Statistics Counters
      </h3>

      <div style="display:grid;grid-template-columns:repeat(4, 1fr);gap:16px;">
        <div style="background:#171614;padding:14px;border:1px solid rgba(213,190,154,0.2);border-radius:2px;">
          <label style="font-size:0.68rem;color:#D5BE9A;text-transform:uppercase;">Stat 1</label>
          <div style="display:flex;gap:6px;margin:6px 0;">
            <input type="number" name="stat_1_number" value="<?= htmlspecialchars($settings['stat_1_number'] ?? 1000) ?>" style="width:70%;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:6px;font-size:0.9rem;">
            <input type="text" name="stat_1_suffix" value="<?= htmlspecialchars($settings['stat_1_suffix'] ?? '+') ?>" style="width:30%;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#D5BE9A;padding:6px;font-size:0.9rem;">
          </div>
          <input type="text" name="stat_1_label" value="<?= htmlspecialchars($settings['stat_1_label'] ?? 'Happy Families') ?>" style="width:100%;box-sizing:border-box;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#D2C8BC;padding:6px;font-size:0.78rem;">
        </div>

        <div style="background:#171614;padding:14px;border:1px solid rgba(213,190,154,0.2);border-radius:2px;">
          <label style="font-size:0.68rem;color:#D5BE9A;text-transform:uppercase;">Stat 2</label>
          <div style="display:flex;gap:6px;margin:6px 0;">
            <input type="number" name="stat_2_number" value="<?= htmlspecialchars($settings['stat_2_number'] ?? 500) ?>" style="width:70%;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:6px;font-size:0.9rem;">
            <input type="text" name="stat_2_suffix" value="<?= htmlspecialchars($settings['stat_2_suffix'] ?? '+') ?>" style="width:30%;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#D5BE9A;padding:6px;font-size:0.9rem;">
          </div>
          <input type="text" name="stat_2_label" value="<?= htmlspecialchars($settings['stat_2_label'] ?? 'Products') ?>" style="width:100%;box-sizing:border-box;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#D2C8BC;padding:6px;font-size:0.78rem;">
        </div>

        <div style="background:#171614;padding:14px;border:1px solid rgba(213,190,154,0.2);border-radius:2px;">
          <label style="font-size:0.68rem;color:#D5BE9A;text-transform:uppercase;">Stat 3</label>
          <div style="display:flex;gap:6px;margin:6px 0;">
            <input type="number" name="stat_3_number" value="<?= htmlspecialchars($settings['stat_3_number'] ?? 30) ?>" style="width:70%;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:6px;font-size:0.9rem;">
            <input type="text" name="stat_3_suffix" value="<?= htmlspecialchars($settings['stat_3_suffix'] ?? '+') ?>" style="width:30%;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#D5BE9A;padding:6px;font-size:0.9rem;">
          </div>
          <input type="text" name="stat_3_label" value="<?= htmlspecialchars($settings['stat_3_label'] ?? 'Years Experience') ?>" style="width:100%;box-sizing:border-box;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#D2C8BC;padding:6px;font-size:0.78rem;">
        </div>

        <div style="background:#171614;padding:14px;border:1px solid rgba(213,190,154,0.2);border-radius:2px;">
          <label style="font-size:0.68rem;color:#D5BE9A;text-transform:uppercase;">Stat 4</label>
          <div style="display:flex;gap:6px;margin:6px 0;">
            <input type="number" name="stat_4_number" value="<?= htmlspecialchars($settings['stat_4_number'] ?? 100) ?>" style="width:70%;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:6px;font-size:0.9rem;">
            <input type="text" name="stat_4_suffix" value="<?= htmlspecialchars($settings['stat_4_suffix'] ?? '%') ?>" style="width:30%;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#D5BE9A;padding:6px;font-size:0.9rem;">
          </div>
          <input type="text" name="stat_4_label" value="<?= htmlspecialchars($settings['stat_4_label'] ?? 'Satisfaction') ?>" style="width:100%;box-sizing:border-box;background:#1E1A17;border:1px solid rgba(213,190,154,0.3);color:#D2C8BC;padding:6px;font-size:0.78rem;">
        </div>
      </div>
    </div>

    <!-- SUBMIT BAR -->
    <div style="position:sticky;bottom:20px;background:rgba(23,22,20,0.95);border:1px solid #D5BE9A;padding:16px 24px;border-radius:3px;display:flex;justify-content:space-between;align-items:center;backdrop-filter:blur(10px);box-shadow:0 10px 40px rgba(0,0,0,0.6);">
      <span style="color:#F5F0E8;font-size:0.85rem;">Ready to publish changes live to the website?</span>
      <button type="submit" style="padding:12px 32px;background:#D5BE9A;color:#171614;border:none;font-size:0.85rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;cursor:pointer;border-radius:2px;">
        Publish All Content &rarr;
      </button>
    </div>

  </form>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
