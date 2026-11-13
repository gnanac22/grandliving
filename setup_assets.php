<?php
require_once __DIR__ . '/config/db.php';

echo "Updating database with images and Google Maps...\n";

// 1. Insert product_images
$products = [
    1  => 'meridian-sectional-sofa.jpg',
    2  => 'aurelius-coffee-table.jpg',
    3  => 'riviera-accent-chair.jpg',
    4  => 'palazzo-king-bed.jpg',
    5  => 'lumiere-wardrobe.jpg',
    6  => 'nightfall-bedside-table.jpg',
    7  => 'vesper-dining-table.jpg',
    8  => 'contessa-dining-chair.jpg',
    9  => 'stratton-executive-desk.jpg',
    10 => 'luminary-bookshelf.jpg',
    11 => 'azure-outdoor-lounge-set.jpg',
    12 => 'palisade-walk-in-wardrobe.jpg',
    13 => 'solstice-bar-cabinet.jpg',
    14 => 'heirloom-console-table.jpg',
    15 => 'dreamscape-kids-bed.jpg',
    16 => 'scholar-study-desk-chair.jpg'
];

$pdo->exec('DELETE FROM product_images');
$ins = $pdo->prepare('INSERT INTO product_images (product_id, image_path, alt_text, is_primary, sort_order) VALUES (?, ?, ?, 1, 0)');

foreach ($products as $id => $file) {
    $path = '/uploads/products/' . $file;
    $alt  = str_replace(['-', '.jpg'], [' ', ''], $file);
    $ins->execute([$id, $path, ucwords($alt)]);
}
echo "✓ Inserted 16 product_images\n";

// 2. Download luxury category images and hero
$images = [
    'assets/images/hero-bg.jpg' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1920&q=85',
    'uploads/categories/living-room.jpg' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=800&q=80',
    'uploads/categories/bedroom.jpg' => 'https://images.unsplash.com/photo-1540518614846-7ede433c4ef9?auto=format&fit=crop&w=800&q=80',
    'uploads/categories/dining-room.jpg' => 'https://images.unsplash.com/photo-1617806118233-18e1de247200?auto=format&fit=crop&w=800&q=80',
    'uploads/categories/home-office.jpg' => 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?auto=format&fit=crop&w=800&q=80',
    'uploads/categories/outdoor.jpg' => 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=800&q=80',
    'uploads/categories/wardrobes.jpg' => 'https://images.unsplash.com/photo-1558997519-83ea9252def8?auto=format&fit=crop&w=800&q=80',
    'uploads/categories/accent-decor.jpg' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=800&q=80',
    'uploads/categories/kids-youth.jpg' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=800&q=80',

    // Gallery images
    'uploads/gallery/showroom-living.jpg' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1000&q=80',
    'uploads/gallery/showroom-bedroom.jpg' => 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?auto=format&fit=crop&w=1000&q=80',
    'uploads/gallery/inspiration-living.jpg' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1000&q=80',
    'uploads/gallery/inspiration-bedroom.jpg' => 'https://images.unsplash.com/photo-1617325247661-675ab4b64ae2?auto=format&fit=crop&w=1000&q=80',
    'uploads/gallery/inspiration-office.jpg' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=1000&q=80',
];

foreach ($images as $localPath => $url) {
    if (!file_exists(__DIR__ . '/' . $localPath)) {
        echo "Downloading: {$localPath}... ";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code == 200 && !empty($data)) {
            file_put_contents(__DIR__ . '/' . $localPath, $data);
            echo "DONE (" . round(strlen($data) / 1024) . " KB)\n";
        } else {
            echo "FAILED (code $code)\n";
        }
    } else {
        echo "Exists: {$localPath}\n";
    }
}

// 3. Update categories image paths
$catImages = [
    'living-room'   => 'living-room.jpg',
    'bedroom'       => 'bedroom.jpg',
    'dining-room'   => 'dining-room.jpg',
    'home-office'   => 'home-office.jpg',
    'outdoor'       => 'outdoor.jpg',
    'wardrobes'     => 'wardrobes.jpg',
    'accent-decor'  => 'accent-decor.jpg',
    'kids-youth'    => 'kids-youth.jpg',
];

$upCat = $pdo->prepare('UPDATE categories SET image = ? WHERE slug = ?');
foreach ($catImages as $slug => $img) {
    $upCat->execute([$img, $slug]);
}
echo "✓ Updated categories images\n";

// 4. Update website_settings with Grand Living Vizag maps link & address
$settingsToUpdate = [
    'site_name'         => 'Grand Living',
    'tagline'           => 'The Art of Living Beautifully',
    'address'           => 'Grand Living, Dwaraka Nagar Main Road, Visakhapatnam, Andhra Pradesh – 530 016',
    'google_maps_link'  => 'https://maps.app.goo.gl/4H7RJMT3GGWLZR7R7',
    'google_maps_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3800.672520330607!2d83.30708307593645!3d17.725510783220456!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a394300718ddd9d%3A0x28756797bcfa5bf7!2sGrand%20Living!5e0!3m2!1sen!2sin!4v1731518000000!5m2!1sen!2sin',
];

$setStmt = $pdo->prepare('INSERT INTO website_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?');
foreach ($settingsToUpdate as $k => $v) {
    $setStmt->execute([$k, $v, $v]);
}
echo "✓ Updated website settings with Grand Living Vizag maps link and embed\n";
echo "All assets updated successfully!\n";
