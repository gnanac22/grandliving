<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/frontend-functions.php';

$phone_primary = getSetting('phone_primary', $pdo) ?: '09052751666';
$phone_secondary = getSetting('phone_secondary', $pdo);
$email = getSetting('email', $pdo) ?: 'hello@grandfurniturevizag.com';
$address = getSetting('address', $pdo) ?: '12-3-456, MG Road, Dwaraka Nagar, Visakhapatnam, Andhra Pradesh – 530 016';
$whatsapp = getSetting('whatsapp_number', $pdo) ?: '919052751666';
$hours = getSetting('business_hours', $pdo) ?: 'Monday – Saturday: 10:00 AM – 8:00 PM | Sunday: 11:00 AM – 6:00 PM';
$maps_embed = getSetting('google_maps_embed', $pdo) ?: 'https://maps.google.com/maps?q=Visakhapatnam&output=embed';

$page_title       = "Connect With Grand Furniture Vizag — Visakhapatnam Showroom";
$page_description = "Visit Grand Furniture showroom in Visakhapatnam or schedule a private interior design consultation.";

include 'includes/header.php';
?>

<div style="padding-top:120px;background:#171614;min-height:100vh;color:#F5F0E8;">
  <header style="padding:60px 0 40px;text-align:center;border-bottom:1px solid rgba(213,190,154,0.15);">
    <div class="container">
      <span style="font-family:'Manrope',sans-serif;font-size:0.75rem;font-weight:700;letter-spacing:0.25em;text-transform:uppercase;color:#D5BE9A;display:block;margin-bottom:12px;">Visit Our Showroom</span>
      <h1 style="font-family:'Cormorant Garamond',serif;font-size:clamp(2.5rem, 5vw, 4rem);font-weight:300;color:#F5F0E8;margin-bottom:16px;">
        Connect With Our Design Artisans
      </h1>
      <p style="font-family:'Manrope',sans-serif;font-size:0.95rem;color:#B7AA98;max-width:640px;margin:0 auto;line-height:1.8;">
        Experience our bespoke collections in person or request a dedicated consultation with our furniture architects.
      </p>
    </div>
  </header>

  <section style="padding:80px 0 100px;">
    <div class="container">
      <div class="contact-main-grid" style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.2fr);gap:60px;">
        
        <!-- Showroom Information -->
        <div>
          <div class="contact-info-panel" style="background:#1E1A17;border:1px solid rgba(213,190,154,0.15);padding:36px;border-radius:2px;margin-bottom:30px;">
            <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;color:#D5BE9A;margin-bottom:24px;">Showroom Location</h2>
            
            <div style="margin-bottom:20px;">
              <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.18em;color:#D5BE9A;display:block;margin-bottom:6px;font-weight:700;">Address</span>
              <p style="color:#B7AA98;font-size:0.95rem;line-height:1.7;"><?= nl2br(htmlspecialchars($address)) ?></p>
              <a href="https://maps.app.goo.gl/4H7RJMT3GGWLZR7R7" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:6px;margin-top:8px;color:#D5BE9A;text-decoration:none;font-size:0.8rem;letter-spacing:0.05em;border-bottom:1px solid rgba(213,190,154,0.4);padding-bottom:2px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Open in Google Maps &rarr;
              </a>
            </div>

            <div style="margin-bottom:20px;">
              <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.18em;color:#D5BE9A;display:block;margin-bottom:6px;font-weight:700;">Direct Phone Inquiries</span>
              <p style="color:#F5F0E8;font-size:1.1rem;font-weight:600;">
                <a href="tel:<?= htmlspecialchars($phone_primary) ?>" style="color:#D5BE9A;text-decoration:none;"><?= htmlspecialchars($phone_primary) ?></a>
                <?php if ($phone_secondary !== ''): ?>
                  <span style="color:rgba(183,170,152,0.4);margin:0 8px;">/</span>
                  <a href="tel:<?= htmlspecialchars($phone_secondary) ?>" style="color:#F5F0E8;text-decoration:none;"><?= htmlspecialchars($phone_secondary) ?></a>
                <?php endif; ?>
              </p>
            </div>

            <div style="margin-bottom:20px;">
              <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.18em;color:#D5BE9A;display:block;margin-bottom:6px;font-weight:700;">Email Communication</span>
              <p style="color:#F5F0E8;font-size:0.95rem;">
                <a href="mailto:<?= htmlspecialchars($email) ?>" style="color:#D5BE9A;text-decoration:none;"><?= htmlspecialchars($email) ?></a>
              </p>
            </div>

            <div>
              <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.18em;color:#D5BE9A;display:block;margin-bottom:6px;font-weight:700;">Showroom Hours</span>
              <p style="color:#B7AA98;font-size:0.9rem;line-height:1.7;"><?= nl2br(htmlspecialchars($hours)) ?></p>
            </div>
          </div>

          <a href="https://wa.me/<?= htmlspecialchars($whatsapp) ?>" target="_blank" rel="noopener"
             style="display:flex;align-items:center;justify-content:center;gap:12px;padding:16px;background:#25D366;color:#fff;text-decoration:none;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;border-radius:2px;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.374 0 0 5.373 0 12c0 2.113.554 4.121 1.523 5.868L.057 23.887l6.187-1.44A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.822 9.822 0 01-5.009-1.368l-.36-.214-3.724.868.9-3.63-.234-.373A9.806 9.806 0 012.182 12C2.182 6.564 6.563 2.182 12 2.182S21.818 6.564 21.818 12 17.437 21.818 12 21.818z"/></svg>
            Instant WhatsApp Assistant
          </a>
        </div>

        <!-- Contact Message Form -->
        <div class="contact-form-panel" style="background:#1E1A17;border:1px solid rgba(213,190,154,0.15);padding:36px;border-radius:2px;">
          <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;color:#D5BE9A;margin-bottom:12px;">Send an Inquiry</h2>
          <p style="color:#B7AA98;font-size:0.9rem;margin-bottom:28px;">Our concierge will reply within one business day with details, catalogue guides, or consultation scheduling.</p>

          <form action="/process-enquiry.php" method="POST" style="display:flex;flex-direction:column;gap:18px;">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="contact-form-row" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;">
              <div>
                <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Full Name *</label>
                <input type="text" name="name" required placeholder="Srikanth Varma" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px 14px;font-size:0.9rem;">
              </div>
              <div>
                <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Phone Number *</label>
                <input type="tel" name="phone" required placeholder="09052751666" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px 14px;font-size:0.9rem;">
              </div>
            </div>

            <div class="contact-form-row" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;">
              <div>
                <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Email Address</label>
                <input type="email" name="email" placeholder="client@example.com" style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px 14px;font-size:0.9rem;">
              </div>
              <div>
                <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Subject *</label>
                <select name="subject" required style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px 14px;font-size:0.9rem;">
                  <option value="Showroom Visit Request">Showroom Visit Request</option>
                  <option value="Product Enquiry">Specific Product Pricing &amp; Details</option>
                  <option value="Custom Furniture Commission">Custom Furniture Commission</option>
                  <option value="Architectural Partnership">Architect / Interior Designer Inquiry</option>
                  <option value="General Question">General Question</option>
                </select>
              </div>
            </div>

            <div>
              <label style="display:block;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.12em;color:#D5BE9A;margin-bottom:6px;">Message / Architectural Requirements *</label>
              <textarea name="message" rows="5" required placeholder="Tell us about your home, desired furniture dimensions, or styling requirements..." style="width:100%;box-sizing:border-box;background:#171614;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:12px 14px;font-size:0.9rem;resize:vertical;"></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="padding:16px;font-size:0.85rem;margin-top:10px;">
              Submit Showroom Inquiry
            </button>
          </form>
        </div>

      </div>

      <!-- Map Embed -->
      <div style="margin-top:60px;border:1px solid rgba(213,190,154,0.2);height:400px;border-radius:2px;overflow:hidden;">
        <iframe src="<?= htmlspecialchars($maps_embed) ?>" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
      </div>
    </div>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
