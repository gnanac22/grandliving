<?php require_once 'config/db.php'; require_once 'includes/functions.php'; ?>
<?php $page_title = "Our Story | Grand Furniture Vizag"; ?>
<?php $page_description = "Learn about Grand Furniture Vizag — 30+ years crafting luxury furniture in Visakhapatnam."; ?>
<?php include 'includes/header.php'; ?>

<style>
  /* ===========================
     CSS CUSTOM PROPERTIES
  =========================== */
  :root {
    --obsidian:   #171614;
    --espresso:   #29221D;
    --ivory:      #F5F0E8;
    --champagne:  #D5BE9A;
    --walnut:     #76543B;
    --stone:      #B7AA98;
    --sand:       #E8DFD2;
    --dark-card:  #1E1A17;

    --font-display: 'Cormorant Garamond', Georgia, serif;
    --font-body:    'Manrope', system-ui, sans-serif;

    --transition-base: 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    --max-width: 1280px;
    --section-pad: 120px 0;
  }

  /* ===========================
     GLOBAL RESETS (page-scoped)
  =========================== */
  .about-page-wrap *,
  .about-page-wrap *::before,
  .about-page-wrap *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  .about-page-wrap {
    overflow-x: hidden;
    font-family: var(--font-body);
  }

  .about-container {
    max-width: var(--max-width);
    margin: 0 auto;
    padding: 0 48px;
  }

  /* ===========================
     SECTION 1 — ABOUT HERO
  =========================== */
  .about-hero {
    position: relative;
    width: 100%;
    height: 100vh;
    min-height: 600px;
    background: linear-gradient(160deg, #171614 0%, #29221D 50%, #76543B 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  .about-hero__inner {
    text-align: center;
    z-index: 2;
    padding: 0 24px;
  }

  .about-hero__label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    font-family: var(--font-body);
    font-size: 0.7rem;
    font-variant: small-caps;
    letter-spacing: 0.25em;
    color: var(--champagne);
    text-transform: uppercase;
    margin-bottom: 32px;
    opacity: 0;
  }

  .about-hero__label::before,
  .about-hero__label::after {
    content: '';
    display: block;
    width: 60px;
    height: 1px;
    background: var(--champagne);
    opacity: 0.6;
  }

  .about-hero__headline {
    font-family: var(--font-display);
    font-size: clamp(4rem, 9vw, 8rem);
    font-weight: 400;
    color: var(--ivory);
    letter-spacing: 0.05em;
    line-height: 0.95;
    text-transform: uppercase;
    margin-bottom: 32px;
  }

  .about-hero__headline .line {
    display: block;
    overflow: hidden;
  }

  .about-hero__subtitle {
    font-family: var(--font-body);
    font-size: 1rem;
    color: var(--stone);
    letter-spacing: 0.04em;
    max-width: 480px;
    margin: 0 auto;
    line-height: 1.7;
    opacity: 0;
  }

  /* Scroll indicator */
  .about-hero__scroll {
    position: absolute;
    bottom: 36px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    opacity: 0;
    animation: heroScrollFade 1.4s ease 2.2s forwards;
  }

  .about-hero__scroll span {
    font-family: var(--font-body);
    font-size: 0.6rem;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: var(--stone);
  }

  .about-hero__scroll-line {
    width: 1px;
    height: 48px;
    background: linear-gradient(to bottom, var(--champagne), transparent);
    animation: scrollPulse 2s ease-in-out 2.8s infinite;
  }

  @keyframes heroScrollFade {
    to { opacity: 1; }
  }

  @keyframes scrollPulse {
    0%, 100% { transform: scaleY(1); opacity: 1; }
    50%       { transform: scaleY(0.6); opacity: 0.4; }
  }

  /* Hero grain overlay */
  .about-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
    pointer-events: none;
    z-index: 1;
    opacity: 0.5;
  }

  /* ===========================
     SECTION 2 — BRAND STORY
  =========================== */
  .brand-story {
    background: var(--espresso);
    padding: var(--section-pad);
  }

  .brand-story__grid {
    display: grid;
    grid-template-columns: 40% 60%;
    gap: 80px;
    align-items: start;
  }

  /* Left — Pull quote */
  .brand-story__quote-wrap {
    position: sticky;
    top: 80px;
  }

  .brand-story__quotemark {
    font-family: var(--font-display);
    font-size: 10rem;
    line-height: 0.8;
    color: var(--champagne);
    opacity: 0.4;
    display: block;
    margin-bottom: 24px;
    user-select: none;
  }

  .brand-story__quote-text {
    font-family: var(--font-display);
    font-style: italic;
    font-size: 1.8rem;
    color: var(--ivory);
    line-height: 1.4;
  }

  /* Right — Narrative */
  .brand-story__label {
    font-family: var(--font-body);
    font-size: 0.7rem;
    font-variant: small-caps;
    letter-spacing: 0.25em;
    color: var(--champagne);
    text-transform: uppercase;
    margin-bottom: 24px;
    display: block;
  }

  .brand-story__narrative p {
    font-family: var(--font-body);
    font-size: 1rem;
    color: var(--stone);
    line-height: 1.8;
    margin-bottom: 24px;
  }

  .brand-story__narrative p:last-child {
    margin-bottom: 0;
  }

  /* ===========================
     SECTION 3 — PHILOSOPHY
  =========================== */
  .philosophy {
    background: var(--obsidian);
    padding: var(--section-pad);
  }

  .philosophy__inner {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
  }

  .philosophy__label {
    font-family: var(--font-body);
    font-size: 0.7rem;
    font-variant: small-caps;
    letter-spacing: 0.25em;
    color: var(--champagne);
    text-transform: uppercase;
    margin-bottom: 40px;
    display: block;
  }

  .philosophy__rule {
    display: block;
    width: 100%;
    height: 1px;
    background: var(--champagne);
    opacity: 0.3;
    margin: 36px auto;
    max-width: 540px;
  }

  .philosophy__headline {
    font-family: var(--font-display);
    font-size: clamp(3rem, 7vw, 6rem);
    font-weight: 400;
    color: var(--ivory);
    letter-spacing: 0.08em;
    line-height: 1;
    text-transform: uppercase;
  }

  .philosophy__headline .line {
    display: block;
  }

  .philosophy__body {
    font-family: var(--font-body);
    font-size: 1.1rem;
    color: var(--stone);
    line-height: 1.8;
    max-width: 700px;
    margin: 0 auto;
  }

  /* ===========================
     SECTION 4 — OUR VALUES
  =========================== */
  .our-values {
    background: var(--dark-card);
    padding: 100px 0;
  }

  .section-header {
    text-align: center;
    margin-bottom: 72px;
  }

  .section-header__label {
    font-family: var(--font-body);
    font-size: 0.7rem;
    font-variant: small-caps;
    letter-spacing: 0.25em;
    color: var(--champagne);
    text-transform: uppercase;
    display: block;
    margin-bottom: 16px;
  }

  .section-header__title {
    font-family: var(--font-display);
    font-size: 3rem;
    font-weight: 400;
    color: var(--ivory);
  }

  .values-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
  }

  .value-card {
    position: relative;
    background: linear-gradient(165deg, rgba(41,34,29,0.92) 0%, rgba(23,22,20,0.96) 100%);
    border: 1px solid rgba(213,190,154,0.18);
    border-top: 2px solid var(--champagne);
    border-radius: 4px;
    padding: 44px 32px;
    transition: all 0.45s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: default;
    will-change: transform, box-shadow;
    overflow: hidden;
    backdrop-filter: blur(8px);
  }

  .value-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 60%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(213,190,154,0.08), transparent);
    transform: skewX(-20deg);
    transition: left 0.75s ease;
    pointer-events: none;
  }

  .value-card:hover::before {
    left: 180%;
  }

  .value-card:hover {
    border-color: rgba(213,190,154,0.4);
    border-top-color: #F5F0E8;
    transform: translateY(-8px);
    box-shadow: 0 24px 60px rgba(0,0,0,0.6), 0 0 30px rgba(213,190,154,0.12);
  }

  .value-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
  }

  .value-card__badge {
    font-family: var(--font-display);
    font-size: 1.1rem;
    font-weight: 300;
    letter-spacing: 0.1em;
    color: rgba(213,190,154,0.45);
    border: 1px solid rgba(213,190,154,0.22);
    border-radius: 2px;
    padding: 2px 10px;
    transition: all 0.35s ease;
  }

  .value-card:hover .value-card__badge {
    color: var(--champagne);
    border-color: var(--champagne);
    background: rgba(213,190,154,0.08);
  }

  .value-card__icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(213,190,154,0.14) 0%, rgba(23,22,20,0.7) 100%);
    border: 1px solid rgba(213,190,154,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.4s ease;
  }

  .value-card:hover .value-card__icon-wrap {
    background: radial-gradient(circle, rgba(213,190,154,0.25) 0%, rgba(23,22,20,0.9) 100%);
    border-color: var(--champagne);
    transform: scale(1.08) rotate(4deg);
    box-shadow: 0 0 20px rgba(213,190,154,0.25);
  }

  .value-card__icon {
    width: 26px;
    height: 26px;
    color: var(--champagne);
    transition: color 0.3s ease;
  }

  .value-card:hover .value-card__icon {
    color: var(--ivory);
  }

  .value-card__title {
    font-family: var(--font-display);
    font-size: 1.7rem;
    color: var(--ivory);
    font-weight: 400;
    margin-bottom: 14px;
    letter-spacing: 0.01em;
    transition: color 0.3s ease;
  }

  .value-card:hover .value-card__title {
    color: var(--champagne);
  }

  .value-card__body {
    font-family: var(--font-body);
    font-size: 0.92rem;
    color: #D2C8BC;
    line-height: 1.75;
  }

  /* ===========================
     SECTION 5 — SHOWROOM PHOTOS
  =========================== */
  .showroom-photos {
    background: var(--espresso);
    padding: 100px 0;
  }

  .photos-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 64px;
  }

  .photo-card {
    display: flex;
    flex-direction: column;
    gap: 16px;
    perspective: 1100px;
  }

  .photo-placeholder {
    aspect-ratio: 16 / 11;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-end;
    padding: 20px;
    position: relative;
    overflow: hidden;
    border-radius: 4px;
    border: 1px solid rgba(213,190,154,0.3);
    background: #171614;
    transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.5s ease, border-color 0.4s ease;
    will-change: transform;
  }

  .photo-placeholder:hover {
    border-color: rgba(213,190,154,0.6);
    box-shadow: 0 24px 60px rgba(0,0,0,0.6), 0 0 30px rgba(213,190,154,0.1);
  }

  .photo-card__image {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    z-index: 1;
    display: block;
    transform: scale(1.02);
    transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1), filter 0.8s ease;
    pointer-events: none;
  }

  .photo-card:hover .photo-card__image {
    transform: scale(1.08);
    filter: saturate(1.1) brightness(1.05);
  }

  .photo-placeholder::after {
    content: '';
    position: absolute;
    inset: 0;
    z-index: 2;
    background: linear-gradient(180deg, rgba(23,22,20,0.05) 0%, rgba(23,22,20,0.15) 50%, rgba(23,22,20,0.85) 100%);
    pointer-events: none;
    transition: opacity 0.4s ease;
  }

  .photo-card:hover .photo-placeholder::after {
    opacity: 0.75;
  }

  .photo-card__eyebrow {
    position: absolute;
    top: 16px;
    left: 16px;
    z-index: 3;
    padding: 6px 12px;
    color: var(--ivory);
    background: rgba(23,22,20,0.82);
    border: 1px solid rgba(213,190,154,0.35);
    border-radius: 2px;
    font: 600 0.62rem var(--font-body);
    letter-spacing: 0.16em;
    text-transform: uppercase;
    backdrop-filter: blur(8px);
    transition: all 0.3s ease;
  }

  .photo-card:hover .photo-card__eyebrow {
    border-color: var(--champagne);
    color: var(--champagne);
  }

  .photo-caption {
    font-family: var(--font-body);
    font-size: 0.95rem;
    color: var(--ivory);
    display: flex;
    align-items: center;
    justify-content: space-between;
    letter-spacing: 0.02em;
    padding: 0 4px;
  }

  .photo-caption__meta {
    color: var(--champagne);
    font-size: 0.72rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    font-weight: 600;
  }

  .showroom-address {
    text-align: center;
  }

  .showroom-address__text {
    font-family: var(--font-body);
    font-size: 1rem;
    color: var(--stone);
    margin-bottom: 24px;
  }

  .showroom-address__text strong {
    color: var(--ivory);
  }

  /* ===========================
     BUTTONS
  =========================== */
  .btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 36px;
    font-family: var(--font-body);
    font-size: 0.75rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: var(--transition-base);
    font-weight: 600;
  }

  .btn--dark {
    background: var(--obsidian);
    color: var(--ivory);
    border: 1px solid var(--obsidian);
  }

  .btn--dark:hover {
    background: var(--espresso);
    border-color: var(--espresso);
  }

  .btn--ghost-dark {
    background: transparent;
    color: var(--obsidian);
    border: 1px solid var(--obsidian);
  }

  .btn--ghost-dark:hover {
    background: var(--obsidian);
    color: var(--ivory);
  }

  .btn--ghost-champagne {
    background: transparent;
    color: var(--champagne);
    border: 1px solid var(--champagne);
  }

  .btn--ghost-champagne:hover {
    background: var(--champagne);
    color: var(--obsidian);
  }

  .btn-group {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    justify-content: center;
  }

  /* ===========================
     SECTION 6 — TIMELINE
  =========================== */
  .timeline {
    background: var(--obsidian);
    padding: 100px 0;
  }

  .timeline__track {
    position: relative;
    max-width: 900px;
    margin: 0 auto;
    padding: 40px 0;
  }

  /* Centre vertical line */
  .timeline__track::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, transparent, var(--champagne) 10%, var(--champagne) 90%, transparent);
    transform: translateX(-50%);
  }

  .timeline__item {
    display: flex;
    align-items: flex-start;
    gap: 40px;
    margin-bottom: 64px;
    position: relative;
  }

  .timeline__item:last-child {
    margin-bottom: 0;
  }

  /* Left items */
  .timeline__item--left {
    flex-direction: row;
    padding-right: calc(50% + 60px);
  }

  /* Right items */
  .timeline__item--right {
    flex-direction: row-reverse;
    padding-left: calc(50% + 60px);
  }

  .timeline__badge {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    top: 0;
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: var(--champagne);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-display);
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--obsidian);
    letter-spacing: 0.03em;
    z-index: 2;
    flex-shrink: 0;
    box-shadow: 0 0 0 4px var(--obsidian), 0 0 0 6px rgba(213, 190, 154, 0.3);
  }

  .timeline__card {
    background: var(--espresso);
    padding: 32px;
    flex: 1;
    position: relative;
  }

  .timeline__card-title {
    font-family: var(--font-display);
    font-size: 1.3rem;
    color: var(--ivory);
    font-weight: 400;
    margin-bottom: 10px;
  }

  .timeline__card-body {
    font-family: var(--font-body);
    font-size: 0.9rem;
    color: var(--stone);
    line-height: 1.7;
  }

  /* Arrow for left card (points right toward centre) */
  .timeline__item--left .timeline__card::after {
    content: '';
    position: absolute;
    right: -10px;
    top: 24px;
    width: 0;
    height: 0;
    border-top: 10px solid transparent;
    border-bottom: 10px solid transparent;
    border-left: 10px solid var(--espresso);
  }

  /* Arrow for right card (points left toward centre) */
  .timeline__item--right .timeline__card::after {
    content: '';
    position: absolute;
    left: -10px;
    top: 24px;
    width: 0;
    height: 0;
    border-top: 10px solid transparent;
    border-bottom: 10px solid transparent;
    border-right: 10px solid var(--espresso);
  }

  /* ===========================
     SECTION 7 — CTA
  =========================== */
  .about-cta {
    background: linear-gradient(135deg, #D5BE9A, #E8DFD2);
    padding: 100px 0;
  }

  .about-cta__inner {
    text-align: center;
  }

  .about-cta__headline {
    font-family: var(--font-display);
    font-size: 3rem;
    font-weight: 400;
    color: var(--obsidian);
    margin-bottom: 20px;
    line-height: 1.1;
  }

  .about-cta__sub {
    font-family: var(--font-body);
    font-size: 1rem;
    color: var(--espresso);
    margin-bottom: 48px;
    letter-spacing: 0.03em;
  }

  /* ===========================
     RESPONSIVE — 992px
  =========================== */
  @media (max-width: 992px) {
    .about-container {
      padding: 0 32px;
    }

    .brand-story__grid {
      grid-template-columns: 1fr;
      gap: 48px;
    }

    .brand-story__quote-wrap {
      position: static;
    }

    .values-grid {
      grid-template-columns: repeat(2, 1fr);
    }

    .photos-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
    }

    .timeline__item--left {
      padding-right: calc(50% + 48px);
    }

    .timeline__item--right {
      padding-left: calc(50% + 48px);
    }
  }

  /* ===========================
     RESPONSIVE — 768px
  =========================== */
  @media (max-width: 768px) {
    :root {
      --section-pad: 80px 0;
    }

    .about-container {
      padding: 0 24px;
    }

    .about-hero__headline {
      font-size: clamp(3.5rem, 12vw, 5.5rem);
    }

    .philosophy__headline {
      font-size: clamp(2.5rem, 10vw, 4rem);
    }

    .section-header__title {
      font-size: 2.2rem;
    }

    .about-cta__headline {
      font-size: 2.2rem;
    }

    .values-grid {
      grid-template-columns: 1fr;
    }

    .photos-grid {
      grid-template-columns: 1fr;
    }

    .photos-grid .photo-card:last-child {
      max-width: 100%;
      grid-column: auto;
    }

    /* Collapse timeline to single column */
    .timeline__track::before {
      left: 36px;
    }

    .timeline__badge {
      left: 36px;
      width: 60px;
      height: 60px;
      font-size: 0.8rem;
    }

    .timeline__item--left,
    .timeline__item--right {
      flex-direction: column;
      padding-left: 96px;
      padding-right: 0;
    }

    .timeline__item--left .timeline__card::after,
    .timeline__item--right .timeline__card::after {
      display: none;
    }

    .brand-story__quotemark {
      font-size: 6rem;
    }

    .brand-story__quote-text {
      font-size: 1.4rem;
    }
  }

  /* ===========================
     RESPONSIVE — 576px
  =========================== */
  @media (max-width: 576px) {
    .about-container {
      padding: 0 16px;
    }

    .philosophy__headline {
      font-size: clamp(1.875rem, 8vw, 2.2rem);
      letter-spacing: 0.01em;
      overflow-x: clip;
    }

    .about-hero__label::before,
    .about-hero__label::after {
      width: 30px;
    }

    .value-card {
      padding: 36px 24px;
    }

    .btn {
      padding: 14px 24px;
      font-size: 0.7rem;
    }

    .btn-group {
      flex-direction: column;
      align-items: center;
    }

    .timeline__track::before {
      left: 24px;
    }

    .timeline__badge {
      left: 24px;
      width: 48px;
      height: 48px;
      font-size: 0.7rem;
    }

    .timeline__item--left,
    .timeline__item--right {
      padding-left: 76px;
    }
  }
