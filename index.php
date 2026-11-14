<?php
// Load config — will fail gracefully if no DB
$db_available = false;
$pdo = null;
try {
    require_once 'config/db.php';
    if ($pdo !== null) {
        $db_available = true;
    }
} catch (Exception $e) {
    // No database — we'll use fallback data
}
require_once 'includes/functions.php';
require_once 'includes/frontend-functions.php';

$page_title       = "Grand Furniture Vizag – The Art of Living Beautifully";
$page_description = "Grand Furniture Vizag offers luxury, handcrafted furniture for every room. Visit our showroom in Visakhapatnam and discover pieces that transform your living spaces.";

$db_error   = !$db_available;
$categories = [];
$inspiration= [];
$products   = [];
$homeTestimonials = [];

if ($db_available && $pdo) {
    try {
        $categories  = getCategories($pdo, 6);
        $inspiration = getGalleryItems($pdo, 'room-inspiration', 6);
        $products    = getFeaturedProducts(12, $pdo);
        $homeTestimonials = $pdo->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY created_at DESC LIMIT 6")->fetchAll();
        $showroom_address = getSettingValue($pdo, 'address',
            'Grand Living, Dwaraka Nagar Main Road, Visakhapatnam, Andhra Pradesh – 530 016');
        $maps_embed       = getSettingValue($pdo, 'google_maps_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3800.672520330607!2d83.30708307593645!3d17.725510783220456!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a394300718ddd9d%3A0x28756797bcfa5bf7!2sGrand%20Living!5e0!3m2!1sen!2sin!4v1731518000000!5m2!1sen!2sin');
        $maps_link        = getSettingValue($pdo, 'maps_link', 'https://maps.app.goo.gl/4H7RJMT3GGWLZR7R7');
        $whatsapp_default = getSettingValue($pdo, 'whatsapp_number', '919052751666');
        $phone_number     = getSettingValue($pdo, 'phone_primary', '09052751666');
        
        // Dynamic CMS Content Settings
        $hero_eyebrow     = getSettingValue($pdo, 'hero_eyebrow', 'HAUTE-COUTURE FURNITURE • VISAKHAPATNAM');
        $hero_headline_1  = getSettingValue($pdo, 'hero_headline_1', 'THE ART OF');
        $hero_headline_2  = getSettingValue($pdo, 'hero_headline_2', 'LIVING BEAUTIFULLY.');
        $hero_subheadline = getSettingValue($pdo, 'hero_subheadline', 'Discover furniture that transforms everyday spaces into extraordinary experiences.');
        $hero_btn1_text   = getSettingValue($pdo, 'hero_btn1_text', 'Explore Collection');
        $hero_btn1_link   = getSettingValue($pdo, 'hero_btn1_link', '/catalogue.php');
        $hero_btn2_text   = getSettingValue($pdo, 'hero_btn2_text', 'Discover Our World');
        $hero_btn2_link   = getSettingValue($pdo, 'hero_btn2_link', '/about.php');

        $legacy_years      = getSettingValue($pdo, 'legacy_years', '30');
        $legacy_title      = getSettingValue($pdo, 'legacy_title', 'Years of Master Artistry');
        $legacy_tagline    = getSettingValue($pdo, 'legacy_tagline', 'Three Decades of Curating Extraordinary Living Spaces & Master Artistry');
        $legacy_hallmark_1 = getSettingValue($pdo, 'legacy_hallmark_1', '100% Solid Teak');
        $legacy_hallmark_2 = getSettingValue($pdo, 'legacy_hallmark_2', 'Master Artisans');
        $legacy_hallmark_3 = getSettingValue($pdo, 'legacy_hallmark_3', 'Handcrafted in AP');

        $values_label   = getSettingValue($pdo, 'values_label', 'What We Stand For');
        $values_title   = getSettingValue($pdo, 'values_title', 'Our Core Values');
        $values_sub     = getSettingValue($pdo, 'values_sub', 'Four foundational pillars guiding three decades of master woodworking and architectural living.');
        $value_1_title  = getSettingValue($pdo, 'value_1_title', 'Quality');
        $value_1_desc   = getSettingValue($pdo, 'value_1_desc', 'We refuse to compromise on materials, finish, or detail. Every product leaves our workshop meeting the highest standards.');
        $value_2_title  = getSettingValue($pdo, 'value_2_title', 'Craftsmanship');
        $value_2_desc   = getSettingValue($pdo, 'value_2_desc', 'Our artisans bring generations of woodworking knowledge to every piece. No two items are ever truly identical.');
        $value_3_title  = getSettingValue($pdo, 'value_3_title', 'Integrity');
        $value_3_desc   = getSettingValue($pdo, 'value_3_desc', 'We price fairly, deliver on time, and stand behind everything we sell. Your trust is our most valuable asset.');
        $value_4_title  = getSettingValue($pdo, 'value_4_title', 'Service');
        $value_4_desc   = getSettingValue($pdo, 'value_4_desc', 'From your first visit to our showroom through years of after-sales care, we are committed to your satisfaction.');

        $stat_1_num     = getSettingValue($pdo, 'stat_1_number', '1000');
        $stat_1_sfx     = getSettingValue($pdo, 'stat_1_suffix', '+');
        $stat_1_lbl     = getSettingValue($pdo, 'stat_1_label', 'Happy Families');
        $stat_2_num     = getSettingValue($pdo, 'stat_2_number', '500');
        $stat_2_sfx     = getSettingValue($pdo, 'stat_2_suffix', '+');
        $stat_2_lbl     = getSettingValue($pdo, 'stat_2_label', 'Products');
        $stat_3_num     = getSettingValue($pdo, 'stat_3_number', '30');
        $stat_3_sfx     = getSettingValue($pdo, 'stat_3_suffix', '+');
        $stat_3_lbl     = getSettingValue($pdo, 'stat_3_label', 'Years Experience');
        $stat_4_num     = getSettingValue($pdo, 'stat_4_number', '100');
        $stat_4_sfx     = getSettingValue($pdo, 'stat_4_suffix', '%');
        $stat_4_lbl     = getSettingValue($pdo, 'stat_4_label', 'Satisfaction');

        $csrf_token       = generateCSRFToken();
    } catch (Exception $e) {
        $db_error         = true;
    }
}

if ($db_error || !$db_available) {
    $showroom_address = $showroom_address ?? 'Grand Living, Dwaraka Nagar Main Road, Visakhapatnam, Andhra Pradesh – 530 016';
    $maps_embed       = $maps_embed ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3800.672520330607!2d83.30708307593645!3d17.725510783220456!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a394300718ddd9d%3A0x28756797bcfa5bf7!2sGrand%20Living!5e0!3m2!1sen!2sin!4v1731518000000!5m2!1sen!2sin';
    $maps_link        = $maps_link ?? 'https://maps.app.goo.gl/4H7RJMT3GGWLZR7R7';
    $whatsapp_default = $whatsapp_default ?? '919052751666';
    $phone_number     = $phone_number ?? '09052751666';
    $csrf_token       = $csrf_token ?? bin2hex(random_bytes(16));
}

/* ── Fallback data ───────────────────────────────────────────────── */
if ($db_error || empty($categories)) {
    $fallback_cat_names = ['Living Room','Bedroom','Dining Room','Home Office','Outdoor','Kids Room'];
    $categories = array_map(function($name) {
        return [
            'id'          => 0,
            'name'        => $name,
            'slug'        => strtolower(str_replace(' ', '-', $name)),
            'image'       => '',
            'description' => '',
        ];
    }, $fallback_cat_names);
}

if ($db_error || empty($inspiration)) {
    $fallback_insp_titles = [
        'Modern Living Room','Master Bedroom Suite','Elegant Dining Space',
        'Home Office Sanctuary','Cozy Reading Corner','Kids Wonderland',
    ];
    $inspiration = array_map(function($title) {
        return ['id' => 0, 'title' => $title, 'image' => '', 'slug' => ''];
    }, $fallback_insp_titles);
}

if ($db_error || empty($products)) {
    $fallback_product_names = [
        'Mahogany Royal Sofa','Teak King Bed Frame','Italian Marble Dining Table',
        'Walnut Study Desk','Luxury Recliner Set','Bookshelf Cabinet',
        'Ottoman Coffee Table','Modular Wardrobe',
    ];
    $fallback_cats = [
        'Living Room','Bedroom','Dining Room','Home Office',
        'Living Room','Study','Living Room','Bedroom',
    ];
    $products = [];
    foreach ($fallback_product_names as $i => $pname) {
        $products[] = [
            'id'               => $i + 1,
            'name'             => $pname,
            'slug'             => strtolower(str_replace(' ', '-', $pname)),
            'category_name'    => $fallback_cats[$i],
            'description'      => 'Exquisitely crafted with premium materials for lasting elegance and everyday comfort.',
            'image'            => '',
            'whatsapp_number'  => $whatsapp_default,
        ];
    }
}

/* Card gradient palettes */
$card_gradients = [
    'linear-gradient(160deg,#29221D 0%,#76543B 100%)',
    'linear-gradient(160deg,#76543B 0%,#D5BE9A 100%)',
    'linear-gradient(160deg,#29221D 0%,#B7AA98 100%)',
    'linear-gradient(160deg,#3D2B1F 0%,#76543B 100%)',
    'linear-gradient(160deg,#29221D 0%,#D5BE9A 100%)',
    'linear-gradient(160deg,#54392A 0%,#B7AA98 100%)',
];
$insp_gradients = [
    'linear-gradient(180deg,#29221D 0%,#76543B 100%)',
    'linear-gradient(180deg,#3D2B1F 0%,#D5BE9A 100%)',
    'linear-gradient(180deg,#1E1A17 0%,#76543B 100%)',
    'linear-gradient(180deg,#29221D 0%,#B7AA98 100%)',
    'linear-gradient(180deg,#54392A 0%,#D5BE9A 100%)',
    'linear-gradient(180deg,#29221D 0%,#54392A 100%)',
];
$prod_gradients = [
    'linear-gradient(145deg,#29221D,#76543B)',
    'linear-gradient(145deg,#3D2B1F,#D5BE9A)',
    'linear-gradient(145deg,#1E1A17,#76543B)',
    'linear-gradient(145deg,#29221D,#B7AA98)',
    'linear-gradient(145deg,#54392A,#D5BE9A)',
    'linear-gradient(145deg,#29221D,#54392A)',
    'linear-gradient(145deg,#3D2B1F,#76543B)',
    'linear-gradient(145deg,#1E1A17,#D5BE9A)',
];

require_once 'includes/header.php';
?>

<!-- ============================================================
     PAGE-SPECIFIC STYLES
     ============================================================ -->
<style>
/* ── Design Tokens ─────────────────────────────────────────────── */
:root {
  --obsidian:   #171614;
  --espresso:   #29221D;
  --ivory:      #F5F0E8;
  --champagne:  #D5BE9A;
  --walnut:     #76543B;
  --stone:      #B7AA98;
  --sand:       #E8DFD2;
  --dark-mid:   #1E1A17;
  --product-bg: #1A1816;

  --ff-serif: 'Cormorant Garamond', 'Georgia', serif;
  --ff-sans:  'Manrope', 'Inter', sans-serif;

  --transition-base: 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  --transition-slow: 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  --shadow-card: 0 20px 60px rgba(0,0,0,0.4);
  --shadow-hover: 0 30px 80px rgba(0,0,0,0.6);
}

/* ── Reset & Base ───────────────────────────────────────────────── */
*, *::before, *::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html {
  scroll-behavior: smooth;
  font-size: 16px;
}

body {
  font-family: var(--ff-sans);
  background-color: var(--obsidian);
  color: var(--ivory);
  line-height: 1.6;
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

img {
  max-width: 100%;
  display: block;
}

a {
  text-decoration: none;
  color: inherit;
}

ul { list-style: none; }

/* ── Typography ─────────────────────────────────────────────────── */
h1, h2, h3, h4, h5, h6 {
  font-family: var(--ff-serif);
  font-weight: 600;
  line-height: 1.1;
}

/* ── Utility ────────────────────────────────────────────────────── */
.section-label {
  font-family: var(--ff-sans);
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.25em;
  text-transform: uppercase;
  color: var(--champagne);
  display: block;
  margin-bottom: 1rem;
}

.section-title {
  font-family: var(--ff-serif);
  font-size: clamp(2.2rem, 4vw, 3.5rem);
  color: var(--ivory);
  line-height: 1.1;
  margin-bottom: 3rem;
}

.section-header {
  text-align: center;
  margin-bottom: 4rem;
}

.container {
  width: 90%;
  max-width: 1320px;
  margin: 0 auto;
}

/* ── Buttons ────────────────────────────────────────────────────── */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-family: var(--ff-sans);
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  padding: 1rem 2.2rem;
  border: 2px solid transparent;
  cursor: pointer;
  transition: var(--transition-base);
  white-space: nowrap;
  position: relative;
  overflow: hidden;
}

