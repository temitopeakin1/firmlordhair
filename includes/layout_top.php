<?php
declare(strict_types=1);
$description = $description ?? '';
$flash = take_flash();
$openBag = take_open_bag();
$count = cart_count();
$here = static function (string $path): string {
    return request_path() === $path ? ' is-on' : '';
};
$path = request_path();
$categorySlug = '';
$activeGroup = (string) ($_GET['group'] ?? '');
foreach (['/categories/', '/category/'] as $prefix) {
    if (!str_starts_with($path, $prefix)) {
        continue;
    }
    $rest = trim(substr($path, strlen($prefix)), '/');
    $bits = $rest === '' ? [] : explode('/', $rest);
    if (count($bits) >= 2 && in_array($bits[0], category_menu_groups(), true)) {
        $activeGroup = $bits[0];
        $categorySlug = $bits[1];
    } elseif (count($bits) === 1 && in_array($bits[0], category_menu_groups(), true)) {
        $activeGroup = $bits[0];
    } elseif (count($bits) >= 1) {
        $categorySlug = $bits[0];
    }
    break;
}
$activeCategory = $categorySlug !== '' ? category_by($categorySlug) : null;
if ($activeCategory !== null && $activeGroup === '') {
    $activeGroup = (string) ($activeCategory['group'] ?? '');
}
$onCategories = ($activeCategory !== null || $activeGroup !== '') ? ' is-on' : '';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title === SITE_NAME ? SITE_NAME : $title . ' · ' . SITE_NAME) ?></title>
    <?php if ($description !== ''): ?>
        <meta name="description" content="<?= e($description) ?>">
    <?php endif; ?>
    <link rel="icon" href="<?= e(asset('images/logo.svg')) ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
    <script>document.documentElement.classList.add('js')</script>
</head>
<body class="<?= $openBag ? 'bag-open' : '' ?>">
<a class="skip" href="#content">Skip to content</a>
<div class="topbar">
<div class="notice" aria-label="Delivery notice">
    <div class="notice-track">
        <?php for ($i = 0; $i < 2; $i++): ?>
            <span>Nationwide Delivery</span>
            <span>Fast and quick delivery</span>
            <span>Quality hairs and accessories</span>
            <span>Same-day dispatch in Lagos</span>
            <span>Secure packaging for every piece</span>
            <span>We sell luxury hair of all types</span>
            <span>Raw hair</span>
            <span>Donor hair</span>
            <span>Vietnamese hair</span>
            <span>Chinese luxury hair</span>
            <span>Cambodia hair</span>
            <span>Frontal and closure of different types</span>
            <span>Packet weavon</span>
            <span>Hair products and salon equipment</span>
        <?php endfor; ?>
    </div>
</div>
<header class="site-header">
    <div class="wrap header-inner">
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">Menu</button>
        <a class="brand" href="<?= e(url()) ?>">
            <?= file_get_contents(dirname(__DIR__) . '/assets/images/logo.svg') ?>
            <span class="brand-name"><?= e(SITE_NAME) ?></span>
        </a>
        <nav class="nav" id="site-nav">
            <a class="<?= trim($here('/')) ?>" href="<?= e(url()) ?>">Home</a>
            <a class="<?= trim($here('/shop')) ?>" href="<?= e(url('shop')) ?>">Shop</a>
            <div class="nav-item has-sub">
                <button class="nav-sub-toggle<?= $onCategories ?>" type="button" aria-expanded="false" aria-controls="nav-categories">Categories</button>
                <div class="nav-sub" id="nav-categories">
                    <?php foreach (shop_nav() as $item): ?>
                        <?php if ($item['kind'] === 'link'): ?>
                            <a class="<?= trim($here('/categories/' . $item['slug'])) ?>" href="<?= e(url(category_path(category_by($item['slug'])))) ?>"><?= e($item['name']) ?></a>
                        <?php else: ?>
                            <?php
                            $childCats = categories_in_group($item['group']);
                            $menuOn = $activeGroup === $item['group'] ? ' is-on' : '';
                            ?>
                            <div class="nav-item has-sub">
                                <button class="nav-sub-toggle<?= $menuOn ?>" type="button" aria-expanded="false" aria-controls="<?= e($item['id']) ?>"><?= e($item['name']) ?></button>
                                <div class="nav-sub nav-sub-nested" id="<?= e($item['id']) ?>">
                                    <?php foreach ($childCats as $cat): ?>
                                        <a class="<?= trim($here('/' . category_path($cat))) ?>" href="<?= e(url(category_path($cat))) ?>"><?= e($cat['name']) ?></a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <a class="<?= trim($here('/about')) ?>" href="<?= e(url('about')) ?>">The House</a>
        </nav>
        <div class="tools">
            <button class="icon-btn" type="button" data-open-search aria-label="Search">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M16 16l5 5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </button>
            <a class="icon-btn" href="<?= e(url(customer() ? 'account' : 'login')) ?>" aria-label="Account">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M5 19.5c1.4-3 3.8-4.5 7-4.5s5.6 1.5 7 4.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </a>
            <button class="icon-btn bag-btn" type="button" data-open-bag aria-label="Open bag">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 8h12l-1 12H7L6 8z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M9 8V7a3 3 0 0 1 6 0v1" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>
                <span class="bag-count"><?= $count ?></span>
            </button>
        </div>
    </div>
</header>
</div>
<?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
<main id="content">