</style>

<div class="about-page-wrap">

  <!-- =========================================
       SECTION 1 — ABOUT HERO
  ========================================= -->
  <section class="about-hero" id="about-hero">
    <div class="about-hero__inner">
      <div class="about-hero__label" id="heroLabel">
        Established in Visakhapatnam
      </div>

      <h1 class="about-hero__headline" id="heroHeadline">
        <span class="line" data-line="1">OUR</span>
        <span class="line" data-line="2">STORY.</span>
      </h1>

      <p class="about-hero__subtitle" id="heroSubtitle">
        Three decades of transforming homes with the art of fine furniture.
      </p>
    </div>

    <div class="about-hero__scroll" aria-hidden="true">
      <span>Scroll</span>
      <div class="about-hero__scroll-line"></div>
    </div>
  </section><!-- /.about-hero -->


  <!-- =========================================
       SECTION 2 — BRAND STORY
  ========================================= -->
  <section class="brand-story" id="brand-story">
    <div class="about-container">
      <div class="brand-story__grid">

        <!-- Left: Pull Quote -->
        <div class="brand-story__quote-wrap" id="brandQuote">
          <span class="brand-story__quotemark" aria-hidden="true">&ldquo;</span>
          <blockquote class="brand-story__quote-text">
            Every piece of furniture is a promise &mdash; to endure, to delight, to feel like home.
          </blockquote>
        </div>

        <!-- Right: Narrative -->
        <div class="brand-story__narrative" id="brandNarrative">
          <span class="brand-story__label">The Grand Story</span>

          <!-- Admin: Update this copy via CMS or directly in this file -->
          <p>
            Grand Furniture Vizag was born out of a singular passion: to bring world-class furniture craftsmanship to the homes of Visakhapatnam. Founded over three decades ago by [Founder Name], what began as a modest workshop has grown into one of the region&rsquo;s most trusted furniture destinations.
          </p>
          <p>
            Our journey has been shaped by the trust of thousands of families who have invited our creations into their homes. Every sofa, every bed frame, every dining table carries with it the weight of this responsibility &mdash; and the pride of our artisans.
          </p>
          <p>
            Today, Grand Furniture Vizag stands as a testament to what happens when craftsmanship meets vision. We do not simply make furniture. We craft the backdrop of your life&rsquo;s most precious moments.
          </p>
        </div>

      </div>
    </div>
  </section><!-- /.brand-story -->


  <!-- =========================================
       SECTION 3 — PHILOSOPHY
  ========================================= -->
  <section class="philosophy" id="philosophy">
    <div class="about-container">
      <div class="philosophy__inner">
        <span class="philosophy__label" id="philLabel">Our Philosophy</span>

        <span class="philosophy__rule" id="philRuleTop" aria-hidden="true"></span>

        <h2 class="philosophy__headline" id="philHeadline">
          <span class="line">FURNITURE IS NOT</span>
          <span class="line">MERELY AN OBJECT.</span>
        </h2>

        <span class="philosophy__rule" id="philRuleBottom" aria-hidden="true"></span>

        <p class="philosophy__body" id="philBody">
          It is the stage upon which your life unfolds. The chair where morning coffee tastes better. The table around which family gathers. The bed that holds the weight of your dreams. At Grand Furniture Vizag, we design with this truth at heart &mdash; that great furniture doesn&rsquo;t just fill space, it creates it.
        </p>
      </div>
    </div>
  </section><!-- /.philosophy -->


  <!-- =========================================
       SECTION 4 — OUR VALUES
  ========================================= -->
  <section class="our-values" id="our-values">
    <div class="about-container">
      <div class="section-header">
        <span class="section-header__label">What We Stand For</span>
        <h2 class="section-header__title">Our Core Values</h2>
      </div>

      <div class="values-grid">

        <!-- Value 1: Quality -->
        <div class="value-card" data-value="1">
          <div class="value-card__header">
            <div class="value-card__icon-wrap">
              <svg class="value-card__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <!-- Gem / Diamond icon -->
                <path d="M6 3h12l4 6-10 13L2 9z"/>
                <path d="M11 3L8 9l4 13 4-13-3-6"/>
                <path d="M2 9h20"/>
              </svg>
            </div>
            <span class="value-card__badge">01</span>
          </div>
          <h3 class="value-card__title">Quality</h3>
          <p class="value-card__body">We refuse to compromise on materials, finish, or detail. Every product leaves our workshop meeting the highest standards.</p>
        </div>

        <!-- Value 2: Craftsmanship -->
        <div class="value-card" data-value="2">
          <div class="value-card__header">
            <div class="value-card__icon-wrap">
              <svg class="value-card__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <!-- Tools icon -->
                <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
              </svg>
            </div>
            <span class="value-card__badge">02</span>
          </div>
          <h3 class="value-card__title">Craftsmanship</h3>
          <p class="value-card__body">Our artisans bring generations of woodworking knowledge to every piece. No two items are ever truly identical.</p>
        </div>

        <!-- Value 3: Integrity -->
        <div class="value-card" data-value="3">
          <div class="value-card__header">
            <div class="value-card__icon-wrap">
              <svg class="value-card__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <!-- Shield icon -->
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
              </svg>
            </div>
            <span class="value-card__badge">03</span>
          </div>
          <h3 class="value-card__title">Integrity</h3>
          <p class="value-card__body">We price fairly, deliver on time, and stand behind everything we sell. Your trust is our most valuable asset.</p>
        </div>

        <!-- Value 4: Service -->
        <div class="value-card" data-value="4">
          <div class="value-card__header">
            <div class="value-card__icon-wrap">
              <svg class="value-card__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <!-- Handshake icon -->
                <path d="M20.42 4.58a5.4 5.4 0 00-7.65 0l-.77.78-.77-.78a5.4 5.4 0 00-7.65 0C1.46 6.7 1.33 10.28 4 13l8 8 8-8c2.67-2.72 2.54-6.3.42-8.42z"/>
                <path d="M12 5.36L8.87 8.5a2.13 2.13 0 003 3L12 11l1.13-1.5a2.13 2.13 0 003-3L13 5.36"/>
              </svg>
            </div>
            <span class="value-card__badge">04</span>
          </div>
          <h3 class="value-card__title">Service</h3>
          <p class="value-card__body">From your first visit to our showroom through years of after-sales care, we are committed to your satisfaction.</p>
        </div>

      </div>
    </div>
  </section><!-- /.our-values -->


  <!-- =========================================
       SECTION 5 — SHOWROOM PHOTOS
  ========================================= -->
  <section class="showroom-photos" id="showroom-photos">
    <div class="about-container">
      <div class="section-header">
        <span class="section-header__label">The Showroom</span>
        <h2 class="section-header__title">Experience It In Person</h2>
      </div>

      <div class="photos-grid" id="photosGrid">

        <!-- Photo 1: Showroom Exterior -->
        <div class="photo-card">
          <div class="photo-placeholder">
            <img class="photo-card__image" src="/uploads/gallery/showroom-entrance.jpg" alt="Grand Living furniture showroom exterior in Visakhapatnam" loading="eager">
            <span class="photo-card__eyebrow">FLAGSHIP • VISAKHAPATNAM</span>
          </div>
          <div class="photo-caption">
            <span>Showroom Exterior</span>
            <span class="photo-caption__meta">01 / 04</span>
          </div>
        </div>

        <!-- Photo 2: Curated Living Room Display -->
        <div class="photo-card">
          <div class="photo-placeholder">
            <img class="photo-card__image" src="/uploads/gallery/showroom-living.jpg" alt="Curated Living Room Display at Grand Living Vizag" loading="eager">
            <span class="photo-card__eyebrow">CURATED LIVING</span>
          </div>
          <div class="photo-caption">
            <span>Curated Living Room Display</span>
            <span class="photo-caption__meta">02 / 04</span>
          </div>
        </div>

        <!-- Photo 3: Showroom Photo (Flagship Interior) -->
        <div class="photo-card">
          <div class="photo-placeholder">
            <img class="photo-card__image" src="/uploads/gallery/showroom-main.jpg" alt="Grand Living Flagship Showroom Main Floor in Visakhapatnam" loading="eager">
            <span class="photo-card__eyebrow">SHOWROOM FLOOR</span>
          </div>
          <div class="photo-caption">
            <span>Showroom Photo</span>
            <span class="photo-caption__meta">03 / 04</span>
          </div>
        </div>

        <!-- Photo 4: Bedroom Collection -->
        <div class="photo-card">
          <div class="photo-placeholder">
            <img class="photo-card__image" src="/uploads/gallery/showroom-bedroom.jpg" alt="Master Bedroom Collection at Grand Living Vizag" loading="eager">
            <span class="photo-card__eyebrow">BEDROOM COLLECTION</span>
          </div>
          <div class="photo-caption">
            <span>Bedroom Collection</span>
            <span class="photo-caption__meta">04 / 04</span>
          </div>
        </div>

      </div><!-- /.photos-grid -->

      <div class="showroom-address" id="showroomAddress">
        <p class="showroom-address__text">
          <strong>Visit us at:</strong> Grand Living, Dwaraka Nagar Main Road, Visakhapatnam, Andhra Pradesh – 530 016
        </p>
        <a
          href="https://maps.app.goo.gl/4H7RJMT3GGWLZR7R7"
          target="_blank"
          rel="noopener noreferrer"
          class="btn btn--ghost-champagne"
        >
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;vertical-align:middle;">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
          Get Directions &rarr;
        </a>
      </div>

    </div>
  </section><!-- /.showroom-photos -->


  <!-- =========================================
       SECTION 6 — TIMELINE / MILESTONES
  ========================================= -->
  <section class="timeline" id="timeline">
    <div class="about-container">
      <div class="section-header">
        <span class="section-header__label">Our Journey</span>
        <h2 class="section-header__title">30 Years of Excellence</h2>
      </div>

      <div class="timeline__track">

        <!-- Milestone 1 — ~1994 -->
        <div class="timeline__item timeline__item--left" data-timeline="1">
          <div class="timeline__badge">1994</div>
          <div class="timeline__card">
            <h3 class="timeline__card-title">Founded in Visakhapatnam</h3>
            <p class="timeline__card-body">Began as a small furniture workshop with a vision to serve the growing city.</p>
          </div>
        </div>

        <!-- Milestone 2 — ~2000 -->
        <div class="timeline__item timeline__item--right" data-timeline="2">
          <div class="timeline__badge">2000</div>
          <div class="timeline__card">
            <h3 class="timeline__card-title">First Showroom Opened</h3>
            <p class="timeline__card-body">Opened our flagship showroom, bringing our collection directly to customers.</p>
          </div>
        </div>

        <!-- Milestone 3 — ~2008 -->
        <div class="timeline__item timeline__item--left" data-timeline="3">
          <div class="timeline__badge">2008</div>
          <div class="timeline__card">
            <h3 class="timeline__card-title">Expanded Product Range</h3>
            <p class="timeline__card-body">Introduced bedroom, dining, and office furniture collections to meet growing demand.</p>
          </div>
        </div>

        <!-- Milestone 4 — ~2015 -->
        <div class="timeline__item timeline__item--right" data-timeline="4">
          <div class="timeline__badge">2015</div>
          <div class="timeline__card">
            <h3 class="timeline__card-title">1,000 Families Served</h3>
            <p class="timeline__card-body">Reached the milestone of furnishing over a thousand homes across Andhra Pradesh.</p>
          </div>
        </div>

        <!-- Milestone 5 — ~2024 -->
        <div class="timeline__item timeline__item--left" data-timeline="5">
          <div class="timeline__badge">2024</div>
          <div class="timeline__card">
            <h3 class="timeline__card-title">Three Decades Strong</h3>
            <p class="timeline__card-body">Celebrating 30+ years of trust, craftsmanship, and beautiful living spaces.</p>
          </div>
        </div>

      </div><!-- /.timeline__track -->
    </div>
  </section><!-- /.timeline -->


  <!-- =========================================
       SECTION 7 — CTA
  ========================================= -->
  <section class="about-cta" id="about-cta">
    <div class="about-container">
      <div class="about-cta__inner" id="ctaInner">
        <h2 class="about-cta__headline">Ready to Find Your Perfect Furniture?</h2>
        <p class="about-cta__sub">Explore our catalogue or visit our showroom in Visakhapatnam.</p>
        <div class="btn-group">
          <a href="/collection" class="btn btn--dark">Explore Collection</a>
          <a href="/contact" class="btn btn--ghost-dark">Visit Showroom</a>
        </div>
      </div>
    </div>
  </section><!-- /.about-cta -->

