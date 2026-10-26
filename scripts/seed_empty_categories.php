<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

$photoMap = [
    'raw-body-wave' => 'brazilian-body-wave-0.jpg',
    'raw-straight-wig' => 'firm-line-wig-0.png',
    'donor-silk-straight' => 'brazilian-silk-straight-0.jpg',
    'donor-loose-wave' => 'harbour-loose-wave-0.jpg',
    'vietnamese-bone-straight' => 'silk-press-bundle-0.jpg',
    'vietnamese-body-wave' => 'palm-body-wave-0.jpg',
    'chinese-thick-straight' => 'brazilian-silk-straight-0.jpg',
    'chinese-body-wave' => 'brazilian-body-wave-0.jpg',
    'cambodian-deep-wave' => 'deep-wave-trio-0.jpg',
    'cambodian-curly-bundle' => 'coil-and-kink-0.jpg',
    'hd-13x4-frontal' => 'firm-line-wig-0.png',
    'transparent-13x6-frontal' => 'harbour-loose-wave-0.jpg',
    'hd-5x5-closure' => 'brazilian-silk-straight-0.jpg',
    'silk-4x4-closure' => 'brazilian-body-wave-0.jpg',
];

$dir = dirname(__DIR__) . '/assets/images/products';
$added = 0;

foreach (catalog_seed() as $product) {
    $slug = $product['slug'];
    if (product_by($slug)) {
        continue;
    }
    insert_product_row($product);
    products(true);
    $added++;
    echo "added {$slug}\n";

    if (!isset($photoMap[$slug])) {
        continue;
    }
    $srcName = $photoMap[$slug];
    $src = $dir . '/' . $srcName;
    $ext = pathinfo($srcName, PATHINFO_EXTENSION);
    $destName = $slug . '-0.' . $ext;
    $dest = $dir . '/' . $destName;
    if (is_file($src) && !is_file($dest)) {
        copy($src, $dest);
    }
    if (is_file($dest)) {
        save_product_photo($slug, 0, $destName);
        echo "  photo {$destName}\n";
    }
}

echo "done added={$added}\n";
foreach (db()->query('SELECT category, COUNT(*) n FROM products GROUP BY category ORDER BY category') as $row) {
    echo $row['category'] . ' ' . $row['n'] . "\n";
}
