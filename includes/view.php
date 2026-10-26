<?php
declare(strict_types=1);

function product_card(array $product): void
{
    $href = url('product/' . $product['slug']);
    $badge = badge_of($product);
    $off = sale_off($product);
    $color = $product['colors'][0]['name'] ?? '';
    $length = isset($product['lengths'][0]) ? (string) $product['lengths'][0] : '';
    ?>
    <article class="card reveal">
        <a class="card-media" href="<?= e($href) ?>">
            <?php if ($badge !== ''): ?>
                <span class="badge"><?= e($badge) ?><?= $off > 0 ? ' · ' . $off . '%' : '' ?></span>
            <?php endif; ?>
            <?= piece_media($product) ?>
        </a>
        <div class="card-body">
            <p class="kicker"><?= e($product['texture']) ?></p>
            <h3><a href="<?= e($href) ?>"><?= e($product['name']) ?></a></h3>
            <p class="price">
                <?php if (!empty($product['lengths'])): ?><span>From </span><?php endif; ?>
                <?= e(money((int) $product['price'])) ?>
                <?php if (!empty($product['compare']) && $product['compare'] > $product['price']): ?>
                    <s><?= e(money((int) $product['compare'])) ?></s>
                <?php endif; ?>
            </p>
            <form method="post" action="<?= e(url(ltrim(request_path(), '/'))) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="slug" value="<?= e($product['slug']) ?>">
                <input type="hidden" name="length" value="<?= e($length) ?>">
                <input type="hidden" name="color" value="<?= e($color) ?>">
                <input type="hidden" name="qty" value="1">
                <input type="hidden" name="back" value="<?= e(current_back()) ?>">
                <button class="text-btn" type="submit">Add to bag</button>
            </form>
        </div>
    </article>
    <?php
}

function crumbs(array $items): void
{
    echo '<nav class="crumbs" aria-label="Breadcrumb"><ol>';
    $last = count($items) - 1;
    foreach ($items as $i => $item) {
        echo '<li>';
        if ($i !== $last && !empty($item[1])) {
            echo '<a href="' . e(url(ltrim($item[1], '/'))) . '">' . e($item[0]) . '</a>';
        } else {
            echo '<span>' . e($item[0]) . '</span>';
        }
        echo '</li>';
    }
    echo '</ol></nav>';
}

function stars(int $count): string
{
    return str_repeat('★', max(0, min(5, $count))) . str_repeat('☆', max(0, 5 - $count));
}

function old_value(string $key, string $fallback = ''): string
{
    $old = $_SESSION['old'] ?? [];
    if (!is_array($old) || !isset($old[$key])) {
        return $fallback;
    }
    return (string) $old[$key];
}

function field_error(string $key): string
{
    $errors = $_SESSION['errors'] ?? [];
    if (!is_array($errors) || empty($errors[$key])) {
        return '';
    }
    return (string) $errors[$key];
}

function take_form_state(): void
{
    unset($_SESSION['old'], $_SESSION['errors']);
}