.btn-primary {
  background: var(--champagne);
  color: var(--obsidian);
  border-color: var(--champagne);
}
.btn-primary:hover {
  background: #c2a97e;
  border-color: #c2a97e;
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(213,190,154,0.3);
}

.btn-ghost {
  background: transparent;
  color: var(--ivory);
  border-color: var(--ivory);
}
.btn-ghost:hover {
  background: var(--ivory);
  color: var(--obsidian);
  transform: translateY(-2px);
}

.btn-ghost-champ {
  background: transparent;
  color: var(--champagne);
  border-color: var(--champagne);
}
.btn-ghost-champ:hover {
  background: var(--champagne);
  color: var(--obsidian);
  transform: translateY(-2px);
}

/* ── SECTION 1 · CINEMATIC HERO ─────────────────────────────────── */
.hero {
  position: relative;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 0 1.5rem;
  overflow: hidden;
  background: linear-gradient(135deg, #171614 0%, #29221D 40%, #76543B 70%, #D5BE9A 100%);
}

.hero__bg-img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0.52;
  z-index: 0;
  transform: scale(1.15);
  will-change: transform;
}

.hero__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom,
    rgba(23,22,20,0.4) 0%,
    rgba(23,22,20,0.1) 50%,
    rgba(23,22,20,0.65) 100%);
  z-index: 1;
}

.hero__content {
  position: relative;
  z-index: 2;
  max-width: 900px;
}

.hero__deco-line {
  display: block;
  width: 120px;
  height: 1px;
  background: var(--champagne);
  margin: 0 auto 2rem;
  opacity: 0.6;
  transform-origin: left;
}

.hero__deco-line--bottom {
  margin: 2rem auto 0;
}

.hero__headline {
  font-family: var(--ff-serif);
  font-size: clamp(3rem, 8vw, 7rem);
  font-weight: 600;
  letter-spacing: 0.08em;
  color: var(--ivory);
  line-height: 1;
  margin-bottom: 0;
  overflow: hidden;
}

.hero__headline span {
  display: block;
}

.hero__headline .line-accent {
  color: var(--champagne);
  font-style: italic;
}

.hero__subtitle {
  font-family: var(--ff-sans);
  font-size: clamp(0.95rem, 1.8vw, 1.15rem);
  color: var(--stone);
  max-width: 540px;
  margin: 2rem auto;
  letter-spacing: 0.02em;
  line-height: 1.8;
}

.hero__ctas {
  display: flex;
  gap: 1.2rem;
  justify-content: center;
  flex-wrap: wrap;
  margin-top: 2.5rem;
}

/* Scroll indicator */
.hero__scroll {
  position: absolute;
  bottom: 2.5rem;
  left: 50%;
  transform: translateX(-50%);
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.4rem;
  opacity: 0.7;
}

.hero__scroll span {
  font-family: var(--ff-sans);
  font-size: 0.65rem;
  letter-spacing: 0.25em;
  text-transform: uppercase;
  color: var(--stone);
}

.hero__scroll-line {
  width: 1px;
  height: 50px;
  background: linear-gradient(to bottom, var(--champagne), transparent);
  animation: scrollBounce 2s ease-in-out infinite;
}

@keyframes scrollBounce {
  0%, 100% { transform: scaleY(1); opacity: 0.7; }
  50%       { transform: scaleY(1.3); opacity: 1; }
}

/* ── SECTION 2 · BRAND INTRO ────────────────────────────────────── */
.brand-intro {
  background: var(--espresso);
  padding: 100px 0;
  overflow: hidden;
}

.brand-intro__grid {
  display: grid;
  grid-template-columns: 40% 60%;
  gap: 4rem;
  align-items: center;
}

.brand-intro__left {
  text-align: center;
  padding-right: 2rem;
  border-right: 1px solid rgba(213,190,154,0.2);
}

.brand-intro__counter {
  font-family: var(--ff-serif);
  font-size: clamp(5rem, 10vw, 10rem);
  font-weight: 700;
  color: var(--champagne);
  line-height: 1;
  display: block;
  letter-spacing: -0.02em;
}

.brand-intro__counter-label {
  font-family: var(--ff-sans);
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.25em;
  text-transform: uppercase;
  color: var(--stone);
  margin-top: 0.75rem;
  display: block;
}

.brand-intro__right {}

.brand-intro__headline {
  font-family: var(--ff-serif);
  font-size: clamp(2.2rem, 4.5vw, 3.8rem);
  color: var(--ivory);
  line-height: 1.05;
  letter-spacing: 0.04em;
  margin-bottom: 1.5rem;
}

.brand-intro__body {
  font-family: var(--ff-sans);
  font-size: 1rem;
  color: var(--stone);
  line-height: 1.9;
  max-width: 520px;
  margin-bottom: 2rem;
}

.brand-intro__link {
  font-family: var(--ff-sans);
  font-size: 0.85rem;
  font-weight: 700;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--champagne);
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  border-bottom: 1px solid transparent;
  transition: var(--transition-base);
}
.brand-intro__link:hover {
  border-bottom-color: var(--champagne);
  gap: 0.9rem;
}

/* ── SECTION 3 · FEATURED COLLECTIONS ──────────────────────────── */
.collections {
  background: var(--obsidian);
  padding: 100px 0;
}

.collections__grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.collection-card {
  position: relative;
  aspect-ratio: 4 / 5;
  overflow: hidden;
  cursor: pointer;
  border-radius: 2px;
}

.collection-card__bg {
  position: absolute;
  inset: 0;
  transition: transform var(--transition-slow);
}

.collection-card__img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform var(--transition-slow);
}

.collection-card__pattern {
  position: absolute;
  inset: 0;
  opacity: 0.08;
  background-image:
    repeating-linear-gradient(45deg, var(--champagne) 0, var(--champagne) 1px, transparent 0, transparent 50%);
  background-size: 20px 20px;
}

.collection-card__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom,
    transparent 30%,
    rgba(23,22,20,0.55) 65%,
    rgba(23,22,20,0.9) 100%);
  transition: opacity var(--transition-base);
}

.collection-card:hover .collection-card__overlay {
  opacity: 1;
  background: linear-gradient(to bottom,
    transparent 15%,
    rgba(23,22,20,0.6) 55%,
    rgba(23,22,20,0.95) 100%);
}

.collection-card:hover .collection-card__img,
.collection-card:hover .collection-card__bg {
  transform: scale(1.06);
}

.collection-card__body {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 2rem 1.8rem;
  z-index: 2;
  transform: translateY(0);
  transition: var(--transition-base);
}

.collection-card__name {
  font-family: var(--ff-serif);
  font-size: 1.8rem;
  color: var(--ivory);
  line-height: 1.1;
  margin-bottom: 0.5rem;
  display: block;
}

.collection-card__cta {
  font-family: var(--ff-sans);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: var(--champagne);
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  opacity: 0;
  transform: translateY(10px);
  transition: var(--transition-base);
}

.collection-card:hover .collection-card__cta {
  opacity: 1;
  transform: translateY(0);
}

/* ── SECTION 4 · SIGNATURE SHOWCASE ────────────────────────────── */
.signature-showcase {
  background: var(--dark-mid);
  padding: 100px 0;
}

.signature-showcase__grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 5rem;
  align-items: center;
}

.signature-features {
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
}

.feature-item {
  display: flex;
  gap: 1.5rem;
  align-items: flex-start;
  opacity: 0;
  transform: translateX(-30px);
}

.feature-item.is-visible {
  opacity: 1;
  transform: translateX(0);
}

.feature-item__icon {
  flex-shrink: 0;
  width: 44px;
  height: 44px;
  color: var(--champagne);
  margin-top: 0.1rem;
}

.feature-item__title {
  font-family: var(--ff-serif);
  font-size: 1.5rem;
  color: var(--ivory);
  margin-bottom: 0.4rem;
}

.feature-item__desc {
  font-family: var(--ff-sans);
  font-size: 0.9rem;
  color: var(--stone);
  line-height: 1.7;
}

/* ============================================================
   HAUTE-HORLOGERIE 30 YEARS OF LEGACY SHOWCASE
   ============================================================ */
.signature-deco {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 520px;
  border-radius: 6px;
  overflow: hidden;
  background: radial-gradient(ellipse at 50% 45%, #38271C 0%, #201915 50%, #12100E 100%);
  border: 1px solid rgba(213, 190, 154, 0.28);
  box-shadow: 
    inset 0 0 80px rgba(0, 0, 0, 0.85),
    0 30px 80px rgba(0, 0, 0, 0.65),
    0 0 40px rgba(213, 190, 154, 0.08);
  transform-style: preserve-3d;
  transition: border-color 0.4s ease, box-shadow 0.4s ease;
}

.signature-deco:hover {
  border-color: rgba(213, 190, 154, 0.5);
  box-shadow: 
    inset 0 0 80px rgba(0, 0, 0, 0.85),
    0 35px 90px rgba(0, 0, 0, 0.75),
    0 0 60px rgba(213, 190, 154, 0.16);
}

/* Ambient Center Aura */
.signature-deco__glow {
  position: absolute;
  width: 320px;
  height: 320px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(213, 190, 154, 0.18) 0%, rgba(118, 84, 59, 0.12) 45%, transparent 70%);
  filter: blur(25px);
  pointer-events: none;
  animation: glowBreathe 6s ease-in-out infinite alternate;
}

@keyframes glowBreathe {
  0% { transform: scale(0.9); opacity: 0.7; }
  100% { transform: scale(1.15); opacity: 1; }
}

/* Kinetic Gyroscopic Orbital Rings */
.signature-deco__gyro {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  pointer-events: none;
}

.signature-deco__ring {
  position: absolute;
  border-radius: 50%;
  box-sizing: border-box;
  will-change: transform;
}

.signature-deco__ring--outer {
  width: 440px;
  height: 440px;
  border: 1px dashed rgba(213, 190, 154, 0.22);
  animation: orbitCW 32s linear infinite;
}

.signature-deco__ring--mid {
  width: 340px;
  height: 340px;
  border: 1px solid rgba(213, 190, 154, 0.14);
  animation: orbitCCW 24s linear infinite;
}

.signature-deco__ring--inner {
  width: 240px;
  height: 240px;
  border: 1px solid rgba(213, 190, 154, 0.25);
  box-shadow: 0 0 25px rgba(213, 190, 154, 0.12);
  animation: orbitCW 16s linear infinite;
}

/* Orbital satellites / planetary beads */
.gyro-satellite {
  position: absolute;
  width: 7px;
  height: 7px;
  background-color: #D5BE9A;
  border-radius: 50%;
  box-shadow: 0 0 12px #D5BE9A, 0 0 24px rgba(213, 190, 154, 0.8);
}

.gyro-satellite--1 { top: -3.5px; left: 50%; transform: translateX(-50%); }
.gyro-satellite--2 { bottom: -3.5px; left: 50%; transform: translateX(-50%); }
.gyro-satellite--3 { left: -3.5px; top: 50%; transform: translateY(-50%); }

