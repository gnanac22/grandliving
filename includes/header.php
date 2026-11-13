<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    @session_start();
}
$page_title       = $page_title       ?? 'Grand Living Vizag — The Art of Living Beautifully';
$page_description = $page_description ?? 'Grand Living Vizag — Visakhapatnam premier luxury handcrafted furniture showroom. Made-to-order sofas, beds, dining tables, wardrobes, and bespoke custom furniture.';
$current_page     = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($page_description) ?>">
  
  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_description) ?>">
  <meta property="og:site_name" content="Grand Living Vizag">
  
  <!-- Preconnect -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Manrope:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- CSS -->
  <link rel="stylesheet" href="/assets/css/design-system.css">
  <link rel="stylesheet" href="/assets/css/main.css">
  <link rel="stylesheet" href="/assets/css/cursor.css">
  
  <!-- GSAP & SplitType -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
  <script src="https://unpkg.com/split-type"></script>

  <!-- CSRF Meta -->
  <meta name="csrf-token" content="<?= htmlspecialchars($csrf_token ?? '') ?>">
  
  <style>
    /* ============================================================
       HAUTE-COUTURE ARCHITECTURAL CURTAIN PRELOADER
       ============================================================ */
    .grand-loader {
      position: fixed;
      inset: 0;
      z-index: 9999999;
      display: flex;
      align-items: center;
      justify-content: center;
      pointer-events: all;
      overflow: hidden;
      background: transparent;
    }
    
    .grand-loader__curtain {
      position: absolute;
      left: 0;
      right: 0;
      width: 100%;
      height: 50.5%;
      background: #171614;
      z-index: 1;
      will-change: transform;
      transition: transform 1.1s cubic-bezier(0.77, 0, 0.175, 1);
    }
    
    .grand-loader__curtain--top {
      top: 0;
      border-bottom: 1px solid rgba(213, 190, 154, 0.2);
    }
    
    .grand-loader__curtain--bottom {
      bottom: 0;
      border-top: 1px solid rgba(213, 190, 154, 0.2);
    }
    
    .grand-loader__inner {
      position: relative;
      z-index: 2;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding: 20px;
      transition: opacity 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                  transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    
    .grand-loader__crest {
      margin-bottom: 24px;
      animation: crestRotate 12s linear infinite;
    }
    
    @keyframes crestRotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    
    .grand-loader__title {
      font-family: 'Cormorant Garamond', Georgia, serif;
      font-size: clamp(2.4rem, 6vw, 4.2rem);
      font-weight: 300;
      letter-spacing: 0.22em;
      color: #F5F0E8;
      display: block;
      text-transform: uppercase;
      line-height: 1;
      text-shadow: 0 4px 25px rgba(0,0,0,0.8);
    }
    
    .grand-loader__sub {
      font-family: 'Manrope', sans-serif;
      font-size: 0.72rem;
      letter-spacing: 0.4em;
      color: #D5BE9A;
      display: block;
      margin-top: 14px;
      text-transform: uppercase;
      font-weight: 600;
    }
    
    .grand-loader__progress-bar {
      width: 220px;
      height: 1.5px;
      background: rgba(213, 190, 154, 0.15);
      margin-top: 36px;
      position: relative;
      overflow: hidden;
    }
    
    .grand-loader__progress-fill {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      width: 0%;
      background: #D5BE9A;
      box-shadow: 0 0 12px rgba(213, 190, 154, 0.9);
      transition: width 0.08s ease-out;
    }
    
    .grand-loader__counter-wrap {
      margin-top: 14px;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.3rem;
      font-style: italic;
      color: #D5BE9A;
      letter-spacing: 0.12em;
    }
    
    /* Reveal Animation State */
    .grand-loader.is-done .grand-loader__inner {
      opacity: 0;
      transform: translateY(-25px);
    }
    
    .grand-loader.is-done .grand-loader__curtain--top {
      transform: translateY(-100%);
    }
    
    .grand-loader.is-done .grand-loader__curtain--bottom {
      transform: translateY(100%);
    }
    .grand-loader.is-done {
      pointer-events: none !important;
      visibility: hidden !important;
    }
  </style>
</head>
<body>

<!-- Haute-Couture Architectural Curtain Loader -->
<div class="grand-loader" id="grandLoader" aria-hidden="true">
  <div class="grand-loader__curtain grand-loader__curtain--top"></div>
  <div class="grand-loader__curtain grand-loader__curtain--bottom"></div>
  
  <div class="grand-loader__inner">
    <!-- Geometric Gold Monogram Crest -->
    <div class="grand-loader__crest">
      <svg width="68" height="68" viewBox="0 0 100 100" fill="none">
        <circle cx="50" cy="50" r="48" stroke="#D5BE9A" stroke-width="0.8" stroke-dasharray="3 4" opacity="0.6"/>
        <circle cx="50" cy="50" r="41" stroke="#D5BE9A" stroke-width="0.6" opacity="0.4"/>
        <path d="M64 34 C 56 28, 40 28, 34 38 C 28 47, 28 57, 34 65 C 40 73, 56 73, 64 66 L 64 50 L 48 50" stroke="#D5BE9A" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <!-- Editorial Brand Name -->
    <div class="grand-loader__brand-wrap">
      <span class="grand-loader__title">GRAND LIVING</span>
      <span class="grand-loader__sub">VISAKHAPATNAM &bull; ARCHITECTURAL LUXURY</span>
    </div>

    <!-- Hairline progress bar & percentage counter -->
    <div class="grand-loader__progress-bar">
      <div class="grand-loader__progress-fill" id="loaderFill"></div>
    </div>
    
    <div class="grand-loader__counter-wrap">
      <span class="grand-loader__counter" id="loaderCounter">00</span>
      <span class="grand-loader__counter-symbol">%</span>
    </div>
  </div>
</div>

<!-- ============================================================
     NAVIGATION
     ============================================================ -->
<nav class="nav" id="mainNav" aria-label="Main navigation">
  <div class="nav__inner">
    
    <!-- Logo -->
    <a href="/" class="nav__logo" aria-label="Grand Living Home">
      <span class="nav__logo-text">
        <span style="display:block;font-size:1.15em;letter-spacing:0.2em;font-weight:400;">GRAND LIVING</span>
        <span style="display:block;font-size:0.52em;letter-spacing:0.42em;opacity:0.75;margin-top:2px;color:#D5BE9A;">VISAKHAPATNAM</span>
      </span>
    </a>

    <!-- Desktop Links -->
    <ul class="nav__links" id="navLinks">
      <li><a href="/" class="nav__link <?= $current_page === 'index' ? 'is-active' : '' ?>">Home</a></li>
      <li><a href="/catalogue.php" class="nav__link <?= $current_page === 'catalogue' ? 'is-active' : '' ?>">Collections</a></li>
      <li><a href="/about.php" class="nav__link <?= $current_page === 'about' ? 'is-active' : '' ?>">Our Story</a></li>
      <li><a href="/gallery.php" class="nav__link <?= $current_page === 'gallery' ? 'is-active' : '' ?>">Gallery</a></li>
      <li><a href="/custom-request.php" class="nav__link <?= $current_page === 'custom-request' ? 'is-active' : '' ?>">Custom Order</a></li>
      <li><a href="/contact.php" class="nav__link <?= $current_page === 'contact' ? 'is-active' : '' ?>">Showroom</a></li>
    </ul>

    <!-- Actions -->
    <div class="nav__actions">
      <!-- WhatsApp -->
      <a href="https://wa.me/<?= htmlspecialchars($whatsapp_default ?? '919052751666') ?>" 
         class="nav__action-btn" target="_blank" rel="noopener" aria-label="WhatsApp Assistant" title="WhatsApp Concierge">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.374 0 0 5.373 0 12c0 2.113.554 4.121 1.523 5.868L.057 23.887l6.187-1.44A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.822 9.822 0 01-5.009-1.368l-.36-.214-3.724.868.9-3.63-.234-.373A9.806 9.806 0 012.182 12C2.182 6.564 6.563 2.182 12 2.182S21.818 6.564 21.818 12 17.437 21.818 12 21.818z"/>
        </svg>
      </a>

      <!-- Google Maps Button -->
      <a href="https://maps.app.goo.gl/4H7RJMT3GGWLZR7R7" target="_blank" rel="noopener" class="nav__action-btn" aria-label="Showroom Directions" title="Find Showroom on Google Maps">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
          <circle cx="12" cy="10" r="3"></circle>
        </svg>
      </a>
      
      <!-- Hamburger -->
      <button class="nav__hamburger" id="navHamburger" aria-expanded="false" aria-controls="navMobile" aria-label="Toggle navigation menu">
        <span class="nav__hamburger-line"></span>
        <span class="nav__hamburger-line"></span>
        <span class="nav__hamburger-line"></span>
      </button>
    </div>
  </div>
</nav>

<!-- Full-Screen Haute-Couture Architectural Atelier Navigation -->
<div class="nav__mobile" id="navMobile" aria-hidden="true">
  <!-- Subtle Background Geometric Monogram Watermark -->
  <div class="atelier-nav__watermark" aria-hidden="true">G</div>

  <div class="atelier-nav__container">
    <div class="atelier-nav__grid">
      
      <!-- Left Column: Numbered Architectural Navigation -->
      <div class="atelier-nav__left">
        <div class="atelier-nav__eyebrow">
          <span class="atelier-nav__eyebrow-line"></span>
          <span>THE GRAND ATELIER DIRECTORY &bull; VIZAG</span>
        </div>

        <nav class="atelier-nav__menu" aria-label="Expanded site navigation">
          <!-- 01 Home -->
          <a href="/" class="atelier-nav__item <?= $current_page === 'index' ? 'is-active' : '' ?>">
            <span class="atelier-nav__num">01</span>
            <div class="atelier-nav__item-content">
              <span class="atelier-nav__title">Home</span>
              <span class="atelier-nav__sub">The Art of Living Beautifully</span>
            </div>
            <span class="atelier-nav__arrow">&rarr;</span>
          </a>

          <!-- 02 Collections -->
          <a href="/catalogue.php" class="atelier-nav__item <?= $current_page === 'catalogue' ? 'is-active' : '' ?>">
            <span class="atelier-nav__num">02</span>
            <div class="atelier-nav__item-content">
              <span class="atelier-nav__title">Collections</span>
              <span class="atelier-nav__sub">Living, Bedroom, Dining, Office &amp; Outdoor</span>
            </div>
            <span class="atelier-nav__arrow">&rarr;</span>
          </a>

          <!-- 03 Our Story -->
          <a href="/about.php" class="atelier-nav__item <?= $current_page === 'about' ? 'is-active' : '' ?>">
            <span class="atelier-nav__num">03</span>
            <div class="atelier-nav__item-content">
              <span class="atelier-nav__title">Our Story</span>
              <span class="atelier-nav__sub">Artisanal Legacy, Architecture &amp; Heritage</span>
            </div>
            <span class="atelier-nav__arrow">&rarr;</span>
          </a>

          <!-- 04 Atelier Gallery -->
          <a href="/gallery.php" class="atelier-nav__item <?= $current_page === 'gallery' ? 'is-active' : '' ?>">
            <span class="atelier-nav__num">04</span>
            <div class="atelier-nav__item-content">
              <span class="atelier-nav__title">Atelier Gallery</span>
              <span class="atelier-nav__sub">Curated Spatial Photography &amp; Showroom</span>
            </div>
            <span class="atelier-nav__arrow">&rarr;</span>
          </a>

          <!-- 05 Bespoke Studio -->
          <a href="/custom-request.php" class="atelier-nav__item <?= $current_page === 'custom-request' ? 'is-active' : '' ?>">
            <span class="atelier-nav__num">05</span>
            <div class="atelier-nav__item-content">
              <span class="atelier-nav__title">Bespoke Orders</span>
              <span class="atelier-nav__sub">Custom Architectural Furniture Studio</span>
            </div>
            <span class="atelier-nav__arrow">&rarr;</span>
          </a>

          <!-- 06 Showroom & Directions -->
          <a href="/contact.php" class="atelier-nav__item <?= $current_page === 'contact' ? 'is-active' : '' ?>">
            <span class="atelier-nav__num">06</span>
            <div class="atelier-nav__item-content">
              <span class="atelier-nav__title">Showroom Directions</span>
              <span class="atelier-nav__sub">Dwaraka Nagar Flagship, Visakhapatnam</span>
            </div>
            <span class="atelier-nav__arrow">&rarr;</span>
          </a>
        </nav>
      </div>

      <!-- Right Column: Flagship Showroom & Concierge Showcase (Desktop & Tablet) -->
      <div class="atelier-nav__right">
        <div class="atelier-nav__card">
          <div class="atelier-nav__card-header">
            <span class="atelier-nav__card-badge">FLAGSHIP SHOWROOM</span>
            <h3 class="atelier-nav__card-title">Grand Living Visakhapatnam</h3>
            <p class="atelier-nav__card-desc">
              Experience the tactile weight of handcrafted teak, imported Italian leathers, and sculpted marble in person.
            </p>
          </div>

          <!-- Location & Hours -->
          <div class="atelier-nav__card-details">
            <div class="atelier-nav__detail-row">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
              <span>Grand Living, Dwaraka Nagar Main Road, Visakhapatnam, AP – 530 016</span>
            </div>
            <div class="atelier-nav__detail-row">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span>Mon–Sat: 10:00 AM – 8:00 PM &nbsp;|&nbsp; Sun: 11:00 AM – 6:00 PM</span>
            </div>
          </div>

          <!-- Direct CTAs -->
          <div class="atelier-nav__card-actions">
            <a href="https://maps.app.goo.gl/4H7RJMT3GGWLZR7R7" target="_blank" rel="noopener noreferrer" class="atelier-nav__btn atelier-nav__btn--primary">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
              Open in Google Maps
            </a>
            <div class="atelier-nav__btn-group">
              <a href="tel:09052751666" class="atelier-nav__btn atelier-nav__btn--outline">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                </svg>
                Call Concierge
              </a>
              <a href="https://wa.me/<?= htmlspecialchars($whatsapp_default ?? '919052751666') ?>" target="_blank" rel="noopener noreferrer" class="atelier-nav__btn atelier-nav__btn--whatsapp">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.374 0 0 5.373 0 12c0 2.113.554 4.121 1.523 5.868L.057 23.887l6.187-1.44A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.822 9.822 0 01-5.009-1.368l-.36-.214-3.724.868.9-3.63-.234-.373A9.806 9.806 0 012.182 12C2.182 6.564 6.563 2.182 12 2.182S21.818 6.564 21.818 12 17.437 21.818 12 21.818z"/>
                </svg>
                WhatsApp
              </a>
            </div>
          </div>

          <!-- Quick Category Tags -->
          <div class="atelier-nav__card-categories">
            <span class="atelier-nav__cat-label">QUICK CURATIONS:</span>
            <div class="atelier-nav__cat-pills">
              <a href="/catalogue.php?category=living-room">Living Room</a>
              <a href="/catalogue.php?category=bedroom">Bedroom</a>
              <a href="/catalogue.php?category=dining-room">Dining</a>
              <a href="/catalogue.php?category=home-office">Home Office</a>
              <a href="/catalogue.php?category=outdoor">Outdoor</a>
            </div>
          </div>
        </div>
      </div>

    </div><!-- /atelier-nav__grid -->

    <!-- Menu Footer Bar -->
    <div class="atelier-nav__footer">
      <div class="atelier-nav__footer-brand">
        <span class="atelier-nav__footer-title">THE ART OF LIVING BEAUTIFULLY.</span>
        <span class="atelier-nav__footer-loc">ESTABLISHED IN VISAKHAPATNAM, ANDHRA PRADESH</span>
      </div>
      <div class="atelier-nav__footer-credit">
        DONE BY GNANACHANDRA &bull; <a href="#" target="_blank" rel="noopener">GNANACREATS</a>
      </div>
    </div>

  </div><!-- /atelier-nav__container -->
</div>

<main>
