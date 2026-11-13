-- ============================================================
-- Grand Furniture Vizag - Complete Database Schema & Seed Data
-- MySQL 5.7+ / MariaDB 10.3+
-- ============================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET time_zone = '+05:30';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO,STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- ============================================================
-- DROP TABLES (order matters for FK constraints)
-- ============================================================
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `enquiries`;
DROP TABLE IF EXISTS `custom_requests`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `gallery`;
DROP TABLE IF EXISTS `testimonials`;
DROP TABLE IF EXISTS `homepage_sections`;
DROP TABLE IF EXISTS `website_settings`;
DROP TABLE IF EXISTS `admins`;

-- ============================================================
-- TABLE: admins
-- ============================================================
CREATE TABLE `admins` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`     VARCHAR(80) NOT NULL,
    `email`        VARCHAR(180) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `role`         ENUM('super_admin','admin','editor') NOT NULL DEFAULT 'admin',
    `is_active`    TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_login`   DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_admins_username` (`username`),
    UNIQUE KEY `uq_admins_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: categories
-- ============================================================
CREATE TABLE `categories` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(120) NOT NULL,
    `slug`        VARCHAR(140) NOT NULL,
    `description` TEXT,
    `image`       VARCHAR(255) DEFAULT NULL,
    `sort_order`  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categories_slug` (`slug`),
    KEY `idx_categories_sort`     (`sort_order`),
    KEY `idx_categories_active`   (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: products
-- ============================================================
CREATE TABLE `products` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id`       INT UNSIGNED NOT NULL,
    `name`              VARCHAR(200) NOT NULL,
    `slug`              VARCHAR(220) NOT NULL,
    `sku`               VARCHAR(60) DEFAULT NULL,
    `description`       LONGTEXT,
    `short_description` TEXT,
    `price_range`       VARCHAR(80) DEFAULT NULL COMMENT 'e.g. ₹1,20,000 – ₹1,80,000',
    `dimensions`        VARCHAR(255) DEFAULT NULL,
    `materials`         VARCHAR(255) DEFAULT NULL,
    `colors`            VARCHAR(255) DEFAULT NULL,
    `finishes`          VARCHAR(255) DEFAULT NULL,
    `availability`      ENUM('in-stock','made-to-order','out-of-stock') NOT NULL DEFAULT 'made-to-order',
    `is_featured`       TINYINT(1) NOT NULL DEFAULT 0,
    `is_active`         TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order`        SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `meta_title`        VARCHAR(160) DEFAULT NULL,
    `meta_description`  VARCHAR(320) DEFAULT NULL,
    `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_products_slug` (`slug`),
    UNIQUE KEY `uq_products_sku`  (`sku`),
    KEY `idx_products_category`   (`category_id`),
    KEY `idx_products_featured`   (`is_featured`),
    KEY `idx_products_active`     (`is_active`),
    KEY `idx_products_sort`       (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: product_images
-- ============================================================
CREATE TABLE `product_images` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` INT UNSIGNED NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `alt_text`   VARCHAR(200) DEFAULT NULL,
    `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_pi_product`  (`product_id`),
    KEY `idx_pi_primary`  (`product_id`, `is_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: enquiries
-- ============================================================
CREATE TABLE `enquiries` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`         VARCHAR(120) NOT NULL,
    `phone`        VARCHAR(20) NOT NULL,
    `email`        VARCHAR(180) DEFAULT NULL,
    `subject`      VARCHAR(200) DEFAULT NULL,
    `message`      TEXT NOT NULL,
    `product_id`   INT UNSIGNED DEFAULT NULL,
    `product_name` VARCHAR(200) DEFAULT NULL,
    `status`       ENUM('pending','in-progress','resolved') NOT NULL DEFAULT 'pending',
    `admin_notes`  TEXT DEFAULT NULL,
    `ip_address`   VARCHAR(45) DEFAULT NULL,
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_enquiries_status`     (`status`),
    KEY `idx_enquiries_product`    (`product_id`),
    KEY `idx_enquiries_created`    (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: custom_requests
-- ============================================================
CREATE TABLE `custom_requests` (
    `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`               VARCHAR(120) NOT NULL,
    `phone`              VARCHAR(20) NOT NULL,
    `email`              VARCHAR(180) DEFAULT NULL,
    `category`           VARCHAR(120) DEFAULT NULL,
    `dimensions`         VARCHAR(255) DEFAULT NULL,
    `material_preference` VARCHAR(255) DEFAULT NULL,
    `budget_range`       VARCHAR(100) DEFAULT NULL,
    `description`        TEXT NOT NULL,
    `reference_image`    VARCHAR(255) DEFAULT NULL,
    `status`             ENUM('pending','reviewing','quoted','in-production','completed','cancelled') NOT NULL DEFAULT 'pending',
    `admin_notes`        TEXT DEFAULT NULL,
    `created_at`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_cr_status`    (`status`),
    KEY `idx_cr_created`   (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: gallery
-- ============================================================
CREATE TABLE `gallery` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`      VARCHAR(200) DEFAULT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `category`   ENUM('showroom','furniture','room-inspiration') NOT NULL DEFAULT 'furniture',
    `alt_text`   VARCHAR(200) DEFAULT NULL,
    `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_gallery_category` (`category`),
    KEY `idx_gallery_active`   (`is_active`),
    KEY `idx_gallery_sort`     (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: testimonials
-- ============================================================
CREATE TABLE `testimonials` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(120) NOT NULL,
    `location`    VARCHAR(120) DEFAULT NULL,
    `rating`      TINYINT UNSIGNED NOT NULL DEFAULT 5,
    `review`      TEXT NOT NULL,
    `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
    `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_testimonials_active`   (`is_active`),
    KEY `idx_testimonials_verified` (`is_verified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: homepage_sections
-- ============================================================
CREATE TABLE `homepage_sections` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `section_key` VARCHAR(80) NOT NULL,
    `title`       VARCHAR(200) DEFAULT NULL,
    `subtitle`    VARCHAR(200) DEFAULT NULL,
    `content`     LONGTEXT,
    `image`       VARCHAR(255) DEFAULT NULL,
    `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order`  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_hp_key` (`section_key`),
    KEY `idx_hp_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: website_settings
-- ============================================================
CREATE TABLE `website_settings` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `setting_key`   VARCHAR(100) NOT NULL,
    `setting_value` LONGTEXT DEFAULT NULL,
    `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: activity_logs
-- ============================================================
CREATE TABLE `activity_logs` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `admin_id`   INT UNSIGNED DEFAULT NULL,
    `action`     VARCHAR(200) NOT NULL,
    `details`    TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_al_admin`   (`admin_id`),
    KEY `idx_al_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- FOREIGN KEY CONSTRAINTS
-- ============================================================
ALTER TABLE `products`
    ADD CONSTRAINT `fk_products_category`
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `product_images`
    ADD CONSTRAINT `fk_pi_product`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE `activity_logs`
    ADD CONSTRAINT `fk_al_admin`
        FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`)
        ON UPDATE CASCADE ON DELETE SET NULL;

SET foreign_key_checks = 1;

-- ============================================================
-- SEED DATA: admins
-- Password: Admin@2024  (bcrypt hash, cost 12)
-- ============================================================
INSERT INTO `admins` (`username`, `email`, `password_hash`, `role`, `is_active`, `created_at`) VALUES
('admin', 'admin@grandfurniturevizag.com',
 '$2y$12$YfNRGaBXiWBqtxHuNRO4BuZ4R5gHmf5CuRW7PqkVYvFd2KgqJVnEi',
 'super_admin', 1, NOW());

-- ============================================================
-- SEED DATA: categories
-- ============================================================
INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`, `is_active`) VALUES
('Living Room',    'living-room',    'Exquisitely crafted sofas, coffee tables, entertainment units, and accent chairs that define sophisticated living spaces.',    1, 1),
('Bedroom',        'bedroom',        'Luxurious beds, wardrobes, dressing tables, and nightstands that transform your bedroom into a personal sanctuary.',            2, 1),
('Dining Room',    'dining-room',    'Elegant dining tables, chairs, and sideboards crafted for memorable dining experiences.',                                       3, 1),
('Home Office',    'home-office',    'Refined desks, executive chairs, bookshelves, and storage solutions for the discerning professional.',                         4, 1),
('Outdoor',        'outdoor',        'Weather-resistant luxury outdoor furniture for patios, gardens, and balconies.',                                               5, 1),
('Wardrobes',      'wardrobes',      'Custom and ready wardrobes with premium fittings, designed to maximize space without compromising on aesthetics.',             6, 1),
('Accent & Decor', 'accent-decor',   'Statement side tables, bar cabinets, console tables, and decorative storage that complete any interior.',                     7, 1),
('Kids & Youth',   'kids-youth',     'Safe, durable, and beautifully designed furniture for children''s bedrooms and study areas.',                                 8, 1);

UPDATE `categories` SET `image` = CASE `slug`
    WHEN 'living-room' THEN '/uploads/products/meridian-sectional-sofa.jpg'
    WHEN 'bedroom' THEN '/uploads/products/palazzo-king-bed.jpg'
    WHEN 'dining-room' THEN '/uploads/products/vesper-dining-table.jpg'
    WHEN 'home-office' THEN '/uploads/products/stratton-executive-desk.jpg'
    WHEN 'outdoor' THEN '/uploads/products/azure-outdoor-lounge-set.jpg'
    WHEN 'wardrobes' THEN '/uploads/products/palisade-walk-in-wardrobe.jpg'
    WHEN 'accent-decor' THEN '/uploads/products/solstice-bar-cabinet.jpg'
    WHEN 'kids-youth' THEN '/uploads/products/dreamscape-kids-bed.jpg'
END
WHERE `slug` IN ('living-room', 'bedroom', 'dining-room', 'home-office', 'outdoor', 'wardrobes', 'accent-decor', 'kids-youth');

-- ============================================================
-- SEED DATA: products (16 products)
-- ============================================================
INSERT INTO `products`
    (`category_id`, `name`, `slug`, `sku`, `short_description`, `description`,
     `price_range`, `dimensions`, `materials`, `colors`, `finishes`,
     `availability`, `is_featured`, `is_active`, `sort_order`,
     `meta_title`, `meta_description`)
VALUES
-- Living Room (category_id=1)
(1, 'Meridian Sectional Sofa',        'meridian-sectional-sofa',
 'GF-LR-001',
 'A sweeping L-shaped sectional upholstered in full-grain Italian leather with solid teak frame.',
 'The Meridian Sectional Sofa is the cornerstone of refined living. Crafted with a hand-finished solid teak hardwood frame and filled with high-resilience foam topped by premium goose-down cushions, it promises decades of comfort. Upholstered in Grade-A full-grain Italian leather, every curve and stitch is a testament to our artisans'' mastery. Available in custom configurations.',
 '₹2,80,000 – ₹4,50,000', 'W 320 × D 230 × H 85 cm (customisable)', 'Solid Teak, Full-Grain Italian Leather, HR Foam, Goose Down',
 'Cognac, Ivory, Charcoal, Midnight Navy', 'Hand-stitched leather, Matte teak oil finish',
 'made-to-order', 1, 1, 1,
 'Meridian Sectional Sofa – Luxury Leather Sofas Vizag | Grand Furniture',
 'Buy the Meridian Sectional Sofa in Visakhapatnam. Full-grain Italian leather, solid teak frame. Custom configurations available. Grand Furniture Vizag.'),

(1, 'Aurelius Coffee Table',           'aurelius-coffee-table',
 'GF-LR-002',
 'Hand-carved marble-top coffee table with a brushed brass base — a sculptural centrepiece.',
 'The Aurelius Coffee Table merges the timeless beauty of Italian Calacatta marble with the warmth of hand-forged brushed brass. Each marble slab is hand-selected for its unique veining. The apron is carved from solid mango wood, creating a tripartite base that is as structurally sound as it is visually arresting.',
 '₹85,000 – ₹1,40,000', 'Ø 120 cm × H 42 cm | Rectangular: W 140 × D 70 × H 42 cm', 'Calacatta Marble, Mango Wood, Brass',
 'White/Gold Veining, Green Marble, Black Marble', 'Honed marble, Brushed brass, Lacquered wood',
 'in-stock', 1, 1, 2,
 'Aurelius Marble Coffee Table – Luxury Centre Tables Vizag | Grand Furniture',
 'Hand-carved Calacatta marble coffee table with brushed brass base. Available in Visakhapatnam at Grand Furniture Vizag.'),

(1, 'Riviera Accent Chair',            'riviera-accent-chair',
 'GF-LR-003',
 'Barrel-back accent chair in boucle fabric with solid walnut legs — sculptural and supremely comfortable.',
 'Inspired by mid-century French Riviera interiors, the Riviera Accent Chair features a generous barrel-back silhouette upholstered in premium boucle fabric. The solid American walnut legs are hand-turned on a traditional lathe. A perfect reading chair or statement piece.',
 '₹55,000 – ₹80,000', 'W 82 × D 78 × H 90 cm, Seat H 44 cm', 'Solid American Walnut, Boucle Fabric, HR Foam',
 'Cream, Sage Green, Dusty Rose, Charcoal', 'Natural walnut oil, Bouclé weave',
 'made-to-order', 1, 1, 3,
 'Riviera Barrel Accent Chair – Luxury Chairs Vizag | Grand Furniture',
 'Barrel-back accent chair in premium boucle fabric with solid walnut legs. Made to order at Grand Furniture Vizag.'),

-- Bedroom (category_id=2)
(2, 'Palazzo King Bed',               'palazzo-king-bed',
 'GF-BD-001',
 'An upholstered platform bed with a dramatic high headboard in hand-quilted velvet and solid sheesham legs.',
 'The Palazzo King Bed makes the ultimate statement in bedroom luxury. The dramatically tall headboard (140 cm) is hand-quilted in a diamond pattern using premium velvet fabric over high-density foam. The solid sheesham wood platform base requires no box spring and provides excellent mattress support. Available in King (183 × 200 cm) and Super King (200 × 200 cm).',
 '₹1,50,000 – ₹2,20,000', 'W 203 × D 220 × H 145 cm (King) | Customisable', 'Solid Sheesham Wood, Velvet Fabric, HD Foam',
 'Royal Blue, Dusty Pink, Forest Green, Charcoal', 'Hand-quilted velvet, Lacquered sheesham',
 'made-to-order', 1, 1, 1,
 'Palazzo King Bed – Luxury Upholstered Beds Vizag | Grand Furniture',
 'Hand-quilted velvet king bed with solid sheesham wood base. Made to order. Grand Furniture Vizag.'),

(2, 'Lumiere Wardrobe',               'lumiere-wardrobe',
 'GF-BD-002',
 'Six-door sliding wardrobe with mirrored panels, soft-close mechanisms, and modular interior fittings.',
 'The Lumiere Wardrobe is an exercise in seamless organisation and refined aesthetics. Six flush-panel doors feature full-height mirrors with bevelled edges. Interior fittings include velvet-lined jewellery drawers, trouser racks, tie and belt hooks, and LED strip lighting. Engineered wood carcass with real wood veneer exterior.',
 '₹1,20,000 – ₹2,40,000', 'W 300 × D 65 × H 240 cm (fully customisable)', 'Engineered Wood, Real Wood Veneer, Glass Mirror, Soft-close Hardware',
 'White Oak, Walnut, Matte White', 'Real wood veneer, Mirror panels, Matte lacquer',
 'made-to-order', 1, 1, 2,
 'Lumiere Sliding Wardrobe – Custom Wardrobes Vizag | Grand Furniture',
 'Six-door sliding wardrobe with mirrors and LED lighting. Fully customisable. Grand Furniture Vizag, Visakhapatnam.'),

(2, 'Nightfall Bedside Table',        'nightfall-bedside-table',
 'GF-BD-003',
 'Floating nightstand with a solid marble top, cane-panel door, and solid brass handle.',
 'The Nightfall Bedside Table combines contemporary floating design with handcrafted detailing. A solid Portuguese marble top rests on a cane-woven cabinet body finished in hand-painted matte chalk. A single solid brass T-bar handle adds a refined metallic accent. Can be wall-mounted or floor-standing.',
 '₹28,000 – ₹42,000', 'W 50 × D 38 × H 55 cm', 'Marble, Rattan/Cane, Brass, MDF',
 'Chalk White, Sage, Terracotta, Matte Black', 'Chalk paint, Natural cane, Honed marble',
 'in-stock', 0, 1, 3,
 'Nightfall Bedside Table – Luxury Nightstands Vizag | Grand Furniture',
 'Floating bedside table with marble top, cane door, and brass handle. Grand Furniture Vizag.'),

-- Dining Room (category_id=3)
(3, 'Vesper Dining Table',            'vesper-dining-table',
 'GF-DR-001',
 'A live-edge solid mango wood dining table with a hairpin steel base — seats 8 comfortably.',
 'The Vesper Dining Table celebrates the natural character of solid mango wood. Each tabletop is sourced from a single slab, showcasing the tree''s natural live edge, grain, and colour variation. No two tables are identical. The industrial-meets-organic aesthetic is completed by powder-coated hairpin steel legs that provide effortless stability.',
 '₹1,10,000 – ₹1,80,000', 'W 240 × D 100 × H 76 cm (8-seater) | Custom sizes available', 'Solid Mango Wood (live edge slab), Powder-coated Steel',
 'Natural Wood + Black Steel, Natural Wood + Gold Steel', 'Natural wood oil, Matte powder coat',
 'made-to-order', 1, 1, 1,
 'Vesper Live-Edge Dining Table – Luxury Dining Tables Vizag | Grand Furniture',
 'Solid mango wood live-edge dining table seating 8. Custom sizes. Grand Furniture Vizag, Visakhapatnam.'),

(3, 'Contessa Dining Chair',          'contessa-dining-chair',
 'GF-DR-002',
 'An upholstered dining chair with a sculptural solid oak frame and premium fabric seat — sold in sets of 2.',
 'The Contessa Dining Chair pairs sculptural Scandinavian-influenced lines with handcrafted upholstery. The curved backrest is shaped from steam-bent solid oak. The seat is padded with HR foam and upholstered in a performance fabric that is stain-resistant and easy to clean. Sold in pairs or sets of 6.',
 '₹22,000 – ₹32,000 (pair)', 'W 52 × D 56 × H 88 cm, Seat H 46 cm', 'Solid Oak, Performance Fabric, HR Foam',
 'Natural Oak + Linen, Smoked Oak + Velvet Navy, White Oak + Sage', 'Natural oak finish, Stain-resistant fabric',
 'in-stock', 0, 1, 2,
 'Contessa Dining Chair – Luxury Dining Chairs Vizag | Grand Furniture',
 'Sculptural solid oak dining chair with upholstered seat. Sold in pairs. Grand Furniture Vizag.'),

-- Home Office (category_id=4)
(4, 'Stratton Executive Desk',        'stratton-executive-desk',
 'GF-HO-001',
 'A broad executive desk in solid walnut with integrated cable management and a leather writing surface.',
 'The Stratton Executive Desk commands authority and encourages focus. The expansive 180 cm desktop is crafted from bookmatched solid American walnut. A full-width leather writing surface is hand-stitched and hand-dyed. Integrated cable management runs through the pedestal. The left pedestal houses three soft-close drawers; the right features a lockable filing cabinet.',
 '₹1,40,000 – ₹2,10,000', 'W 180 × D 85 × H 76 cm', 'Solid American Walnut, Full-grain Leather, Brass Hardware',
 'Dark Walnut + Cognac Leather, Light Walnut + Black Leather', 'Danish oil finish, Hand-stitched leather',
 'made-to-order', 1, 1, 1,
 'Stratton Executive Desk – Luxury Office Desks Vizag | Grand Furniture',
 'Solid walnut executive desk with leather writing surface and cable management. Grand Furniture Vizag.'),

(4, 'Luminary Bookshelf',             'luminary-bookshelf',
 'GF-HO-002',
 'A floor-to-ceiling modular bookshelf system in solid oak with integrated LED shelves.',
 'The Luminary Bookshelf is designed to be both functional and architectural. Modular solid oak panels can be configured to any wall width and ceiling height. Integrated warm-white LED strip lighting at each shelf level turns your book collection into a gallery. Adjustable shelves accommodate everything from paperbacks to large-format art books.',
 '₹75,000 – ₹1,80,000', 'W 240 × D 35 × H 240 cm (base configuration, fully modular)', 'Solid Oak, Engineered Wood Back, LED Strip',
 'Natural Oak, Smoked Oak, Matte White', 'Natural oil, Lacquer',
 'made-to-order', 0, 1, 2,
 'Luminary Modular Bookshelf – Luxury Bookshelves Vizag | Grand Furniture',
 'Floor-to-ceiling modular bookshelf in solid oak with LED lighting. Grand Furniture Vizag.'),

-- Outdoor (category_id=5)
(5, 'Azure Outdoor Lounge Set',       'azure-outdoor-lounge-set',
 'GF-OD-001',
 'All-weather teak and aluminium outdoor lounge with quick-dry foam cushions.',
 'The Azure Outdoor Lounge Set brings hotel-resort luxury to your outdoor spaces. Frames are crafted from Grade-A plantation teak and marine-grade aluminium. Cushions are filled with open-cell quick-dry foam encased in UV-stabilised, water-repellent Sunbrella fabric. The set includes a 2-seater sofa, 2 armchairs, and a teak-slatted coffee table.',
 '₹1,60,000 – ₹2,40,000', 'Sofa: W 180 × D 85 × H 72 cm | Chair: W 85 × D 85 × H 72 cm', 'Grade-A Teak, Marine Aluminium, Sunbrella Fabric',
 'Dove Grey, Sahara Beige, Charcoal', 'Natural teak seal, Powder-coated aluminium',
 'made-to-order', 1, 1, 1,
 'Azure Outdoor Lounge Set – Luxury Outdoor Furniture Vizag | Grand Furniture',
 'All-weather teak outdoor lounge set with Sunbrella cushions. Grand Furniture Vizag, Visakhapatnam.'),

-- Wardrobes (category_id=6)
(6, 'Palisade Walk-in Wardrobe System','palisade-walk-in-wardrobe',
 'GF-WD-001',
 'A complete walk-in wardrobe system with island dresser, velvet-lined drawers, and LED vanity lighting.',
 'The Palisade Walk-in Wardrobe System is a fully bespoke solution for the ultimate dressing room. Modular panels are engineered for precision fit to your room dimensions. The centrepiece island dresser features velvet-lined jewellery trays, a marble top, and hidden power sockets. Full-height hanging rails, pull-out shoe racks, and sensor-activated LED lighting complete the experience.',
 '₹3,00,000 – ₹8,00,000', 'Fully bespoke to your space', 'Engineered Wood, Real Wood Veneer, Marble, Brass, LED',
 'Soft White, Champagne Gold, Matte Sage, Deep Walnut', 'High-gloss lacquer, Real veneer, Honed marble',
 'made-to-order', 1, 1, 1,
 'Palisade Walk-in Wardrobe – Luxury Walk-in Wardrobes Vizag | Grand Furniture',
 'Bespoke walk-in wardrobe system with island dresser and LED lighting. Grand Furniture Vizag.'),

-- Accent & Decor (category_id=7)
(7, 'Solstice Bar Cabinet',           'solstice-bar-cabinet',
 'GF-AC-001',
 'A fluted-front bar cabinet with a marble interior, brass-framed glass doors, and a mirrored back panel.',
 'The Solstice Bar Cabinet is a statement piece that transforms cocktail hour into a ceremony. Fluted solid mango wood panels flank brass-framed glass doors that reveal a mirrored back panel, marble-lined interior shelves, and bespoke glassware storage. A pull-out serving tray is hidden beneath the marble countertop.',
 '₹95,000 – ₹1,40,000', 'W 110 × D 45 × H 130 cm', 'Mango Wood, Brass, Marble, Mirror Glass',
 'Walnut + Brass, White + Brass, Forest Green + Brass', 'Natural oil, Polished brass, Honed marble',
 'in-stock', 1, 1, 1,
 'Solstice Bar Cabinet – Luxury Bar Cabinets Vizag | Grand Furniture',
 'Fluted bar cabinet with marble interior and brass-framed glass doors. Grand Furniture Vizag.'),

(7, 'Heirloom Console Table',         'heirloom-console-table',
 'GF-AC-002',
 'A slender hand-carved console table in solid sheesham with a hammered brass top.',
 'The Heirloom Console Table is a hallway or living room centrepiece that tells a story. The sheesham wood body features intricate hand-carved geometric patterns on the apron and legs by master artisans. The hammered brass top adds textural contrast and develops a patina over time. A single drawer is lined in raw silk.',
 '₹65,000 – ₹90,000', 'W 140 × D 38 × H 82 cm', 'Solid Sheesham, Hammered Brass, Raw Silk',
 'Dark Sheesham + Antique Brass', 'Carved wood, Hammered brass, Silk-lined drawer',
 'in-stock', 0, 1, 2,
 'Heirloom Console Table – Luxury Console Tables Vizag | Grand Furniture',
 'Hand-carved sheesham console table with hammered brass top. Grand Furniture Vizag.'),

-- Kids & Youth (category_id=8)
(8, 'Dreamscape Kids Bed',            'dreamscape-kids-bed',
 'GF-KY-001',
 'A whimsical yet refined kids bed in solid rubberwood with a built-in bookcase headboard and under-bed storage.',
 'The Dreamscape Kids Bed is built to grow with your child. The solid rubberwood frame is finished in non-toxic, child-safe chalk paint. The headboard integrates a three-section bookcase — perfect for bedtime reading. Two large under-bed drawers on smooth soft-close runners provide generous toy and linen storage. Available in Single and Full sizes.',
 '₹55,000 – ₹75,000', 'Single: W 110 × D 210 × H 110 cm | Full: W 140 × D 210 × H 110 cm', 'Solid Rubberwood, MDF Accents, Non-toxic Chalk Paint',
 'Warm White, Sage Green, Soft Yellow', 'Child-safe chalk paint, Soft-close drawers',
 'in-stock', 0, 1, 1,
 'Dreamscape Kids Bed – Luxury Kids Furniture Vizag | Grand Furniture',
 'Solid rubberwood kids bed with bookcase headboard and under-bed storage. Grand Furniture Vizag.'),

(8, 'Scholar Study Desk & Chair',     'scholar-study-desk-chair',
 'GF-KY-002',
 'An ergonomic height-adjustable study desk and chair set for children aged 6–16.',
 'The Scholar Study Desk & Chair set is designed with child ergonomics at its core. The desk height adjusts in 5 cm increments from 56 to 76 cm, accommodating children as they grow. The tilting desktop reduces neck strain. The matching chair is adjustable in seat height and backrest angle. Both are crafted from solid rubberwood and finished in non-toxic paints.',
 '₹32,000 – ₹45,000', 'Desk: W 120 × D 60 × H 56–76 cm (adjustable) | Chair: adjustable', 'Solid Rubberwood, Non-toxic Paint, Ergonomic Foam',
 'White + Natural Wood, White + Blue, White + Pink', 'Non-toxic lacquer, Ergonomic seat cushion',
 'in-stock', 0, 1, 2,
 'Scholar Study Desk & Chair – Kids Study Furniture Vizag | Grand Furniture',
 'Height-adjustable ergonomic study desk and chair for children. Grand Furniture Vizag.'),

(2, 'Classic Teak Cot',              'classic-teak-cot',
 'GF-BD-004',
 'A clean-lined solid teak cot with a supportive slatted base and handcrafted joinery.',
 'The Classic Teak Cot pairs a durable solid-wood frame with a calm, timeless profile. Its supportive slatted base and sturdy joinery make it a practical centrepiece for a restful bedroom. Available in single, queen, and king sizes.',
 '₹68,000 – ₹1,35,000', 'Single, Queen, and King sizes available', 'Solid Teak Wood',
 'Natural Teak, Dark Walnut', 'Hand-rubbed teak oil, Matte walnut stain',
 'made-to-order', 1, 1, 4,
 'Classic Teak Cot – Solid Wood Beds Vizag | Grand Furniture',
 'Solid teak cot with a slatted base, handcrafted joinery, and custom sizes. Grand Furniture Vizag.');

-- Local product photos for the seeded catalogue
INSERT INTO `product_images` (`product_id`, `image_path`, `alt_text`, `is_primary`, `sort_order`) VALUES
(1, '/uploads/products/meridian-sectional-sofa.jpg', 'Green upholstered sectional sofa', 1, 0),
(2, '/uploads/products/aurelius-coffee-table.jpg', 'Modern living room coffee table', 1, 0),
(3, '/uploads/products/riviera-accent-chair.jpg', 'Upholstered accent chair', 1, 0),
(4, '/uploads/products/palazzo-king-bed.jpg', 'Upholstered king bed in a bright bedroom', 1, 0),
(5, '/uploads/products/lumiere-wardrobe.jpg', 'Contemporary bedroom storage', 1, 0),
(6, '/uploads/products/nightfall-bedside-table.jpg', 'Calm bedside interior', 1, 0),
(7, '/uploads/products/vesper-dining-table.jpg', 'Solid wood dining table and chairs', 1, 0),
(8, '/uploads/products/contessa-dining-chair.jpg', 'Dining chairs around a table', 1, 0),
(9, '/uploads/products/stratton-executive-desk.jpg', 'Desk and laptop in a bright office', 1, 0),
(10, '/uploads/products/luminary-bookshelf.jpg', 'Bookshelf and reading space', 1, 0),
(11, '/uploads/products/azure-outdoor-lounge-set.jpg', 'Outdoor lounge furniture', 1, 0),
(12, '/uploads/products/palisade-walk-in-wardrobe.jpg', 'Walk-in wardrobe interior', 1, 0),
(13, '/uploads/products/solstice-bar-cabinet.jpg', 'Contemporary home interior', 1, 0),
(14, '/uploads/products/heirloom-console-table.jpg', 'Console table and living space', 1, 0),
(15, '/uploads/products/dreamscape-kids-bed.jpg', 'Bright bedroom with upholstered bed', 1, 0),
(16, '/uploads/products/scholar-study-desk-chair.jpg', 'Office meeting table and chairs', 1, 0),
(17, '/uploads/products/classic-teak-cot.jpg', 'Teak cot in a restful living space', 1, 0);

-- ============================================================
-- SEED DATA: website_settings
-- ============================================================
INSERT INTO `website_settings` (`setting_key`, `setting_value`) VALUES
('site_name',          'Grand Furniture Vizag'),
('tagline',            'Where Craftsmanship Meets Luxury'),
('phone_primary',      '09052751666'),
('phone_secondary',    ''),
('email',              'hello@grandfurniturevizag.com'),
('address',            '12-3-456, MG Road, Dwaraka Nagar, Visakhapatnam, Andhra Pradesh – 530 016'),
('whatsapp_number',    '919052751666'),
('business_hours',     'Monday – Saturday: 10:00 AM – 8:00 PM | Sunday: 11:00 AM – 6:00 PM'),
('google_maps_embed',  'https://maps.google.com/maps?q=Visakhapatnam&output=embed'),
('facebook_url',       'https://facebook.com/grandfurniturevizag'),
('instagram_url',      'https://instagram.com/grandfurniturevizag'),
('youtube_url',        'https://youtube.com/@grandfurniturevizag'),
('meta_description',   'Grand Furniture Vizag — Luxury handcrafted furniture in Visakhapatnam. Sofas, beds, dining tables, wardrobes, and bespoke custom pieces.'),
('meta_keywords',      'luxury furniture Vizag, luxury furniture Visakhapatnam, sofa Vizag, wardrobe Vizag, custom furniture Visakhapatnam, Grand Furniture'),
('og_image',           '/assets/images/og-image.jpg'),
('footer_about',       'Grand Furniture Vizag is Visakhapatnam''s premier destination for luxury, handcrafted furniture. Each piece is designed by our in-house team and crafted by master artisans.'),
('enquiry_email_to',   'admin@grandfurniturevizag.com'),
('smtp_host',          ''),
('smtp_port',          '587'),
('smtp_user',          ''),
('smtp_pass',          ''),
('smtp_from_name',     'Grand Furniture Vizag'),
('smtp_from_email',    'noreply@grandfurniturevizag.com');

-- ============================================================
-- SEED DATA: testimonials
-- ============================================================
INSERT INTO `testimonials` (`name`, `location`, `rating`, `review`, `is_verified`, `is_active`) VALUES
('Srinivasa Rao P.',  'MVP Colony, Vizag',      5, 'The Meridian Sectional Sofa exceeded every expectation. The leather quality is extraordinary and the craftsmanship is flawless. Grand Furniture Vizag delivers on their promise of luxury.', 1, 1),
('Meenakshi Sharma',  'Dwaraka Nagar, Vizag',   5, 'We ordered a custom walk-in wardrobe and the team was incredible throughout the process. Perfect fit, stunning finish, and delivered ahead of schedule.', 1, 1),
('Arjun Reddy',       'Madhurawada, Vizag',     5, 'The Vesper dining table is the crown jewel of our home. The live-edge slab is breathtaking — truly a piece of art. Worth every rupee.', 1, 1),
('Priya Nair',        'Rushikonda, Vizag',      5, 'From selection to delivery and installation, the experience was seamless. The team is knowledgeable and genuinely passionate about their craft.', 1, 1),
('Venkata Subbaiah',  'Steel Plant City, Vizag',5, 'Furnished our entire home through Grand Furniture Vizag. The quality is consistent across every piece and the after-sales support is excellent.', 1, 1),
('Kavitha Devi',      'Bheemunipatnam',         4, 'Beautiful Palazzo bed — absolutely transformed our master bedroom. Delivery team was professional and the installation was done perfectly.', 1, 1);

-- ============================================================
-- SEED DATA: gallery
-- ============================================================
INSERT INTO `gallery` (`title`, `image_path`, `category`, `alt_text`, `is_active`, `sort_order`) VALUES
('Grand Furniture Showroom Entrance',  '/uploads/gallery/showroom-entrance.jpg',   'showroom',         'Grand Furniture Vizag showroom entrance, Visakhapatnam', 1, 1),
('Living Room Display',                '/uploads/gallery/showroom-living.jpg',     'showroom',         'Luxury living room display at Grand Furniture Vizag',    1, 2),
('Bedroom Collection Display',         '/uploads/gallery/showroom-bedroom.jpg',    'showroom',         'Premium bedroom furniture display, Grand Furniture Vizag',1, 3),
('Meridian Sectional Sofa',            '/uploads/gallery/meridian-sofa.jpg',       'furniture',        'Meridian Sectional Sofa in cognac leather',              1, 1),
('Vesper Live-Edge Dining Table',      '/uploads/gallery/vesper-table.jpg',        'furniture',        'Vesper live-edge mango wood dining table',               1, 2),
('Palazzo King Bed',                   '/uploads/gallery/palazzo-bed.jpg',         'furniture',        'Palazzo King Bed in royal blue velvet',                  1, 3),
('Solstice Bar Cabinet',               '/uploads/gallery/solstice-bar.jpg',        'furniture',        'Solstice fluted bar cabinet with brass accents',         1, 4),
('Modern Luxury Living Room',          '/uploads/gallery/inspiration-living.jpg',  'room-inspiration', 'Modern luxury living room inspiration',                  1, 1),
('Boutique Hotel-Style Bedroom',       '/uploads/gallery/inspiration-bedroom.jpg', 'room-inspiration', 'Boutique hotel-inspired master bedroom',                 1, 2),
('Contemporary Home Office',           '/uploads/gallery/inspiration-office.jpg',  'room-inspiration', 'Contemporary home office with Stratton desk',            1, 3);

-- ============================================================
-- SEED DATA: homepage_sections
-- ============================================================
INSERT INTO `homepage_sections` (`section_key`, `title`, `subtitle`, `content`, `is_active`, `sort_order`) VALUES
('hero',      'Where Craftsmanship Meets Luxury',  'Handcrafted Furniture for Discerning Homes', 'Explore our curated collections of luxury furniture, each piece crafted by master artisans.', 1, 1),
('about',     'The Grand Story',                   'Three Decades of Artisanal Excellence',      'Founded in Visakhapatnam with a singular passion for exceptional furniture, Grand Furniture Vizag has been crafting heirloom-quality pieces for discerning homes across Andhra Pradesh for over three decades. Every piece that leaves our workshop is a testament to the marriage of traditional craftsmanship and contemporary design.', 1, 2),
('featured',  'Signature Collection',              'Pieces That Define Luxury Living',           '', 1, 3),
('process',   'The Grand Process',                 'From Vision to Heirloom',                    '', 1, 4),
('cta',       'Commission Your Dream Piece',       'Bespoke Furniture, Crafted for You',         'Tell us your vision and our design team will bring it to life — custom dimensions, materials, and finishes.', 1, 5);

-- ============================================================
-- END OF SCHEMA
-- ============================================================