@keyframes orbitCW {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes orbitCCW {
  from { transform: rotate(360deg); }
  to { transform: rotate(0deg); }
}

/* Monumental 3D Floating Shadow Watermark */
.signature-deco__number {
  font-family: var(--ff-serif, 'Cormorant Garamond', serif);
  font-size: clamp(9rem, 20vw, 17rem);
  font-weight: 300;
  color: transparent;
  -webkit-text-stroke: 1.5px rgba(213, 190, 154, 0.08);
  line-height: 1;
  position: absolute;
  user-select: none;
  pointer-events: none;
  letter-spacing: -0.04em;
  animation: watermarkFloat 6s ease-in-out infinite alternate;
}

@keyframes watermarkFloat {
  0% { transform: translateY(-6px) scale(0.98); }
  100% { transform: translateY(6px) scale(1.02); }
}

/* Foreground Medallion Content */
.signature-deco__content {
  position: relative;
  z-index: 3;
  text-align: center;
  padding: clamp(2rem, 4vw, 3rem);
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Heritage Seal Monogram */
.signature-deco__seal {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}

.signature-deco__crest-svg {
  filter: drop-shadow(0 0 10px rgba(213, 190, 154, 0.4));
  animation: crestWobble 14s linear infinite;
}

@keyframes crestWobble {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.signature-deco__seal-text {
  font-family: var(--ff-sans, 'Manrope', sans-serif);
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.28em;
  text-transform: uppercase;
  color: #D5BE9A;
}

/* Digits & Liquid Gold Metallic Specular Sheen */
.signature-deco__digits-wrap {
  display: inline-flex;
  align-items: flex-start;
  justify-content: center;
  position: relative;
  line-height: 0.9;
  margin: 6px 0 14px;
}

.signature-deco__year {
  font-family: var(--ff-serif, 'Cormorant Garamond', Georgia, serif);
  font-size: clamp(5.2rem, 11vw, 8rem);
  font-weight: 300;
  line-height: 0.9;
  display: inline-block;
  background: linear-gradient(110deg, #D5BE9A 0%, #FFF4DD 25%, #D5BE9A 50%, #8C6243 75%, #D5BE9A 100%);
  background-size: 250% auto;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: goldLiquidSheen 4.5s ease-in-out infinite;
  text-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
}

@keyframes goldLiquidSheen {
  0% { background-position: 0% center; }
  50% { background-position: 100% center; }
  100% { background-position: 0% center; }
}

.signature-deco__plus {
  font-family: var(--ff-serif, 'Cormorant Garamond', serif);
  font-size: clamp(2.4rem, 5vw, 3.8rem);
  font-weight: 300;
  color: #D5BE9A;
  margin-left: 2px;
  position: relative;
  top: 6px;
  text-shadow: 0 0 20px rgba(213, 190, 154, 0.6);
  animation: plusGlow 3s ease-in-out infinite alternate;
}

@keyframes plusGlow {
  0% { transform: translateY(0); opacity: 0.8; }
  100% { transform: translateY(-4px); opacity: 1; text-shadow: 0 0 25px rgba(213, 190, 154, 0.9); }
}

/* Subtitle & Tagline Lockup */
.signature-deco__sub-lockup {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  max-width: 380px;
}

.signature-deco__sub {
  font-family: var(--ff-sans, 'Manrope', sans-serif);
  font-size: clamp(0.78rem, 1.4vw, 0.92rem);
  font-weight: 700;
  letter-spacing: 0.32em;
  text-transform: uppercase;
  color: #F5F0E8;
  display: block;
}

.signature-deco__divider {
  width: 50px;
  height: 1.5px;
  background: linear-gradient(90deg, transparent, #D5BE9A, transparent);
  margin: 4px 0;
}

.signature-deco__tagline {
  font-family: var(--ff-sans, 'Manrope', sans-serif);
  font-size: 0.82rem;
  color: #B7AA98;
  line-height: 1.6;
  margin: 0;
}

/* Quality Hallmarks Badges */
.signature-deco__hallmarks {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: center;
  margin-top: 22px;
}

.signature-deco__hallmark {
  font-family: var(--ff-sans, 'Manrope', sans-serif);
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: #D5BE9A;
  background: rgba(23, 22, 20, 0.7);
  border: 1px solid rgba(213, 190, 154, 0.24);
  padding: 5px 12px;
  border-radius: 20px;
  backdrop-filter: blur(6px);
  transition: all 0.3s ease;
}

.signature-deco__hallmark:hover {
  background: #D5BE9A;
  color: #171614;
  border-color: #D5BE9A;
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(213, 190, 154, 0.3);
}

/* ── SECTION 5 · BRAND STATS ────────────────────────────────────── */
.brand-stats {
  background: linear-gradient(135deg, #E8DFD2, #D5BE9A);
  padding: 80px 0;
}

.brand-stats__row {
  display: flex;
  justify-content: space-around;
  align-items: center;
  flex-wrap: wrap;
  gap: 2rem;
}

.stat-item {
  text-align: center;
  padding: 1rem 2rem;
  flex: 1 1 200px;
}

.stat-item__number {
  font-family: var(--ff-serif);
  font-size: clamp(3rem, 6vw, 5rem);
  font-weight: 700;
  color: var(--obsidian);
  line-height: 1;
  display: block;
  letter-spacing: -0.02em;
}

.stat-item__label {
  font-family: var(--ff-sans);
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--walnut);
  margin-top: 0.6rem;
  display: block;
}

.stat-item + .stat-item {
  border-left: 1px solid rgba(118,84,59,0.3);
}

/* ── SECTION 6 · ROOM INSPIRATION ──────────────────────────────── */
.room-inspiration {
  background: var(--obsidian);
  padding: 100px 0;
}

.inspiration-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.inspiration-card {
  position: relative;
  aspect-ratio: 3 / 4;
  overflow: hidden;
  border-radius: 2px;
  cursor: pointer;
}

.inspiration-card__bg {
  position: absolute;
  inset: 0;
  transition: transform var(--transition-slow);
}

.inspiration-card__img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform var(--transition-slow);
}

.inspiration-card__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom,
    transparent 40%,
    rgba(23,22,20,0.95) 100%);
  z-index: 1;
}

.inspiration-card:hover .inspiration-card__bg,
.inspiration-card:hover .inspiration-card__img {
  transform: scale(1.05);
}

.inspiration-card__body {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 2rem 1.5rem;
  z-index: 2;
  transform: translateY(10px);
  transition: var(--transition-base);
}

.inspiration-card:hover .inspiration-card__body {
  transform: translateY(0);
}

.inspiration-card__title {
  font-family: var(--ff-serif);
  font-size: 1.5rem;
  color: var(--ivory);
  margin-bottom: 0.6rem;
}

.inspiration-card__link {
  font-family: var(--ff-sans);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: var(--champagne);
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  opacity: 0;
  transform: translateY(8px);
  transition: var(--transition-base);
}

.inspiration-card:hover .inspiration-card__link {
  opacity: 1;
  transform: translateY(0);
}

/* ── SECTION 7 · FEATURED PRODUCTS ─────────────────────────────── */
.featured-products {
  background: var(--product-bg);
  padding: 100px 0;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
}

.product-card {
  background: var(--espresso);
  border-radius: 2px;
  overflow: hidden;
  transition: var(--transition-base);
  box-shadow: var(--shadow-card);
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-hover);
}

.product-card__img-wrap {
  position: relative;
  aspect-ratio: 1 / 1;
  overflow: hidden;
}

.product-card__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform var(--transition-slow);
}

.product-card:hover .product-card__img {
  transform: scale(1.07);
}

.product-card__placeholder {
  width: 100%;
  height: 100%;
  transition: transform var(--transition-slow);
}

.product-card:hover .product-card__placeholder {
  transform: scale(1.07);
}

.product-card__badge {
  position: absolute;
  top: 1rem;
  left: 1rem;
  font-family: var(--ff-sans);
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  background: var(--champagne);
  color: var(--obsidian);
  padding: 0.3rem 0.7rem;
  border-radius: 2px;
  z-index: 2;
}

.product-card__body {
  padding: 1.4rem 1.5rem 1.8rem;
}

.product-card__name {
  font-family: var(--ff-serif);
  font-size: 1.5rem;
  color: var(--ivory);
  line-height: 1.2;
  margin-bottom: 0.5rem;
}

.product-card__desc {
  font-family: var(--ff-sans);
  font-size: 0.85rem;
  color: var(--stone);
  line-height: 1.65;
  margin-bottom: 1.2rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-card__btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-family: var(--ff-sans);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--champagne);
  border: 1px solid rgba(213,190,154,0.4);
  padding: 0.65rem 1.2rem;
  transition: var(--transition-base);
  border-radius: 1px;
}

.product-card__btn:hover {
  background: var(--champagne);
  color: var(--obsidian);
  border-color: var(--champagne);
}

/* ── SECTION 8 · SHOWROOM ───────────────────────────────────────── */
.showroom-section {
  background: var(--espresso);
  padding: 100px 0;
}

.showroom-section__grid {
  display: grid;
  grid-template-columns: minmax(0, 0.82fr) minmax(0, 1.18fr);
  gap: clamp(2rem, 5vw, 5rem);
  align-items: center;
}

.showroom-info__label {
  font-family: var(--ff-sans);
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.25em;
  text-transform: uppercase;
  color: var(--champagne);
  display: block;
  margin-bottom: 1.2rem;
}

.showroom-info__title {
  font-family: var(--ff-serif);
  font-size: clamp(2rem, 3.5vw, 3rem);
  color: var(--ivory);
  line-height: 1.1;
  margin-bottom: 2rem;
}

.showroom-info__detail {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  margin-bottom: 1.2rem;
}

.showroom-info__detail svg {
  flex-shrink: 0;
  margin-top: 0.1rem;
  color: var(--champagne);
}

.showroom-info__detail p {
  font-family: var(--ff-sans);
  font-size: 0.9rem;
  color: var(--stone);
  line-height: 1.7;
}

.showroom-info__ctas {
  display: flex;
  gap: 1rem;
  margin-top: 2.5rem;
  flex-wrap: wrap;
}

.showroom-scene {
  position: relative;
  width: 100%;
  min-width: 0;
  height: clamp(380px, 42vw, 620px);
  overflow: hidden;
  isolation: isolate;
  background: radial-gradient(ellipse at 50% 70%, rgba(196,158,103,0.15), transparent 58%), #1B1A17;
  touch-action: pan-y;
  cursor: grab;
}

.showroom-scene:active { cursor: grabbing; }

.showroom-scene canvas {
  position: absolute;
  inset: 0;
  display: block;
  width: 100%;
  height: 100%;
  outline: none;
}

.showroom-scene__fallback {
  position: absolute;
  inset: 0;
  display: grid;
  place-content: center;
  text-align: center;
  color: rgba(245,240,232,0.6);
  font: 300 1.6rem 'Cormorant Garamond', Georgia, serif;
  letter-spacing: 0.14em;
}

.showroom-scene__fallback span {
  display: block;
  margin-top: 0.7rem;
  color: var(--champagne);
  font: 600 0.62rem var(--ff-sans);
  letter-spacing: 0.22em;
}

.showroom-scene.is-ready .showroom-scene__fallback { display: none; }

.showroom-scene__caption {
  position: absolute;
  z-index: 1;
  left: clamp(18px, 3vw, 34px);
  bottom: clamp(20px, 3vw, 34px);
  display: grid;
  gap: 7px;
  pointer-events: none;
  text-shadow: 0 2px 18px rgba(0,0,0,0.8);
}

.showroom-scene__eyebrow {
  color: var(--champagne);
  font: 700 0.62rem var(--ff-sans);
  letter-spacing: 0.2em;
}

.showroom-scene__caption-title {
  max-width: 16ch;
  color: var(--ivory);
  font: 300 clamp(1.35rem, 2.2vw, 2rem)/1.1 var(--ff-serif);
}

.showroom-scene__hint {
  position: absolute;
  z-index: 1;
  right: clamp(18px, 3vw, 34px);
  bottom: clamp(24px, 3.2vw, 38px);
  color: rgba(245,240,232,0.72);
  font: 600 0.58rem var(--ff-sans);
  letter-spacing: 0.14em;
  pointer-events: none;
}

.showroom-scene::after {
  position: absolute;
  z-index: 0;
  inset: 48% 0 0;
  background: linear-gradient(180deg, transparent, rgba(17,16,14,0.42));
  content: '';
  pointer-events: none;
}

.showroom-info__ctas .btn { white-space: normal; }

.showroom-info__ctas .btn:last-child {
  flex-basis: 100%;
  width: fit-content;
}

@media (max-width: 992px) {
  .showroom-section__grid { grid-template-columns: minmax(0, 1fr); }
  .showroom-scene { height: clamp(340px, 70vw, 520px); }
}

