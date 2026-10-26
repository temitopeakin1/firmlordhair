<?php
declare(strict_types=1);
$heading = $heading ?? 'The shop';
$intro = $intro ?? '';
$query = $query ?? (string) ($filters['q'] ?? '');
$products = $products ?? [];
$filters = is_array($filters ?? null) ? $filters : [];
$action = $action ?? 'shop';
$categorySlug = (string) ($filters['category'] ?? '');
$groupSlug = (string) ($filters['group'] ?? '');
$parentGroup = (string) ($parent_group ?? $groupSlug);
$parentMeta = is_array($parent_meta ?? null) ? $parent_meta : null;
$pillCategories = $pill_categories ?? categories();
$inMenuGroup = $parentGroup !== '' && in_array($parentGroup, category_menu_groups(), true);
?>
<section class="page-hero">
    <div class="wrap">
        <?php
        $trail = [['Home', '/'], ['Shop', '/shop']];
        if ($inMenuGroup && $parentMeta) {
            if ($categorySlug !== '') {
                $trail[] = [$parentMeta['name'], '/' . category_group_path($parentGroup)];
                $trail[] = [$heading, ''];
            } else {
                $trail[] = [$parentMeta['name'], ''];
            }
        } elseif ($categorySlug !== '' || $groupSlug !== '') {
            $trail[] = [$heading, ''];
        } elseif ($query !== '') {
            $trail[] = ['Search', ''];
        }
        crumbs($trail);
        ?>
        <h1><?= e($heading) ?></h1>
        <p class="lede"><?= e($intro) ?></p>
    </div>
</section>
<section class="section section-tight">
    <div class="wrap">
        <form class="search-inline" action="<?= e(url('search')) ?>" method="get">
            <label>Find a piece
                <input type="search" name="q" value="<?= e($query) ?>" placeholder="Bob, 613, closure, spray">
            </label>
            <button class="btn btn-small" type="submit">Search</button>
        </form>
        <div class="pills" aria-label="Categories">
            <a class="pill<?= $categorySlug === '' && !$inMenuGroup && $query === '' ? ' is-on' : '' ?>" href="<?= e(url('shop')) ?>">All</a>
            <?php if (!$inMenuGroup): ?>
                <a class="pill" href="<?= e(url(category_group_path('origin'))) ?>">Hair by Origin</a>
                <a class="pill" href="<?= e(url(category_group_path('piece'))) ?>">Frontals & Closures</a>
            <?php else: ?>
                <a class="pill<?= $categorySlug === '' ? ' is-on' : '' ?>" href="<?= e(url(category_group_path($parentGroup))) ?>"><?= e($parentMeta['name'] ?? 'All') ?></a>
            <?php endif; ?>
            <?php foreach ($pillCategories as $cat): ?>
                <a class="pill<?= $categorySlug === $cat['slug'] ? ' is-on' : '' ?>" href="<?= e(url(category_path($cat))) ?>"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <form class="filters" method="get" action="<?= e(url($action)) ?>" data-auto-submit>
            <?php if ($query !== ''): ?><input type="hidden" name="q" value="<?= e($query) ?>"><?php endif; ?>
            <?php if ($groupSlug !== '' && $categorySlug === ''): ?><input type="hidden" name="group" value="<?= e($groupSlug) ?>"><?php endif; ?>
            <label>Texture
                <select name="texture" aria-label="Filter by texture">
                    <option value="">Any</option>
                    <?php foreach (textures() as $texture): ?>
                        <option value="<?= e($texture) ?>"<?= ($filters['texture'] ?? '') === $texture ? ' selected' : '' ?>><?= e($texture) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Sort
                <select name="sort" aria-label="Sort products">
                    <?php foreach (['featured' => 'Featured', 'price-asc' => 'Price, low', 'price-desc' => 'Price, high', 'name' => 'Name'] as $key => $label): ?>
                        <option value="<?= e($key) ?>"<?= ($filters['sort'] ?? 'featured') === $key ? ' selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <noscript><button class="btn btn-small" type="submit">Apply</button></noscript>
        </form>
        <?php if (!$products): ?>
            <div class="empty">
                <h2>Nothing in that cut.</h2>
                <p>Try another texture, or look across the whole edit.</p>
                <a class="btn" href="<?= e(url('shop')) ?>">Clear filters</a>
            </div>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($products as $product): product_card($product); endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
