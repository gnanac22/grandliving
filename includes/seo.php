<?php
/**
 * Grand Furniture Vizag — SEO Helper Functions
 * Outputs structured meta tags, Open Graph, Twitter Cards, and JSON-LD schemas.
 */

/**
 * Output all <head> meta tags for a given page.
 *
 * @param string $title        Page title (without site name suffix).
 * @param string $description  Meta description (max ~160 chars).
 * @param string $keywords     Comma-separated keywords (optional).
 * @param string $image        Absolute URL to OG image (falls back to default).
 * @param string $type         OG type: 'website' | 'product' | 'article'.
 * @param string $url          Canonical URL (falls back to current URL).
 */
function outputMetaTags(
    string $title,
    string $description,
    string $keywords = '',
    string $image    = '',
    string $type     = 'website',
    string $url      = ''
): void {
    // Ensure constants are defined (defensive)
    $siteName = defined('SITE_NAME') ? SITE_NAME : 'Grand Furniture Vizag';
    $siteUrl  = defined('SITE_URL')  ? SITE_URL  : '';

    // Build canonical URL
    if (empty($url)) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? '';
        $uri    = $_SERVER['REQUEST_URI'] ?? '/';
        $url    = $scheme . '://' . $host . strtok($uri, '?');  // Strip query string
    }

    // Default OG image
    if (empty($image)) {
        $image = $siteUrl . '/assets/images/og-image.jpg';
    }

    // Full page title
    $fullTitle  = htmlspecialchars($title . ' | ' . $siteName, ENT_QUOTES, 'UTF-8');
    $desc       = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    $kwds       = htmlspecialchars($keywords,    ENT_QUOTES, 'UTF-8');
    $canonUrl   = htmlspecialchars($url,         ENT_QUOTES, 'UTF-8');
    $ogImage    = htmlspecialchars($image,        ENT_QUOTES, 'UTF-8');
    $siteNameE  = htmlspecialchars($siteName,     ENT_QUOTES, 'UTF-8');
    $ogType     = htmlspecialchars($type,         ENT_QUOTES, 'UTF-8');

    echo <<<HTML
    <!-- Primary Meta Tags -->
    <title>{$fullTitle}</title>
    <meta name="title"       content="{$fullTitle}">
    <meta name="description" content="{$desc}">
    <meta name="robots"      content="index, follow">
    <link rel="canonical"    href="{$canonUrl}">

    HTML;

    if (!empty($keywords)) {
        echo "    <meta name=\"keywords\" content=\"{$kwds}\">\n";
    }

    echo <<<HTML
    <!-- Open Graph / Facebook -->
    <meta property="og:type"        content="{$ogType}">
    <meta property="og:url"         content="{$canonUrl}">
    <meta property="og:title"       content="{$fullTitle}">
    <meta property="og:description" content="{$desc}">
    <meta property="og:image"       content="{$ogImage}">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name"   content="{$siteNameE}">
    <meta property="og:locale"      content="en_IN">

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:url"         content="{$canonUrl}">
    <meta name="twitter:title"       content="{$fullTitle}">
    <meta name="twitter:description" content="{$desc}">
    <meta name="twitter:image"       content="{$ogImage}">

    HTML;
}

/**
 * Output JSON-LD LocalBusiness schema for Grand Furniture Vizag.
 *
 * @param array $settings  Website settings array from getSettings($pdo).
 */
function outputLocalBusinessSchema(array $settings): void
{
    $siteName   = $settings['site_name']      ?? 'Grand Furniture Vizag';
    $siteUrl    = defined('SITE_URL') ? SITE_URL : '';
    $address    = $settings['address']        ?? '';
    $phone1     = $settings['phone_primary']  ?? '';
    $phone2     = $settings['phone_secondary']?? '';
    $email      = $settings['email']          ?? '';
    $hours      = $settings['business_hours'] ?? '';
    $instagram  = $settings['instagram_url']  ?? '';
    $facebook   = $settings['facebook_url']   ?? '';

    // Parse business hours into OpeningHoursSpecification (simplified: Mon-Sat + Sun)
    $openingHours = ["Mo-Sa 10:00-20:00", "Su 11:00-18:00"];

    $sameAs = array_values(array_filter([$instagram, $facebook]));

    $schema = [
        '@context'          => 'https://schema.org',
        '@type'             => 'FurnitureStore',
        'name'              => $siteName,
        'url'               => $siteUrl,
        'logo'              => $siteUrl . '/assets/images/logo.png',
        'image'             => $siteUrl . '/assets/images/og-image.jpg',
        'description'       => $settings['meta_description'] ?? 'Luxury handcrafted furniture in Visakhapatnam.',
        'telephone'         => $phone1,
        'email'             => $email,
        'address'           => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $address,
            'addressLocality' => 'Visakhapatnam',
            'addressRegion'   => 'Andhra Pradesh',
            'postalCode'      => '530016',
            'addressCountry'  => 'IN',
        ],
        'geo' => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => '17.7231',
            'longitude' => '83.3012',
        ],
        'openingHoursSpecification' => [
            [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                'opens'     => '10:00',
                'closes'    => '20:00',
            ],
            [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Sunday'],
                'opens'     => '11:00',
                'closes'    => '18:00',
            ],
        ],
        'priceRange'    => '₹₹₹',
        'currenciesAccepted' => 'INR',
        'paymentAccepted'    => 'Cash, Credit Card, Debit Card, Bank Transfer',
        'sameAs'        => $sameAs,
    ];

    if ($phone2) {
        $schema['contactPoint'] = [
            '@type'       => 'ContactPoint',
            'telephone'   => $phone2,
            'contactType' => 'customer service',
            'areaServed'  => 'IN',
            'availableLanguage' => ['English', 'Telugu'],
        ];
    }

    echo "\n    <script type=\"application/ld+json\">\n"
        . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        . "\n    </script>\n";
}