</div><!-- /.about-page-wrap -->


<!-- =========================================
     GSAP + SplitType (CDN)
========================================= -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/split-type@0.3.4/umd/index.min.js" defer></script>

<script>
(function () {
  /* ---- Wait for all deps to be ready ---- */
  function waitForGSAP(cb) {
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined' && typeof SplitType !== 'undefined') {
      cb();
    } else {
      setTimeout(function () { waitForGSAP(cb); }, 60);
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    waitForGSAP(initAnimations);
  });

  function initAnimations() {
    gsap.registerPlugin(ScrollTrigger);

    /* ============================================
       HERO — Label & Subtitle fade-in
    ============================================ */
    var heroLabel    = document.getElementById('heroLabel');
    var heroSubtitle = document.getElementById('heroSubtitle');

    gsap.to(heroLabel, {
      opacity: 1,
      y: 0,
      duration: 1,
      ease: 'power3.out',
      delay: 0.4
    });

    /* ============================================
       HERO — SplitType headline char stagger
    ============================================ */
    var heroHeadlineEl = document.getElementById('heroHeadline');
    if (heroHeadlineEl) {
      var heroSplit = new SplitType(heroHeadlineEl, { types: 'chars, words, lines' });

      gsap.set(heroSplit.chars, { y: '110%', opacity: 0 });

      gsap.to(heroSplit.chars, {
        y: '0%',
        opacity: 1,
        duration: 0.9,
        ease: 'power4.out',
        stagger: 0.045,
        delay: 0.6
      });
    }

    gsap.to(heroSubtitle, {
      opacity: 1,
      y: 0,
      duration: 1.1,
      ease: 'power3.out',
      delay: 1.3
    });

    /* ============================================
       BRAND STORY — Quote & Narrative fade-in
    ============================================ */
    var brandQuote     = document.getElementById('brandQuote');
    var brandNarrative = document.getElementById('brandNarrative');

    if (brandQuote) {
      gsap.from(brandQuote, {
        scrollTrigger: { trigger: brandQuote, start: 'top 80%', once: true },
        x: -60,
        opacity: 0,
        duration: 1.1,
        ease: 'power3.out'
      });
    }

    if (brandNarrative) {
      var paragraphs = brandNarrative.querySelectorAll('p');
      gsap.from(paragraphs, {
        scrollTrigger: { trigger: brandNarrative, start: 'top 80%', once: true },
        y: 40,
        opacity: 0,
        duration: 0.9,
        ease: 'power3.out',
        stagger: 0.18
      });
    }

    /* ============================================
       PHILOSOPHY — Dramatic scale-in headline
    ============================================ */
    var philHeadline = document.getElementById('philHeadline');
    var philLabel    = document.getElementById('philLabel');
    var philBody     = document.getElementById('philBody');
    var philRuleTop  = document.getElementById('philRuleTop');
    var philRuleBot  = document.getElementById('philRuleBottom');

    if (philHeadline) {
      var philSplit = new SplitType(philHeadline, { types: 'lines' });

      var philTL = gsap.timeline({
        scrollTrigger: {
          trigger: '#philosophy',
          start: 'top 80%',
          once: true
        }
      });

      philTL
        .from(philLabel,   { opacity: 0, y: 20, duration: 0.7, ease: 'power3.out' })
        .from(philRuleTop, { scaleX: 0, opacity: 0, duration: 0.8, ease: 'power3.out', transformOrigin: 'left center' }, '-=0.3')
        .from(philSplit.lines, {
          scale: 1.12,
          opacity: 0,
          y: 24,
          duration: 1,
          ease: 'power4.out',
          stagger: 0.15
        }, '-=0.4')
        .from(philRuleBot, { scaleX: 0, opacity: 0, duration: 0.8, ease: 'power3.out', transformOrigin: 'right center' }, '-=0.5')
        .from(philBody,    { opacity: 0, y: 30, duration: 1, ease: 'power3.out' }, '-=0.4');
    }

    /* ============================================
       VALUES — Cards stagger in from bottom
    ============================================ */
    var valueCards = document.querySelectorAll('.value-card');
    if (valueCards.length) {
      gsap.from(valueCards, {
        scrollTrigger: {
          trigger: '#our-values',
          start: 'top 80%',
          once: true
        },
        y: 60,
        opacity: 0,
        duration: 0.8,
        ease: 'power3.out',
        stagger: 0.12
      });

      // Subtle 3D atelier tilt on hover
      if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        valueCards.forEach(function (card) {
          card.addEventListener('mousemove', function (e) {
            var rect = card.getBoundingClientRect();
            var x = (e.clientX - rect.left) / rect.width - 0.5;
            var y = (e.clientY - rect.top) / rect.height - 0.5;
            gsap.to(card, {
              rotationY: x * 8,
              rotationX: -y * 8,
              transformPerspective: 900,
              duration: 0.35,
              ease: 'power2.out'
            });
          });
          card.addEventListener('mouseleave', function () {
            gsap.to(card, {
              rotationY: 0,
              rotationX: 0,
              duration: 0.55,
              ease: 'power3.out'
            });
          });
        });
      }
    }

    /* ============================================
       SHOWROOM PHOTOS — stagger in
    ============================================ */
    var photoCards = document.querySelectorAll('.photo-card');
    if (photoCards.length) {
      gsap.from(photoCards, {
        scrollTrigger: {
          trigger: '#photosGrid',
          start: 'top 80%',
          once: true
        },
        y: 50,
        duration: 0.9,
        ease: 'power3.out',
        stagger: 0.15
      });
    }

    var showroomPhotoCards = document.querySelectorAll('.photo-card');
    var canTiltPhotos = window.matchMedia('(hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)').matches;
    showroomPhotoCards.forEach(function (card) {
      var frame = card.querySelector('.photo-placeholder');
      var image = card.querySelector('.photo-card__image');
      if (!frame || !image) return;

      gsap.fromTo(image, { scale: 1.16 }, {
        scale: 1.035,
        duration: 1.8,
        ease: 'power3.out',
        scrollTrigger: { trigger: card, start: 'top 82%', once: true }
      });

      if (!canTiltPhotos) return;
      card.addEventListener('pointermove', function (event) {
        var bounds = frame.getBoundingClientRect();
        var offsetX = (event.clientX - bounds.left) / bounds.width - 0.5;
        var offsetY = (event.clientY - bounds.top) / bounds.height - 0.5;
        gsap.to(frame, {
          rotationX: offsetY * -6,
          rotationY: offsetX * 8,
          transformPerspective: 1000,
          duration: 0.35,
          ease: 'power2.out',
          overwrite: 'auto'
        });
        gsap.to(image, {
          x: offsetX * -14,
          y: offsetY * -10,
          scale: 1.08,
          duration: 0.45,
          ease: 'power2.out',
          overwrite: 'auto'
        });
      });
      card.addEventListener('pointerleave', function () {
        gsap.to(frame, { rotationX: 0, rotationY: 0, duration: 0.65, ease: 'power3.out', overwrite: 'auto' });
        gsap.to(image, { x: 0, y: 0, scale: 1.035, duration: 0.7, ease: 'power3.out', overwrite: 'auto' });
      });
    });

    var showroomAddr = document.getElementById('showroomAddress');
    if (showroomAddr) {
      gsap.from(showroomAddr, {
        scrollTrigger: { trigger: showroomAddr, start: 'top 85%', once: true },
        y: 30,
        opacity: 0,
        duration: 0.9,
        ease: 'power3.out'
      });
    }

    /* ============================================
       TIMELINE — Alternating left/right animate-in
    ============================================ */
    var timelineItems = document.querySelectorAll('.timeline__item');
    timelineItems.forEach(function (item) {
      var isLeft  = item.classList.contains('timeline__item--left');
      var card    = item.querySelector('.timeline__card');
      var badge   = item.querySelector('.timeline__badge');

      var xOffset = isLeft ? -60 : 60;

      var tl = gsap.timeline({
        scrollTrigger: {
          trigger: item,
          start: 'top 80%',
          once: true
        }
      });

      tl.from(badge, { scale: 0, opacity: 0, duration: 0.5, ease: 'back.out(1.7)' })
        .from(card,  { x: xOffset, opacity: 0, duration: 0.8, ease: 'power3.out' }, '-=0.2');
    });

    /* ============================================
       CTA — Parallax-like entrance
    ============================================ */
    var ctaInner    = document.getElementById('ctaInner');
    var ctaSection  = document.getElementById('about-cta');

    if (ctaInner && ctaSection) {
      /* Entrance animation */
      gsap.from(ctaInner.children, {
        scrollTrigger: { trigger: ctaSection, start: 'top 80%', once: true },
        y: 50,
        opacity: 0,
        duration: 1,
        ease: 'power3.out',
        stagger: 0.18
      });

      /* Subtle parallax on scroll */
      gsap.to(ctaInner, {
        scrollTrigger: {
          trigger: ctaSection,
          start: 'top bottom',
          end: 'bottom top',
          scrub: 1.5
        },
        y: -40,
        ease: 'none'
      });
    }

    /* ============================================
       General fade-in utility for section headers
    ============================================ */
    document.querySelectorAll('.section-header').forEach(function (header) {
      gsap.from(header.children, {
        scrollTrigger: { trigger: header, start: 'top 85%', once: true },
        y: 30,
        duration: 0.8,
        ease: 'power3.out',
        stagger: 0.14
      });
    });

  }/* end initAnimations */

})();
</script>

<?php include 'includes/footer.php'; ?>