@media (max-width: 576px) {
  .showroom-scene { height: min(112vw, 430px); min-height: 330px; }
  .showroom-scene__hint { top: 20px; bottom: auto; font-size: 0.52rem; }
  .showroom-info__ctas { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .showroom-info__ctas .btn {
    justify-content: center;
    padding: 0.9rem 0.7rem;
    font-size: 0.67rem;
    letter-spacing: 0.08em;
  }
  .showroom-info__ctas .btn:last-child { grid-column: 1 / -1; width: 100%; }
}

/* ── SECTION 9 · FINAL CTA ──────────────────────────────────────── */
.final-cta {
  background: var(--obsidian);
  padding: 120px 0;
  text-align: center;
  overflow: hidden;
  position: relative;
}

.final-cta__deco-line {
  width: 0;
  height: 1px;
  background: var(--champagne);
  margin: 0 auto 3rem;
  opacity: 0.5;
  display: block;
}

.final-cta__deco-line--bottom {
  margin: 3rem auto 0;
}

.final-cta__headline {
  font-family: var(--ff-serif);
  font-size: clamp(3rem, 7vw, 6rem);
  color: var(--ivory);
  line-height: 1;
  letter-spacing: 0.06em;
  margin-bottom: 1.5rem;
}

.final-cta__headline span {
  display: block;
}

.final-cta__headline .accent {
  color: var(--champagne);
  font-style: italic;
}

.final-cta__subtitle {
  font-family: var(--ff-sans);
  font-size: 1rem;
  color: var(--stone);
  max-width: 480px;
  margin: 0 auto 3rem;
  line-height: 1.8;
}

.final-cta__ctas {
  display: flex;
  gap: 1.2rem;
  justify-content: center;
  flex-wrap: wrap;
}

/* ── RESPONSIVE ─────────────────────────────────────────────────── */
@media (max-width: 1200px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 992px) {
  .brand-intro__grid {
    grid-template-columns: 1fr;
    gap: 3rem;
  }
  .brand-intro__left {
    border-right: none;
    border-bottom: 1px solid rgba(213,190,154,0.2);
    padding-right: 0;
    padding-bottom: 2rem;
  }
  .collections__grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .signature-showcase__grid {
    grid-template-columns: 1fr;
    gap: 3rem;
  }
  .signature-deco {
    min-height: 300px;
  }
  .inspiration-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .showroom-section__grid {
    grid-template-columns: 1fr;
  }
  .map-container {
    height: 320px;
  }
  .stat-item + .stat-item {
    border-left: none;
  }
  .brand-stats__row {
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .collections__grid {
    grid-template-columns: 1fr;
    max-width: 420px;
    margin: 0 auto;
  }
  .inspiration-grid {
    grid-template-columns: 1fr;
    max-width: 420px;
    margin: 0 auto;
  }
  .products-grid {
    grid-template-columns: 1fr;
    max-width: 420px;
    margin: 0 auto;
  }
  .hero__ctas {
    flex-direction: column;
    align-items: center;
  }
  .showroom-info__ctas {
    flex-direction: column;
  }
  .final-cta__ctas {
    flex-direction: column;
    align-items: center;
  }
}

@media (max-width: 576px) {
  .container { width: 92%; }
  .section-title { margin-bottom: 2rem; }
  .hero__scroll { display: none; }
  .hero__headline { font-size: clamp(2.4rem, 9.5vw, 3.2rem) !important; }
  .hero__ctas { width: 100%; gap: 12px; }
  .hero__ctas .btn { width: 100%; max-width: 320px; text-align: center; }
  .signature-deco {
    min-height: 420px;
    padding: 24px 12px;
  }
  .signature-deco__ring--outer {
    width: 290px;
    height: 290px;
  }
  .signature-deco__ring--middle {
    width: 230px;
    height: 230px;
  }
  .signature-deco__ring--inner {
    width: 170px;
    height: 170px;
  }
  .signature-deco__glow {
    width: 220px;
    height: 220px;
  }
  .signature-deco__content {
    padding: 1.5rem 0.5rem;
  }
  .signature-deco__year {
    font-size: 4.8rem !important;
  }
  .brand-stats__row {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 16px !important;
  }
  .stat-item {
    padding: 1rem 0.5rem !important;
    flex: auto !important;
  }
}
</style>

<!-- ============================================================
     SECTION 1 — CINEMATIC HERO
     ============================================================ -->
<section class="hero" id="hero">
  <img class="hero__bg-img" id="heroBg" src="/assets/images/hero-bg.jpg" alt="Grand Living Luxury Furniture Showroom Vizag">
  <div class="hero__overlay"></div>

  <div class="hero__content">
    <!-- Live Deal Urgency Banner (Royaloak Style) -->
    <div class="hero-countdown-pill" id="heroCountdownPill">
      <span>🔥 <strong>FESTIVE SHOWROOM SALE:</strong> Special Pricing Ends In</span>
      <span class="countdown-digits" id="heroCountdown">06h : 45m : 20s</span>
    </div>

    <span class="hero__deco-line" id="heroDeco1"></span>

    <h1 class="hero__headline" id="heroHeadline">
      <span><?= htmlspecialchars($hero_headline_1 ?? 'THE ART OF') ?></span>
      <span class="line-accent"><?= htmlspecialchars($hero_headline_2 ?? 'LIVING BEAUTIFULLY.') ?></span>
    </h1>

    <span class="hero__deco-line hero__deco-line--bottom" id="heroDeco2"></span>

    <p class="hero__subtitle" id="heroSubtitle">
      <?= htmlspecialchars($hero_subheadline ?? 'Discover furniture that transforms everyday spaces into extraordinary experiences.') ?>
    </p>

    <div class="hero__ctas" id="heroCtas">
      <a href="<?= htmlspecialchars($hero_btn1_link ?? '/catalogue.php') ?>" class="btn btn-primary"><?= htmlspecialchars($hero_btn1_text ?? 'Explore Collection') ?></a>
      <a href="<?= htmlspecialchars($hero_btn2_link ?? '/about.php') ?>" class="btn btn-ghost"><?= htmlspecialchars($hero_btn2_text ?? 'Discover Our World') ?></a>
    </div>
  </div>

  <div class="hero__scroll" id="heroScroll">
    <span>Scroll</span>
    <div class="hero__scroll-line"></div>
  </div>
</section>

<!-- ============================================================
     ROYALOAK-STYLE POPULAR CATEGORY ROUNDELS
     ============================================================ -->
<section class="category-roundels-section" aria-label="Browse popular categories">
  <div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
      <span style="font-family:'Cormorant Garamond',serif;font-size:1.45rem;color:#F5F0E8;font-weight:400;letter-spacing:0.02em;">
        Popular Curations <span style="font-family:'Manrope',sans-serif;font-size:0.75rem;color:#D5BE9A;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;margin-left:8px;">• 500+ Showroom Pieces</span>
      </span>
      <a href="/catalogue" style="font-family:'Manrope',sans-serif;font-size:0.75rem;color:#D5BE9A;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:4px;">
        View All Categories &rarr;
      </a>
    </div>

    <div class="category-roundels-wrapper">
      <a href="/catalogue?category=living-room" class="category-roundel">
        <div class="roundel-avatar-wrap">
          <div class="roundel-avatar-inner">
            <img src="/uploads/categories/living-room.jpg" alt="Living Room Furniture">
          </div>
        </div>
        <span class="roundel-label">Sofas &amp; Couches</span>
        <span class="roundel-offer-tag">Up to 45% Off</span>
      </a>

      <a href="/catalogue?category=bedroom" class="category-roundel">
        <div class="roundel-avatar-wrap">
          <div class="roundel-avatar-inner">
            <img src="/uploads/categories/bedroom.jpg" alt="Beds &amp; Bedroom Furniture">
          </div>
        </div>
        <span class="roundel-label">Royal Cots &amp; Beds</span>
        <span class="roundel-offer-tag">100% Teak</span>
      </a>

      <a href="/catalogue?category=dining-room" class="category-roundel">
        <div class="roundel-avatar-wrap">
          <div class="roundel-avatar-inner">
            <img src="/uploads/categories/dining-room.jpg" alt="Dining Room Furniture">
          </div>
        </div>
        <span class="roundel-label">Solid Dining Sets</span>
        <span class="roundel-offer-tag">Live Edge</span>
      </a>

      <a href="/catalogue?category=wardrobes" class="category-roundel">
        <div class="roundel-avatar-wrap">
          <div class="roundel-avatar-inner">
            <img src="/uploads/categories/wardrobes.jpg" alt="Wardrobes &amp; Closets">
          </div>
        </div>
        <span class="roundel-label">Luxury Wardrobes</span>
        <span class="roundel-offer-tag">Custom Fit</span>
      </a>

      <a href="/catalogue?category=home-office" class="category-roundel">
        <div class="roundel-avatar-wrap">
          <div class="roundel-avatar-inner">
            <img src="/uploads/categories/home-office.jpg" alt="Home Office Furniture">
          </div>
        </div>
        <span class="roundel-label">Executive Desks</span>
        <span class="roundel-offer-tag">Ergonomic</span>
      </a>

      <a href="/catalogue?category=living-room" class="category-roundel">
        <div class="roundel-avatar-wrap">
          <div class="roundel-avatar-inner">
            <img src="/uploads/products/solstice-bar-cabinet.jpg" alt="Bar Cabinets">
          </div>
        </div>
        <span class="roundel-label">Bar Cabinets</span>
        <span class="roundel-offer-tag">Brass Accent</span>
      </a>

      <a href="/catalogue?category=outdoor" class="category-roundel">
        <div class="roundel-avatar-wrap">
          <div class="roundel-avatar-inner">
            <img src="/uploads/categories/outdoor.jpg" alt="Outdoor Furniture">
          </div>
        </div>
        <span class="roundel-label">Garden Lounges</span>
        <span class="roundel-offer-tag">All Weather</span>
      </a>

      <a href="/catalogue?category=kids-youth" class="category-roundel">
        <div class="roundel-avatar-wrap">
          <div class="roundel-avatar-inner">
            <img src="/uploads/categories/kids-youth.jpg" alt="Kids &amp; Youth Furniture">
          </div>
        </div>
        <span class="roundel-label">Kids &amp; Youth</span>
        <span class="roundel-offer-tag">Safe Rounded</span>
      </a>
    </div>
  </div>
</section>

<!-- ============================================================
     5 PILLARS / VALUE PROPOSITIONS STRIP (Royaloak Style)
     ============================================================ -->
<div class="value-props-strip">
  <div class="container">
    <div class="value-props-grid">
      
      <div class="value-prop-card">
        <div class="value-prop-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
        </div>
        <div class="value-prop-content">
          <h4>30+ Years Heritage</h4>
          <p>Vizag's most trusted atelier since 1994.</p>
        </div>
      </div>

      <div class="value-prop-card">
        <div class="value-prop-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div class="value-prop-content">
          <h4>100% Genuine Teak</h4>
          <p>CP &amp; Burma teak; Italian leather.</p>
        </div>
      </div>

      <div class="value-prop-card">
        <div class="value-prop-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="value-prop-content">
          <h4>White-Glove Delivery</h4>
          <p>Complimentary setup across Vizag.</p>
        </div>
      </div>

      <div class="value-prop-card">
        <div class="value-prop-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        </div>
        <div class="value-prop-content">
          <h4>0% EMI Options</h4>
          <p>Easy no-cost installments available.</p>
        </div>
      </div>

      <div class="value-prop-card">
        <div class="value-prop-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="value-prop-content">
          <h4>10-Year Warranty</h4>
          <p>Comprehensive frame &amp; joinery protection.</p>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- ============================================================
     SECTION 2 — BRAND INTRODUCTION
     ============================================================ -->
<section class="brand-intro" id="brand-intro">
  <div class="container">
    <div class="brand-intro__grid">

      <div class="brand-intro__left">
        <span class="brand-intro__counter" id="brandCounter">0+</span>
        <span class="brand-intro__counter-label">Years of Craftsmanship</span>
      </div>

      <div class="brand-intro__right">
        <h2 class="brand-intro__headline">
          BEYOND FURNITURE.<br>
          A WAY OF LIVING.
        </h2>
        <p class="brand-intro__body">
          At Grand Furniture Vizag, we believe your home tells your story. Each piece we craft
          carries decades of expertise, an eye for beauty, and a commitment to quality that
          transforms living spaces into personal sanctuaries.
        </p>
        <a href="/about" class="brand-intro__link">Learn Our Story &nbsp;&#8594;</a>
      </div>

    </div>
  </div>
</section>

<!-- ============================================================
     SECTION 3 — FEATURED COLLECTIONS
     ============================================================ -->
<section class="collections" id="collections">
  <div class="container">
    <div class="section-header">
      <span class="section-label">Our Collections</span>
      <h2 class="section-title">Curated for Every Space</h2>
    </div>

    <div class="collections__grid">
      <?php
      $cardGrads = [
          'linear-gradient(160deg,#29221D 0%,#76543B 100%)',
          'linear-gradient(160deg,#76543B 0%,#D5BE9A 100%)',
          'linear-gradient(160deg,#29221D 0%,#B7AA98 100%)',
          'linear-gradient(160deg,#3D2B1F 0%,#76543B 100%)',
          'linear-gradient(160deg,#29221D 0%,#D5BE9A 100%)',
          'linear-gradient(160deg,#54392A 0%,#B7AA98 100%)',
      ];
      foreach ($categories as $idx => $cat):
          $grad     = $cardGrads[$idx % count($cardGrads)];
          $catName  = sanitizeOutput($cat['name']);
          $catSlug  = sanitizeOutput($cat['slug']);
          $catImg   = !empty($cat['image']) ? sanitizeOutput($cat['image']) : '';
          $catImgSrc = $catImg && $catImg[0] === '/' ? $catImg : ($catImg ? '/uploads/categories/' . $catImg : '');
      ?>
      <a href="catalogue.php?category=<?= $catSlug ?>" class="collection-card">
        <div class="collection-card__bg" style="background:<?= $grad ?>;"></div>
        <?php if ($catImg): ?>
          <img class="collection-card__img"
               src="<?= $catImgSrc ?>"
               alt="<?= $catName ?>"
               loading="lazy">
        <?php else: ?>
          <div class="collection-card__pattern"></div>
        <?php endif; ?>
        <div class="collection-card__overlay"></div>
        <div class="collection-card__body">
          <span class="collection-card__name"><?= $catName ?></span>
          <span class="collection-card__cta">Explore Collection &#8594;</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============================================================
     ROYALOAK-INSPIRED INTERNATIONAL COLLECTIONS SECTION
     ============================================================ -->
<section class="international-collections-section" id="international-collections">
  <div class="container">
    <div style="text-align:center;max-width:700px;margin:0 auto;">
      <span style="font-family:'Manrope',sans-serif;font-size:0.75rem;letter-spacing:0.25em;text-transform:uppercase;color:#D5BE9A;display:block;margin-bottom:8px;font-weight:700;">World-Class Craftsmanship</span>
      <h2 style="font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem, 4.5vw, 3.4rem);color:#F5F0E8;font-weight:300;line-height:1.2;margin-bottom:14px;">
        Go Around The World With Grand Living
      </h2>
      <p style="font-family:'Manrope',sans-serif;font-size:0.92rem;color:#B7AA98;line-height:1.7;">
        Explore furniture crafted with global architectural aesthetics — from Tuscan full-grain leather to royal Indian solid teak and German ergonomics.
      </p>
    </div>

    <!-- Country Tabs -->
    <div class="intl-tabs-wrap">
      <button class="intl-tab-btn is-active" data-country="all">
        <span>🌐</span> All Global Masterpieces
      </button>
      <button class="intl-tab-btn" data-country="italy">
        <span>🇮🇹</span> Italian Collection
      </button>
      <button class="intl-tab-btn" data-country="india">
        <span>🇮🇳</span> Royal Teak Heritage
      </button>
      <button class="intl-tab-btn" data-country="usa">
        <span>🇺🇸</span> American Lounge
      </button>
      <button class="intl-tab-btn" data-country="germany">
        <span>🇩🇪</span> German Precision
      </button>
      <button class="intl-tab-btn" data-country="turkey">
        <span>🇹🇷</span> Turkish Velvet
      </button>
    </div>

    <!-- International Products Grid -->
    <div class="intl-grid">
      <!-- 1. Italian Sectional -->
      <article class="intl-card" data-country="italy" data-id="1">
        <div class="intl-card__media">
          <img src="/uploads/products/meridian-sectional-sofa.jpg" alt="Meridian Sectional Sofa" loading="lazy">
          <span class="intl-card__flag-badge">🇮🇹 Italian Modernist</span>
          <span class="intl-card__discount-pill">38% OFF</span>
          <button class="intl-card__wishlist-btn wishlist-toggle" data-product-id="1" aria-label="Save to Wishlist">♡</button>
        </div>
        <div class="intl-card__content">
          <span class="intl-card__meta">Full-Grain Italian Leather</span>
          <h3 class="intl-card__title">Meridian Sectional Sofa</h3>
          <p class="intl-card__desc">A sweeping L-shaped modular sectional upholstered in certified Tuscan leather with solid teak undercarriage.</p>
          <div class="intl-pricing-box">
            <div class="intl-prices">
              <span class="intl-price-current">₹2,80,000</span>
              <span class="intl-price-mrp">₹4,50,000</span>
            </div>
            <div class="intl-emi-pill trigger-emi-calc" data-price="280000">
              ⚡ or ₹23,333/mo • 0% EMI Options &gt;
            </div>
            <div class="intl-card__actions">
              <a href="/products/meridian-sectional-sofa" class="btn btn-outline" style="padding:10px 14px;font-size:0.75rem;flex:1;text-align:center;">View Details</a>
              <a href="https://wa.me/919052751666?text=Hello+Grand+Living+Vizag%21+I+would+like+to+enquire+about+the+Meridian+Sectional+Sofa." target="_blank" rel="noopener" class="btn btn-champagne" style="padding:10px 14px;font-size:0.75rem;">WhatsApp</a>
            </div>
          </div>
        </div>
      </article>

      <!-- 2. Royal Teak Cot -->
      <article class="intl-card" data-country="india" data-id="2">
        <div class="intl-card__media">
          <img src="/uploads/products/classic-teak-cot.jpg" alt="Classic Burma Teak Cot" loading="lazy">
          <span class="intl-card__flag-badge">🇮🇳 Royal Teak Heritage</span>
          <span class="intl-card__discount-pill">35% OFF</span>
          <button class="intl-card__wishlist-btn wishlist-toggle" data-product-id="2" aria-label="Save to Wishlist">♡</button>
        </div>
        <div class="intl-card__content">
          <span class="intl-card__meta">Grade-A Central Province Teak</span>
          <h3 class="intl-card__title">Heirloom Hand-Carved Teak Cot</h3>
          <p class="intl-card__desc">Generational mortise-and-tenon woodworking with hand-rubbed organic oil polish for natural golden patina.</p>
          <div class="intl-pricing-box">
            <div class="intl-prices">
              <span class="intl-price-current">₹1,45,000</span>
              <span class="intl-price-mrp">₹2,25,000</span>
            </div>
            <div class="intl-emi-pill trigger-emi-calc" data-price="145000">
              ⚡ or ₹12,083/mo • 0% EMI Options &gt;
            </div>
            <div class="intl-card__actions">
              <a href="/products/classic-teak-cot" class="btn btn-outline" style="padding:10px 14px;font-size:0.75rem;flex:1;text-align:center;">View Details</a>
              <a href="https://wa.me/919052751666?text=Hello+Grand+Living+Vizag%21+I+would+like+to+enquire+about+the+Heirloom+Teak+Cot." target="_blank" rel="noopener" class="btn btn-champagne" style="padding:10px 14px;font-size:0.75rem;">WhatsApp</a>
            </div>
          </div>
        </div>
      </article>

      <!-- 3. Turkish Velvet Palazzo Bed -->
      <article class="intl-card" data-country="turkey" data-id="3">
        <div class="intl-card__media">
          <img src="/uploads/products/palazzo-king-bed.jpg" alt="Palazzo King Bed" loading="lazy">
          <span class="intl-card__flag-badge">🇹🇷 Turkish Opulence</span>
          <span class="intl-card__discount-pill">40% OFF</span>
          <button class="intl-card__wishlist-btn wishlist-toggle" data-product-id="3" aria-label="Save to Wishlist">♡</button>
        </div>
        <div class="intl-card__content">
          <span class="intl-card__meta">Royal Blue Crushed Velvet</span>
          <h3 class="intl-card__title">Palazzo Grand King Bed</h3>
          <p class="intl-card__desc">Fluted architectural headboard in high-density foam wrapped in imported Ottoman-inspired royal blue velvet.</p>
          <div class="intl-pricing-box">
            <div class="intl-prices">
              <span class="intl-price-current">₹1,95,000</span>
              <span class="intl-price-mrp">₹3,25,000</span>
            </div>
            <div class="intl-emi-pill trigger-emi-calc" data-price="195000">
              ⚡ or ₹16,250/mo • 0% EMI Options &gt;
            </div>
            <div class="intl-card__actions">
              <a href="/products/palazzo-king-bed" class="btn btn-outline" style="padding:10px 14px;font-size:0.75rem;flex:1;text-align:center;">View Details</a>
              <a href="https://wa.me/919052751666?text=Hello+Grand+Living+Vizag%21+I+would+like+to+enquire+about+the+Palazzo+King+Bed." target="_blank" rel="noopener" class="btn btn-champagne" style="padding:10px 14px;font-size:0.75rem;">WhatsApp</a>
            </div>
          </div>
        </div>
      </article>

      <!-- 4. American Lounge Riviera Chair -->
      <article class="intl-card" data-country="usa" data-id="4">
        <div class="intl-card__media">
          <img src="/uploads/products/riviera-accent-chair.jpg" alt="Riviera Accent Chair" loading="lazy">
          <span class="intl-card__flag-badge">🇺🇸 American Lounge</span>
          <span class="intl-card__discount-pill">30% OFF</span>
          <button class="intl-card__wishlist-btn wishlist-toggle" data-product-id="4" aria-label="Save to Wishlist">♡</button>
        </div>
        <div class="intl-card__content">
          <span class="intl-card__meta">Bouclé Fabric &amp; Solid Walnut</span>
          <h3 class="intl-card__title">Riviera Ergonomic Barrel Chair</h3>
          <p class="intl-card__desc">Sculptural barrel-back silhouette crafted for deep lounging with American walnut dowel base.</p>
          <div class="intl-pricing-box">
            <div class="intl-prices">
              <span class="intl-price-current">₹65,000</span>
              <span class="intl-price-mrp">₹95,000</span>
            </div>
            <div class="intl-emi-pill trigger-emi-calc" data-price="65000">
              ⚡ or ₹5,416/mo • 0% EMI Options &gt;
            </div>
            <div class="intl-card__actions">
              <a href="/products/riviera-accent-chair" class="btn btn-outline" style="padding:10px 14px;font-size:0.75rem;flex:1;text-align:center;">View Details</a>
              <a href="https://wa.me/919052751666?text=Hello+Grand+Living+Vizag%21+I+would+like+to+enquire+about+the+Riviera+Chair." target="_blank" rel="noopener" class="btn btn-champagne" style="padding:10px 14px;font-size:0.75rem;">WhatsApp</a>
            </div>
          </div>
        </div>
      </article>

      <!-- 5. German Precision Palisade Wardrobe -->
      <article class="intl-card" data-country="germany" data-id="5">
        <div class="intl-card__media">
          <img src="/uploads/products/palisade-walk-in-wardrobe.jpg" alt="Palisade Walk-in Wardrobe" loading="lazy">
          <span class="intl-card__flag-badge">🇩🇪 German Precision</span>
          <span class="intl-card__discount-pill">32% OFF</span>
          <button class="intl-card__wishlist-btn wishlist-toggle" data-product-id="5" aria-label="Save to Wishlist">♡</button>
        </div>
        <div class="intl-card__content">
          <span class="intl-card__meta">Anodized Aluminium &amp; Fluted Glass</span>
          <h3 class="intl-card__title">Palisade Architectural Wardrobe</h3>
          <p class="intl-card__desc">German soft-close track fittings with integrated motion sensor LED illumination and bespoke storage modules.</p>
          <div class="intl-pricing-box">
            <div class="intl-prices">
              <span class="intl-price-current">₹3,40,000</span>
              <span class="intl-price-mrp">₹5,00,000</span>
            </div>
            <div class="intl-emi-pill trigger-emi-calc" data-price="340000">
              ⚡ or ₹28,333/mo • 0% EMI Options &gt;
            </div>
            <div class="intl-card__actions">
              <a href="/products/palisade-walk-in-wardrobe" class="btn btn-outline" style="padding:10px 14px;font-size:0.75rem;flex:1;text-align:center;">View Details</a>
              <a href="https://wa.me/919052751666?text=Hello+Grand+Living+Vizag%21+I+would+like+to+enquire+about+the+Palisade+Wardrobe." target="_blank" rel="noopener" class="btn btn-champagne" style="padding:10px 14px;font-size:0.75rem;">WhatsApp</a>
            </div>
          </div>
        </div>
      </article>

      <!-- 6. Italian Live-Edge Vesper Table -->
      <article class="intl-card" data-country="italy" data-id="6">
        <div class="intl-card__media">
          <img src="/uploads/products/vesper-dining-table.jpg" alt="Vesper Dining Table" loading="lazy">
          <span class="intl-card__flag-badge">🇮🇹 Italian Modernist</span>
          <span class="intl-card__discount-pill">36% OFF</span>
          <button class="intl-card__wishlist-btn wishlist-toggle" data-product-id="6" aria-label="Save to Wishlist">♡</button>
        </div>
        <div class="intl-card__content">
          <span class="intl-card__meta">Single-Slab Live Edge &amp; Brass</span>
          <h3 class="intl-card__title">Vesper Sculptural Dining Table</h3>
          <p class="intl-card__desc">Single continuous natural slab seated on sculptural matte black architectural trestle legs.</p>
          <div class="intl-pricing-box">
            <div class="intl-prices">
              <span class="intl-price-current">₹1,85,000</span>
              <span class="intl-price-mrp">₹2,90,000</span>
            </div>
            <div class="intl-emi-pill trigger-emi-calc" data-price="185000">
              ⚡ or ₹15,416/mo • 0% EMI Options &gt;
            </div>
            <div class="intl-card__actions">
              <a href="/products/vesper-dining-table" class="btn btn-outline" style="padding:10px 14px;font-size:0.75rem;flex:1;text-align:center;">View Details</a>
              <a href="https://wa.me/919052751666?text=Hello+Grand+Living+Vizag%21+I+would+like+to+enquire+about+the+Vesper+Dining+Table." target="_blank" rel="noopener" class="btn btn-champagne" style="padding:10px 14px;font-size:0.75rem;">WhatsApp</a>
            </div>
          </div>
        </div>
      </article>
    </div>
  </div>
</section>

<!-- ============================================================
     ROYALOAK-INSPIRED "SHOP BY LOOK" / ROOM LOOKBOOK
     ============================================================ -->
<section class="shop-the-look-section" id="shop-the-look">
  <div class="container">
    <div style="display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:36px;">
      <div>
        <span style="font-family:'Manrope',sans-serif;font-size:0.75rem;letter-spacing:0.25em;text-transform:uppercase;color:#D5BE9A;display:block;margin-bottom:8px;font-weight:700;">Spatial Visualizer</span>
        <h2 style="font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem, 4vw, 3.2rem);color:#F5F0E8;font-weight:300;line-height:1.2;margin:0;">
          Shop By Look — Curated Rooms
        </h2>
      </div>
      <a href="/gallery" class="btn btn-outline" style="padding:12px 24px;">View Full Visual Archive &rarr;</a>
    </div>

    <div class="look-grid">
      <!-- Look 1 -->
      <div class="look-card">
        <img src="/uploads/gallery/showroom-living.jpg" alt="Living Room Suite Inspiration" loading="lazy">
        <div class="look-card__overlay">
          <span class="look-card__tag">Curated Living Suite</span>
          <h3 class="look-card__title">The Dwaraka Penthouse Suite</h3>
          <p class="look-card__sub">Complete set including Meridian Leather Sectional, Aurelius Table &amp; Accent Armchair.</p>
          <a href="/custom-request" class="look-card__btn">Request This Room Look &rarr;</a>
        </div>
      </div>

      <!-- Look 2 -->
      <div class="look-card">
        <img src="/uploads/gallery/showroom-bedroom.jpg" alt="Master Bedroom Collection" loading="lazy">
        <div class="look-card__overlay">
          <span class="look-card__tag">Presidential Bedroom</span>
          <h3 class="look-card__title">The Palm Beach Sanctuary</h3>
          <p class="look-card__sub">Palazzo Velvet King Bed paired with Nightfall bedside tables and Lumiere Wardrobe.</p>
          <a href="/custom-request" class="look-card__btn">Request This Room Look &rarr;</a>
        </div>
      </div>

      <!-- Look 3 -->
      <div class="look-card">
        <img src="/uploads/gallery/showroom-main.jpg" alt="Architectural Flagship Space" loading="lazy">
        <div class="look-card__overlay">
          <span class="look-card__tag">Flagship Display</span>
          <h3 class="look-card__title">The Coastal Atelier Salon</h3>
          <p class="look-card__sub">Live edge dining centerpiece accompanied by bespoke bar console and dining chairs.</p>
          <a href="/contact" class="look-card__btn">Tour This Setup In Showroom &rarr;</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     SECTION 4 — SIGNATURE SHOWCASE
     ============================================================ -->
<section class="signature-showcase" id="signature-showcase">
  <div class="container">
    <div class="signature-showcase__grid">

      <!-- Left: Feature Highlights -->
      <div class="signature-features" id="signatureFeatures">

        <div class="feature-item">
          <svg class="feature-item__icon" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
          <div>
            <h3 class="feature-item__title">Premium Materials</h3>
            <p class="feature-item__desc">Only the finest woods, fabrics, and metals, ethically sourced.</p>
          </div>
        </div>

        <div class="feature-item">
          <svg class="feature-item__icon" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
          <div>
            <h3 class="feature-item__title">Expert Craftsmanship</h3>
            <p class="feature-item__desc">Every joint, curve, and finish executed with mastery.</p>
          </div>
        </div>

        <div class="feature-item">
          <svg class="feature-item__icon" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 3h4v4H3zM3 10h4v4H3zM3 17h4v4H3zM10 3h11M10 12h11M10 21h11"/>
          </svg>
          <div>
            <h3 class="feature-item__title">Custom Sizing</h3>
            <p class="feature-item__desc">Furniture made to fit your exact space and vision.</p>
          </div>
        </div>

        <div class="feature-item">
          <svg class="feature-item__icon" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
          <div>
            <h3 class="feature-item__title">Timeless Design</h3>
            <p class="feature-item__desc">Aesthetic that transcends trends and stands the test of time.</p>
          </div>
        </div>

      </div><!-- /signature-features -->

      <!-- Right: Masterpiece 30 Years of Legacy Display -->
      <div class="signature-deco" id="signatureDeco" role="region" aria-label="30 Years of Legacy Showcase">
        
        <!-- Ambient Glowing Stardust & Aura -->
        <div class="signature-deco__glow" aria-hidden="true"></div>
        
        <!-- Kinetic Gyroscopic Orbital Rings -->
        <div class="signature-deco__gyro" aria-hidden="true">
          <div class="signature-deco__ring signature-deco__ring--outer">
            <span class="gyro-satellite gyro-satellite--1"></span>
            <span class="gyro-satellite gyro-satellite--2"></span>
          </div>
          <div class="signature-deco__ring signature-deco__ring--mid">
            <span class="gyro-satellite gyro-satellite--3"></span>
          </div>
          <div class="signature-deco__ring signature-deco__ring--inner"></div>
        </div>

        <!-- Floating Monumental 3D Background Shadow Watermark -->
        <span class="signature-deco__number" aria-hidden="true">30</span>

        <!-- Foreground Haute-Horlogerie Exhibition Medallion -->
        <div class="signature-deco__content">
          <!-- Heritage Seal Monogram -->
          <div class="signature-deco__seal">
            <svg width="34" height="34" viewBox="0 0 100 100" fill="none" class="signature-deco__crest-svg">
              <circle cx="50" cy="50" r="46" stroke="#D5BE9A" stroke-width="1.2" stroke-dasharray="3 3"/>
              <circle cx="50" cy="50" r="38" stroke="#D5BE9A" stroke-width="0.8" opacity="0.6"/>
              <path d="M64 34 C 56 28, 40 28, 34 38 C 28 47, 28 57, 34 65 C 40 73, 56 73, 64 66 L 64 50 L 48 50" stroke="#D5BE9A" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="signature-deco__seal-text">ESTD. 1994 &bull; VISAKHAPATNAM</span>
          </div>

          <!-- Monumental 30+ Number with Liquid Gold Sheen -->
          <div class="signature-deco__digits-wrap">
            <span class="signature-deco__year" id="decoYear"><?= htmlspecialchars($legacy_years ?? '30') ?></span>
            <sup class="signature-deco__plus">+</sup>
          </div>

          <!-- Animated Typography Lockup -->
          <div class="signature-deco__sub-lockup">
            <span class="signature-deco__sub"><?= htmlspecialchars($legacy_title ?? 'YEARS OF TIMELESS LEGACY') ?></span>
            <div class="signature-deco__divider"></div>
            <p class="signature-deco__tagline">
              <?= htmlspecialchars($legacy_tagline ?? 'Three Decades of Curating Extraordinary Living Spaces & Master Artistry') ?>
            </p>
          </div>

          <!-- Quality Hallmarks Pill Badges -->
          <div class="signature-deco__hallmarks">
            <span class="signature-deco__hallmark"><?= htmlspecialchars($legacy_hallmark_1 ?? '100% Solid Teak') ?></span>
            <span class="signature-deco__hallmark"><?= htmlspecialchars($legacy_hallmark_2 ?? 'Master Artisans') ?></span>
            <span class="signature-deco__hallmark"><?= htmlspecialchars($legacy_hallmark_3 ?? 'Handcrafted in AP') ?></span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ============================================================
     SECTION 4B — OUR CORE VALUES
     ============================================================ -->
<section class="our-values" id="our-values">
  <div class="container">
    <div class="section-header">
      <span class="section-header__label"><?= htmlspecialchars($values_label ?? 'What We Stand For') ?></span>
      <h2 class="section-header__title"><?= htmlspecialchars($values_title ?? 'Our Core Values') ?></h2>
      <p class="section-header__sub"><?= htmlspecialchars($values_sub ?? 'Four foundational pillars guiding three decades of master woodworking and architectural living.') ?></p>
    </div>

    <div class="values-grid">

      <!-- Value 1: Quality -->
      <div class="value-card" data-value="1">
        <div class="value-card__header">
          <div class="value-card__icon-wrap">
            <svg class="value-card__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M6 3h12l4 6-10 13L2 9z"/>
              <path d="M11 3L8 9l4 13 4-13-3-6"/>
              <path d="M2 9h20"/>
            </svg>
          </div>
          <span class="value-card__badge">01</span>
        </div>
        <h3 class="value-card__title"><?= htmlspecialchars($value_1_title ?? 'Quality') ?></h3>
        <p class="value-card__body"><?= htmlspecialchars($value_1_desc ?? 'We refuse to compromise on materials, finish, or detail. Every product leaves our workshop meeting the highest standards.') ?></p>
      </div>

      <!-- Value 2: Craftsmanship -->
      <div class="value-card" data-value="2">
        <div class="value-card__header">
          <div class="value-card__icon-wrap">
            <svg class="value-card__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
            </svg>
          </div>
          <span class="value-card__badge">02</span>
        </div>
        <h3 class="value-card__title"><?= htmlspecialchars($value_2_title ?? 'Craftsmanship') ?></h3>
        <p class="value-card__body"><?= htmlspecialchars($value_2_desc ?? 'Our artisans bring generations of woodworking knowledge to every piece. No two items are ever truly identical.') ?></p>
      </div>

      <!-- Value 3: Integrity -->
      <div class="value-card" data-value="3">
        <div class="value-card__header">
          <div class="value-card__icon-wrap">
            <svg class="value-card__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
          </div>
          <span class="value-card__badge">03</span>
        </div>
        <h3 class="value-card__title"><?= htmlspecialchars($value_3_title ?? 'Integrity') ?></h3>
        <p class="value-card__body"><?= htmlspecialchars($value_3_desc ?? 'We price fairly, deliver on time, and stand behind everything we sell. Your trust is our most valuable asset.') ?></p>
      </div>

      <!-- Value 4: Service -->
      <div class="value-card" data-value="4">
        <div class="value-card__header">
          <div class="value-card__icon-wrap">
            <svg class="value-card__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M20.42 4.58a5.4 5.4 0 00-7.65 0l-.77.78-.77-.78a5.4 5.4 0 00-7.65 0C1.46 6.7 1.33 10.28 4 13l8 8 8-8c2.67-2.72 2.54-6.3.42-8.42z"/>
              <path d="M12 5.36L8.87 8.5a2.13 2.13 0 003 3L12 11l1.13-1.5a2.13 2.13 0 003-3L13 5.36"/>
            </svg>
          </div>
          <span class="value-card__badge">04</span>
        </div>
        <h3 class="value-card__title"><?= htmlspecialchars($value_4_title ?? 'Service') ?></h3>
        <p class="value-card__body"><?= htmlspecialchars($value_4_desc ?? 'From your first visit to our showroom through years of after-sales care, we are committed to your satisfaction.') ?></p>
      </div>

    </div>
  </div>
</section>

<!-- ============================================================
     SECTION 5 — BRAND STATS
     ============================================================ -->
<!-- Numbers are illustrative; verify actual figures with client -->
<section class="brand-stats" id="brand-stats">
  <div class="container">
    <div class="brand-stats__row">

      <div class="stat-item">
        <span class="stat-item__number" data-target="<?= (int)($stat_1_num ?? 1000) ?>" data-suffix="<?= htmlspecialchars($stat_1_sfx ?? '+') ?>">0</span>
        <span class="stat-item__label"><?= htmlspecialchars($stat_1_lbl ?? 'Happy Families') ?></span>
      </div>

      <div class="stat-item">
        <span class="stat-item__number" data-target="<?= (int)($stat_2_num ?? 500) ?>" data-suffix="<?= htmlspecialchars($stat_2_sfx ?? '+') ?>">0</span>
        <span class="stat-item__label"><?= htmlspecialchars($stat_2_lbl ?? 'Products') ?></span>
      </div>

      <div class="stat-item">
        <span class="stat-item__number" data-target="<?= (int)($stat_3_num ?? 30) ?>" data-suffix="<?= htmlspecialchars($stat_3_sfx ?? '+') ?>">0</span>
        <span class="stat-item__label"><?= htmlspecialchars($stat_3_lbl ?? 'Years Experience') ?></span>
      </div>

      <div class="stat-item">
        <span class="stat-item__number" data-target="<?= (int)($stat_4_num ?? 100) ?>" data-suffix="<?= htmlspecialchars($stat_4_sfx ?? '%') ?>">0</span>
        <span class="stat-item__label"><?= htmlspecialchars($stat_4_lbl ?? 'Satisfaction') ?></span>
      </div>

    </div>
  </div>
</section>

<!-- ============================================================
     SECTION 6 — ROOM INSPIRATION
     ============================================================ -->
<section class="room-inspiration" id="room-inspiration">
  <div class="container">
    <div class="section-header">
      <span class="section-label">Room Inspiration</span>
      <h2 class="section-title">Spaces That Inspire</h2>
    </div>

    <div class="inspiration-grid">
      <?php
      $inspGrads = [
          'linear-gradient(180deg,#29221D 0%,#76543B 100%)',
          'linear-gradient(180deg,#3D2B1F 0%,#D5BE9A 100%)',
          'linear-gradient(180deg,#1E1A17 0%,#76543B 100%)',
          'linear-gradient(180deg,#29221D 0%,#B7AA98 100%)',
          'linear-gradient(180deg,#54392A 0%,#D5BE9A 100%)',
          'linear-gradient(180deg,#29221D 0%,#54392A 100%)',
      ];
      foreach ($inspiration as $idx => $item):
          $grad      = $inspGrads[$idx % count($inspGrads)];
          $itemTitle = sanitizeOutput($item['title']);
          $itemSlug  = sanitizeOutput($item['slug'] ?? '');
          $itemImg   = !empty($item['image']) ? sanitizeOutput($item['image']) : '';
          $itemImgSrc = $itemImg && $itemImg[0] === '/' ? $itemImg : ($itemImg ? '/uploads/gallery/' . $itemImg : '');
          $href      = $itemSlug ? '/inspiration/' . $itemSlug : '/inspiration';
      ?>
      <a href="<?= $href ?>" class="inspiration-card">
        <div class="inspiration-card__bg" style="background:<?= $grad ?>;"></div>
        <?php if ($itemImg): ?>
          <img class="inspiration-card__img"
               src="<?= $itemImgSrc ?>"
               alt="<?= $itemTitle ?>"
               loading="lazy">
        <?php endif; ?>
        <div class="inspiration-card__overlay"></div>
        <div class="inspiration-card__body">
          <h3 class="inspiration-card__title"><?= $itemTitle ?></h3>
          <span class="inspiration-card__link">View More &#8594;</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>



<!-- ============================================================
     SECTION 7 — FEATURED PRODUCTS
     ============================================================ -->
<section class="featured-products" id="featured-products">
  <div class="container">
    <div class="section-header">
      <span class="section-label">Featured Pieces</span>
      <h2 class="section-title">Crafted for Your Home</h2>
    </div>

    <div class="products-grid">
      <?php
      $prodGrads = [
          'linear-gradient(145deg,#29221D,#76543B)',
          'linear-gradient(145deg,#3D2B1F,#D5BE9A)',
          'linear-gradient(145deg,#1E1A17,#76543B)',
          'linear-gradient(145deg,#29221D,#B7AA98)',
          'linear-gradient(145deg,#54392A,#D5BE9A)',
          'linear-gradient(145deg,#29221D,#54392A)',
          'linear-gradient(145deg,#3D2B1F,#76543B)',
          'linear-gradient(145deg,#1E1A17,#D5BE9A)',
      ];
      foreach ($products as $idx => $product):
          $grad         = $prodGrads[$idx % count($prodGrads)];
          $prodName     = sanitizeOutput($product['name']);
          $prodSlug     = sanitizeOutput($product['slug'] ?? '');
          $prodCat      = sanitizeOutput($product['category_name'] ?? '');
          $prodDesc     = sanitizeOutput($product['short_description'] ?? $product['description'] ?? '');
          $prodImg      = !empty($product['primary_image'] ?? $product['image'] ?? '')
                            ? sanitizeOutput($product['primary_image'] ?? $product['image'])
                            : '';
          $waNum        = !empty($product['whatsapp_number'])
                            ? sanitizeOutput($product['whatsapp_number'])
                            : sanitizeOutput($whatsapp_default);
          $waText       = rawurlencode("Hello Grand Living, I am inquiring about " . $product['name']);
          $waLink       = "https://wa.me/{$waNum}?text={$waText}";
          $prodUrl      = "product.php?slug=" . urlencode($prodSlug);
      ?>
      <div class="product-card">
        <a href="<?= $prodUrl ?>" class="product-card__img-wrap" style="text-decoration:none;display:block;">
          <?php if ($prodImg): ?>
            <img class="product-card__img"
                 src="<?= $prodImg ?>"
                 alt="<?= $prodName ?>"
                 loading="lazy">
          <?php else: ?>
            <div class="product-card__placeholder" style="background:<?= $grad ?>;"></div>
          <?php endif; ?>
          <?php if ($prodCat): ?>
            <span class="product-card__badge"><?= $prodCat ?></span>
          <?php endif; ?>
        </a>
        <div class="product-card__body">
          <h3 class="product-card__name">
            <a href="<?= $prodUrl ?>" style="color:inherit;text-decoration:none;"><?= $prodName ?></a>
          </h3>
          <?php if ($prodDesc): ?>
            <p class="product-card__desc"><?= $prodDesc ?></p>
          <?php endif; ?>
          <div style="display:flex;gap:8px;align-items:center;margin-top:auto;width:100%;">
            <a href="<?= $prodUrl ?>" class="btn btn-outline" style="padding:10px 14px;font-size:0.75rem;flex:1;text-align:center;">
              View Piece
            </a>
            <a href="<?= $waLink ?>" class="product-card__btn" target="_blank" rel="noopener noreferrer" style="flex:1;text-align:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
              <path d="M12 0C5.374 0 0 5.373 0 12c0 2.113.554 4.121 1.523 5.868L.057 23.887l6.187-1.44A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.822 9.822 0 01-5.009-1.368l-.36-.214-3.724.868.9-3.63-.234-.373A9.806 9.806 0 012.182 12C2.182 6.564 6.563 2.182 12 2.182S21.818 6.564 21.818 12 17.437 21.818 12 21.818z"/>
            </svg>
            Enquire
          </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============================================================
     SECTION 7B — CUSTOMER EXPERIENCES & VERIFIED REVIEWS
     ============================================================ -->
<?php if (!empty($homeTestimonials)): ?>
<section class="testimonials-section" id="testimonials" style="background:#171614;padding:100px 0;border-top:1px solid rgba(213,190,154,0.12);border-bottom:1px solid rgba(213,190,154,0.12);">
  <div class="container">
    <div class="section-header" style="text-align:center;margin-bottom:60px;">
      <span class="section-label" style="font-family:'Manrope',sans-serif;font-size:0.72rem;letter-spacing:0.28em;color:#D5BE9A;text-transform:uppercase;font-weight:700;display:block;margin-bottom:12px;">Client Testimonials</span>
      <h2 class="section-title" style="font-family:'Cormorant Garamond',serif;font-size:clamp(2.4rem, 4vw, 3.4rem);color:#F5F0E8;font-weight:300;">Voices of Distinction</h2>
      <p style="font-family:'Manrope',sans-serif;font-size:0.95rem;color:#B7AA98;max-width:560px;margin:12px auto 0;line-height:1.7;">Reflections from homeowners across Visakhapatnam who chose the art of living beautifully.</p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:24px;">
      <?php foreach ($homeTestimonials as $t): ?>
        <div class="testimonial-card" style="background:linear-gradient(165deg, rgba(41,34,29,0.85) 0%, rgba(23,22,20,0.95) 100%);border:1px solid rgba(213,190,154,0.18);border-top:2px solid #D5BE9A;padding:32px 28px;border-radius:3px;display:flex;flex-direction:column;justify-content:space-between;gap:18px;">
          <div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
              <span style="color:#D5BE9A;font-size:1.05rem;letter-spacing:2px;">
                <?= str_repeat('★', (int)$t['rating']) . str_repeat('☆', 5 - (int)$t['rating']) ?>
              </span>
              <?php if ($t['is_verified']): ?>
                <span style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.12em;background:rgba(213,190,154,0.12);color:#D5BE9A;border:1px solid rgba(213,190,154,0.3);padding:3px 8px;border-radius:2px;">
                  ✓ Verified Homeowner
                </span>
              <?php endif; ?>
            </div>
            <p style="color:#D2C8BC;font-size:0.95rem;line-height:1.75;margin:0;font-style:italic;">
              &ldquo;<?= htmlspecialchars($t['review']) ?>&rdquo;
            </p>
          </div>
          <div style="border-top:1px solid rgba(213,190,154,0.12);padding-top:14px;">
            <strong style="color:#F5F0E8;font-family:'Cormorant Garamond',serif;font-size:1.25rem;display:block;"><?= htmlspecialchars($t['name']) ?></strong>
            <span style="color:#D5BE9A;font-size:0.75rem;letter-spacing:0.08em;"><?= htmlspecialchars($t['location']) ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============================================================
     SECTION 8 — SHOWROOM EXPERIENCE
     ============================================================ -->
<section class="showroom-section" id="showroom">
  <div class="container">
    <div class="showroom-section__grid">

      <!-- Left Info -->
      <div class="showroom-info">
        <span class="showroom-info__label">Visit Us</span>
        <h2 class="showroom-info__title">Visit Our Showroom</h2>

        <div class="showroom-info__detail">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
          <p><?= sanitizeOutput($showroom_address) ?></p>
        </div>

        <div class="showroom-info__detail">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
          <p>Mon–Sat: 10:00 AM – 8:00 PM &nbsp;|&nbsp; Sun: 11:00 AM – 6:00 PM</p>
        </div>

        <div class="showroom-info__ctas">
          <a href="tel:<?= sanitizeOutput($phone_number) ?>" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
              <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
            </svg>
            Call Us
          </a>
          <a href="https://wa.me/<?= sanitizeOutput($whatsapp_default) ?>" class="btn btn-ghost-champ" target="_blank" rel="noopener noreferrer">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.374 0 0 5.373 0 12c0 2.113.554 4.121 1.523 5.868L.057 23.887l6.187-1.44A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.822 9.822 0 01-5.009-1.368l-.36-.214-3.724.868.9-3.63-.234-.373A9.806 9.806 0 012.182 12C2.182 6.564 6.563 2.182 12 2.182S21.818 6.564 21.818 12 17.437 21.818 12 21.818z"/>
            </svg>
            WhatsApp
          </a>
          <a href="<?= sanitizeOutput($maps_link) ?>" class="btn btn-primary" target="_blank" rel="noopener noreferrer" style="background:#D5BE9A;color:#171614;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            Open in Google Maps
          </a>
        </div>
      </div><!-- /showroom-info -->

      <div class="showroom-scene" id="showroomScene" aria-label="Interactive three-dimensional Grand Living showroom">
        <div class="showroom-scene__fallback" aria-hidden="true">GRAND LIVING <span>VISAKHAPATNAM SHOWROOM</span></div>
        <div class="showroom-scene__caption" aria-hidden="true">
          <span class="showroom-scene__eyebrow">GRAND LIVING • VIZAG</span>
          <span class="showroom-scene__caption-title">A room made to be lived in.</span>
        </div>
        <span class="showroom-scene__hint" aria-hidden="true">DRAG TO LOOK AROUND</span>
      </div>

    </div>
  </div>
</section>

<script type="module" src="/assets/js/showroom-3d.js"></script>

<!-- ============================================================
     SECTION 9 — FINAL CTA
     ============================================================ -->
<section class="final-cta" id="final-cta">
  <div class="container">

    <span class="final-cta__deco-line" id="ctaLine1"></span>

    <h2 class="final-cta__headline">
      <span>LET'S CREATE</span>
      <span class="accent">A SPACE YOU LOVE.</span>
    </h2>

    <p class="final-cta__subtitle">
      Visit our showroom or explore our catalogue to begin your journey.
    </p>

    <div class="final-cta__ctas">
      <a href="/collection" class="btn btn-primary">Explore Collection</a>
      <a href="/contact"    class="btn btn-ghost">Contact Our Team</a>
    </div>

    <span class="final-cta__deco-line final-cta__deco-line--bottom" id="ctaLine2"></span>

  </div>
</section>

<!-- ============================================================
     GSAP ANIMATIONS
     ============================================================ -->
<script>
(function () {
  'use strict';

  /* ── Wait for GSAP + ST to be ready ───────────────────────────── */
  function initAnimations() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
      setTimeout(initAnimations, 100);
      return;
    }

    gsap.registerPlugin(ScrollTrigger);

    /* ── Smooth scroll for anchor links ─────────────────────────── */
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
      anchor.addEventListener('click', function (e) {
        var target = document.querySelector(this.getAttribute('href'));
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });

    /* ── HERO — Deco lines ───────────────────────────────────────── */
    gsap.fromTo('#heroDeco1', { scaleX: 0, transformOrigin: 'left' }, {
      scaleX: 1, duration: 1.2, ease: 'expo.out', delay: 0.2
    });
    gsap.fromTo('#heroDeco2', { scaleX: 0, transformOrigin: 'right' }, {
      scaleX: 1, duration: 1.2, ease: 'expo.out', delay: 0.4
    });

    /* ── HERO — Headline (SplitType if available, else fallback) ── */
    var heroHeadlineEl = document.getElementById('heroHeadline');
    if (heroHeadlineEl) {
      if (typeof SplitType !== 'undefined') {
        var split = new SplitType('#heroHeadline', { types: 'chars,words' });
        gsap.fromTo(split.chars,
          { y: 80, opacity: 0 },
          {
            y: 0, opacity: 1,
            duration: 0.9, ease: 'power3.out',
            stagger: 0.03, delay: 0.5
          }
        );
      } else {
        gsap.fromTo('#heroHeadline',
          { y: 60, opacity: 0 },
          { y: 0, opacity: 1, duration: 1.2, ease: 'power3.out', delay: 0.5 }
        );
      }
    }

    /* ── HERO — Subtitle & CTAs ──────────────────────────────────── */
    gsap.fromTo('#heroSubtitle',
      { y: 30, opacity: 0 },
      { y: 0, opacity: 1, duration: 1, ease: 'power2.out', delay: 1.3 }
    );
    gsap.fromTo('#heroCtas',
      { y: 25, opacity: 0 },
      { y: 0, opacity: 1, duration: 0.9, ease: 'power2.out', delay: 1.6 }
    );
    gsap.fromTo('#heroScroll',
      { opacity: 0 },
      { opacity: 0.7, duration: 1, delay: 2.2, ease: 'power1.in' }
    );

    /* ── BRAND INTRO — Counter ───────────────────────────────────── */
    var brandCounterEl = document.getElementById('brandCounter');
    if (brandCounterEl) {
      ScrollTrigger.create({
        trigger: '#brand-intro',
        start: 'top 80%',
        once: true,
        onEnter: function () {
          var obj = { val: 0 };
          gsap.to(obj, {
            val: 30,
            duration: 2.5,
            ease: 'power2.out',
            onUpdate: function () {
              brandCounterEl.textContent = Math.floor(obj.val) + '+';
            }
          });
        }
      });

      gsap.fromTo('.brand-intro__left',
        { x: -60, opacity: 0 },
        { x: 0, opacity: 1, duration: 1.2, ease: 'power3.out',
          scrollTrigger: { trigger: '#brand-intro', start: 'top 80%' } }
      );
      gsap.fromTo('.brand-intro__right',
        { x: 60, opacity: 0 },
        { x: 0, opacity: 1, duration: 1.2, ease: 'power3.out', delay: 0.2,
          scrollTrigger: { trigger: '#brand-intro', start: 'top 80%' } }
      );
    }

    /* ── COLLECTIONS — Cards stagger in ─────────────────────────── */
    gsap.fromTo('.collection-card',
      { y: 50, opacity: 0 },
      {
        y: 0, opacity: 1, duration: 0.9, ease: 'power3.out', stagger: 0.12,
        scrollTrigger: { trigger: '.collections__grid', start: 'top 80%' }
      }
    );

    /* ── SIGNATURE SHOWCASE — Feature items ─────────────────────── */
    var featureItems = document.querySelectorAll('.feature-item');
    if (featureItems.length) {
      gsap.fromTo(featureItems,
        { x: -40, opacity: 0 },
        {
          x: 0, opacity: 1, duration: 0.9, ease: 'power3.out', stagger: 0.18,
          scrollTrigger: { trigger: '#signatureFeatures', start: 'top 80%' }
        }
      );
    }

    /* ── SIGNATURE SHOWCASE — 30 Years of Legacy Animation ───────── */
    var signatureDecoEl = document.getElementById('signatureDeco');
    var decoYearEl = document.getElementById('decoYear');

    if (signatureDecoEl) {
      gsap.fromTo(signatureDecoEl,
        { scale: 0.88, opacity: 0, y: 40 },
        {
          scale: 1, opacity: 1, y: 0, duration: 1.4, ease: 'expo.out',
          scrollTrigger: { trigger: '#signatureDeco', start: 'top 80%' }
        }
      );

      // Live Counter Animation for 30 Years
      if (decoYearEl) {
        ScrollTrigger.create({
          trigger: '#signatureDeco',
          start: 'top 80%',
          once: true,
          onEnter: function () {
            var counterObj = { count: 0 };
            gsap.to(counterObj, {
              count: 30,
              duration: 2.2,
              ease: 'power3.out',
              onUpdate: function () {
                var currentVal = Math.floor(counterObj.count);
                decoYearEl.textContent = (currentVal < 10 ? '0' : '') + currentVal;
              }
            });
          }
        });
      }

      // Interactive 3D Cursor Tilt Parallax
      if (window.matchMedia('(hover: hover)').matches) {
        signatureDecoEl.addEventListener('mousemove', function (e) {
          var rect = signatureDecoEl.getBoundingClientRect();
          var normX = (e.clientX - rect.left) / rect.width - 0.5;
          var normY = (e.clientY - rect.top) / rect.height - 0.5;
          gsap.to(signatureDecoEl, {
            rotationY: normX * 12,
            rotationX: -normY * 12,
            ease: 'power1.out',
            duration: 0.35,
            transformPerspective: 1000
          });
        });
        signatureDecoEl.addEventListener('mouseleave', function () {
          gsap.to(signatureDecoEl, {
            rotationY: 0,
            rotationX: 0,
            ease: 'power2.out',
            duration: 0.7
          });
        });
      }
    }

    /* ── OUR CORE VALUES — Stagger in & 3D Tilt ─────────────────── */
    var homeValueCards = document.querySelectorAll('#our-values .value-card');
    if (homeValueCards.length) {
      gsap.from(homeValueCards, {
        scrollTrigger: {
          trigger: '#our-values',
          start: 'top 80%',
          once: true
        },
        y: 50,
        opacity: 0,
        duration: 0.9,
        ease: 'power3.out',
        stagger: 0.12
      });

      if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        homeValueCards.forEach(function (card) {
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

    /* ── STATS — Count-up ────────────────────────────────────────── */
    var statEls = document.querySelectorAll('.stat-item__number');
    statEls.forEach(function (el) {
      var target = parseInt(el.dataset.target, 10) || 0;
      var suffix = el.dataset.suffix || '';
      ScrollTrigger.create({
        trigger: el,
        start: 'top 80%',
        once: true,
        onEnter: function () {
          var obj = { val: 0 };
          gsap.to(obj, {
            val: target,
            duration: 2.5,
            ease: 'power2.out',
            onUpdate: function () {
              el.textContent = Math.floor(obj.val) + suffix;
            }
          });
        }
      });
    });

    /* ── INSPIRATION — Cards ─────────────────────────────────────── */
    gsap.fromTo('.inspiration-card',
      { y: 50, opacity: 0 },
      {
        y: 0, opacity: 1, duration: 0.9, ease: 'power3.out', stagger: 0.12,
        scrollTrigger: { trigger: '.inspiration-grid', start: 'top 80%' }
      }
    );

    /* ── PRODUCTS — Cards ────────────────────────────────────────── */
    gsap.fromTo('.product-card',
      { y: 50, opacity: 0 },
      {
        y: 0, opacity: 1, duration: 0.8, ease: 'power3.out', stagger: 0.1,
        scrollTrigger: { trigger: '.products-grid', start: 'top 80%' }
      }
    );

    /* ── FINAL CTA — Deco lines animate width ────────────────────── */
    ['#ctaLine1', '#ctaLine2'].forEach(function (sel, i) {
      var el = document.querySelector(sel);
      if (!el) return;
      gsap.fromTo(el,
        { width: 0 },
        {
          width: '120px', duration: 1.4, ease: 'expo.out', delay: i * 0.2,
          scrollTrigger: { trigger: '#final-cta', start: 'top 80%' }
        }
      );
    });

    /* ── FINAL CTA — Headline ────────────────────────────────────── */
    gsap.fromTo('.final-cta__headline',
      { y: 50, opacity: 0 },
      {
        y: 0, opacity: 1, duration: 1.2, ease: 'power3.out', delay: 0.3,
        scrollTrigger: { trigger: '#final-cta', start: 'top 80%' }
      }
    );
    gsap.fromTo('.final-cta__subtitle, .final-cta__ctas',
      { y: 30, opacity: 0 },
      {
        y: 0, opacity: 1, duration: 1, ease: 'power2.out', stagger: 0.2, delay: 0.6,
        scrollTrigger: { trigger: '#final-cta', start: 'top 80%' }
      }
    );

    /* ── Section headers ─────────────────────────────────────────── */
    gsap.utils.toArray('.section-header').forEach(function (hdr) {
      gsap.fromTo(hdr,
        { y: 40, opacity: 0 },
        {
          y: 0, opacity: 1, duration: 1, ease: 'power3.out',
          scrollTrigger: { trigger: hdr, start: 'top 85%' }
        }
      );
    });

  } /* /initAnimations */

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAnimations);
  } else {
    initAnimations();
  }

})();
</script>

<!-- Interactive FinTech EMI Calculator Modal (Royaloak Style) -->
<div class="emi-modal-backdrop" id="emiModalBackdrop" aria-hidden="true">
  <div class="emi-modal-card">
    <button class="emi-close-btn" id="emiModalClose" aria-label="Close EMI Calculator">&times;</button>
    <span style="font-size:0.75rem;letter-spacing:0.2em;text-transform:uppercase;color:#D5BE9A;font-weight:700;display:block;margin-bottom:8px;">Instant FinTech Calculator</span>
    <h3 style="font-family:'Cormorant Garamond',serif;font-size:2rem;color:#F5F0E8;font-weight:400;margin-bottom:6px;">No-Cost Furniture EMI</h3>
    <p style="font-size:0.84rem;color:#B7AA98;line-height:1.5;margin-bottom:24px;">Split your luxury furniture investment into easy interest-free monthly installments.</p>

    <div style="background:#141210;padding:20px;border-radius:4px;border:1px solid rgba(213,190,154,0.18);margin-bottom:20px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <span style="font-size:0.8rem;color:#B7AA98;">Piece Value:</span>
        <strong style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;color:#D5BE9A;" id="emiModalAmount">₹1,50,000</strong>
      </div>

      <div style="margin-bottom:14px;">
        <label style="display:block;font-size:0.72rem;letter-spacing:0.1em;text-transform:uppercase;color:#D5BE9A;margin-bottom:6px;">Select Tenure Plan:</label>
        <select id="emiTenureSelect" style="width:100%;background:#1C1916;border:1px solid rgba(213,190,154,0.3);color:#F5F0E8;padding:10px 14px;border-radius:2px;font-size:0.9rem;outline:none;cursor:pointer;">
          <option value="3">3 Months No-Cost EMI (0% Interest)</option>
          <option value="6" selected>6 Months No-Cost EMI (0% Interest)</option>
          <option value="9">9 Months Low-Cost EMI</option>
          <option value="12">12 Months Easy Installments</option>
        </select>
      </div>

      <div style="padding-top:14px;border-top:1px dashed rgba(213,190,154,0.2);display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:0.82rem;color:#F5F0E8;font-weight:600;">Estimated Monthly EMI:</span>
        <span style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;color:#4EBE7B;font-weight:700;" id="emiMonthlyOutput">₹25,000 / month</span>
      </div>
    </div>

    <div style="display:flex;gap:10px;">
      <a href="https://wa.me/919052751666?text=Hello+Grand+Living%21+I+am+interested+in+the+0%25+No-Cost+EMI+options+for+my+furniture+order." target="_blank" rel="noopener" class="btn btn-primary" style="flex:1;text-align:center;padding:12px;">
        Avail EMI On WhatsApp &rarr;
      </a>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