/**
 * Output JSON-LD Product schema for a product page.
 * Does NOT include fake reviews or ratings.
 *
 * @param array $product  Product row from getProduct($slug, $pdo).
 */
function outputProductSchema(array $product): void
{
    $siteUrl = defined('SITE_URL') ? SITE_URL : '';
    $siteName = defined('SITE_NAME') ? SITE_NAME : 'Grand Furniture Vizag';

    $primaryImage = '';
    if (!empty($product['images'])) {
        foreach ($product['images'] as $img) {
            if ($img['is_primary']) {
                $primaryImage = $siteUrl . $img['image_path'];
                break;
            }
        }
        if (!$primaryImage) {
            $primaryImage = $siteUrl . $product['images'][0]['image_path'];
        }
    }

    $availability = match ($product['availability'] ?? 'made-to-order') {
        'in-stock'       => 'https://schema.org/InStock',
        'out-of-stock'   => 'https://schema.org/OutOfStock',
        default          => 'https://schema.org/PreOrder',
    };

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        'name'        => $product['name'],
        'description' => strip_tags($product['short_description'] ?? $product['description'] ?? ''),
        'sku'         => $product['sku'] ?? '',
        'brand'       => [
            '@type' => 'Brand',
            'name'  => $siteName,
        ],
        'manufacturer' => [
            '@type' => 'Organization',
            'name'  => $siteName,
        ],
        'offers' => [
            '@type'         => 'Offer',
            'url'           => $siteUrl . '/product/' . ($product['slug'] ?? ''),
            'priceCurrency' => 'INR',
            'price'         => '', // No single price — enquiry model
            'priceSpecification' => [
                '@type'           => 'PriceSpecification',
                'priceCurrency'   => 'INR',
                'name'            => $product['price_range'] ?? 'Price on enquiry',
            ],
            'availability' => $availability,
            'seller'       => [
                '@type' => 'Organization',
                'name'  => $siteName,
            ],
        ],
        'material'   => $product['materials']   ?? '',
        'color'      => $product['colors']      ?? '',
    ];

    if ($primaryImage) {
        $schema['image'] = $primaryImage;
    }

    if (!empty($product['category_name'])) {
        $schema['category'] = $product['category_name'] . ' Furniture';
    }

    echo "\n    <script type=\"application/ld+json\">\n"
        . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        . "\n    </script>\n";
}

/**
 * Output JSON-LD BreadcrumbList schema.
 *
 * @param array $breadcrumbs  Ordered array of ['name' => string, 'url' => string].
 *                             Last item can omit 'url' (current page).
 */
function outputBreadcrumbSchema(array $breadcrumbs): void
{
    $siteUrl = defined('SITE_URL') ? SITE_URL : '';
    $items   = [];
    $pos     = 1;

    foreach ($breadcrumbs as $crumb) {
        $item = [
            '@type'    => 'ListItem',
            'position' => $pos,
            'name'     => $crumb['name'],
        ];
        if (!empty($crumb['url'])) {
            $item['item'] = strpos($crumb['url'], 'http') === 0
                ? $crumb['url']
                : $siteUrl . $crumb['url'];
        }
        $items[] = $item;
        $pos++;
    }

    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];

    echo "\n    <script type=\"application/ld+json\">\n"
        . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        . "\n    </script>\n";
}

/**
 * Render HTML breadcrumb navigation (visible, not just schema).
 *
 * @param array $breadcrumbs  Same format as outputBreadcrumbSchema().
 */
function renderBreadcrumbs(array $breadcrumbs): void
{
    echo '<nav class="breadcrumb-nav" aria-label="Breadcrumb">' . "\n";
    echo '  <ol class="breadcrumb-list">' . "\n";
    $last = array_key_last($breadcrumbs);
    foreach ($breadcrumbs as $i => $crumb) {
        $name = htmlspecialchars($crumb['name'], ENT_QUOTES, 'UTF-8');
        if ($i === $last || empty($crumb['url'])) {
            echo "    <li class=\"breadcrumb-item current\" aria-current=\"page\">{$name}</li>\n";
        } else {
            $url = htmlspecialchars($crumb['url'], ENT_QUOTES, 'UTF-8');
            echo "    <li class=\"breadcrumb-item\"><a href=\"{$url}\">{$name}</a></li>\n";
        }
    }
    echo '  </ol>' . "\n";
    echo '</nav>' . "\n";
}
