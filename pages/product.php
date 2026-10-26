<?php
declare(strict_types=1);
/** @var array $product */
/** @var array $related */
$related = $related ?? [];
$cat = category_by($product['category']);
$lengths = $product['lengths'] ?? [];
$minLength = $lengths ? (int) $lengths[0] : 0;
$quote = quote_price($product, $minLength ? (string) $minLength : null);
?>
<section class="page-hero">
    <div class="wrap">
        <?php
        $trail = [['Home', '/'], ['Shop', '/shop']];
        $catGroup = (string) ($cat['group'] ?? '');
        if ($cat && in_array($catGroup, category_menu_groups(), true)) {
            $groupMeta = category_group_meta($catGroup);
            if ($groupMeta) {
                $trail[] = [$groupMeta['name'], '/' . category_group_path($catGroup)];
            }
        }
        $trail[] = [$cat['name'] ?? 'Shop', '/' . category_path($cat)];
        $trail[] = [$product['name'], ''];
        crumbs($trail);
        ?>
    </div>
</section>
<section class="section section-tight">
    <div class="wrap product">
        <div class="gallery">
            <?php $frames = piece_frames($product); ?>
            <?php foreach ($frames as $index => $frame): ?>
                <div class="frame<?= $index === 0 ? ' is-on' : '' ?>" data-frame="<?= (int) $frame ?>">
                    <?= piece_media($product, (int) $frame) ?>
                </div>
            <?php endforeach; ?>
            <div class="thumbs">
                <?php foreach ($frames as $index => $frame): ?>
                    <button type="button" data-thumb="<?= (int) $frame ?>" class="<?= $index === 0 ? 'is-on' : '' ?>" aria-label="View <?= $index + 1 ?>">
                        <?= piece_media($product, (int) $frame) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="buy">
            <p class="kicker"><?= e($product['texture']) ?></p>
            <h1><?= e($product['name']) ?></h1>
            <p class="price price-live" data-base="<?= (int) $product['price'] ?>" data-compare="<?= (int) ($product['compare'] ?? 0) ?>" data-step="<?= (int) ($product['inch'] ?? 0) ?>" data-min="<?= $minLength ?>">
                <strong><?= e(money($quote['price'])) ?></strong>
                <?php if ($quote['compare'] > $quote['price']): ?>
                    <s><?= e(money($quote['compare'])) ?></s>
                <?php endif; ?>
            </p>
            <p class="lede"><?= e($product['story']) ?></p>
            <form method="post" action="<?= e(url(ltrim(request_path(), '/'))) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="slug" value="<?= e($product['slug']) ?>">
                <input type="hidden" name="back" value="<?= e(current_back()) ?>">
                <?php if ($lengths): ?>
                    <p class="kicker">Length</p>
                    <div class="choices">
                        <?php foreach ($lengths as $i => $length): ?>
                            <label class="choice">
                                <input type="radio" name="length" value="<?= (int) $length ?>"<?= $i === 0 ? ' checked' : '' ?>>
                                <span><?= (int) $length ?>"</span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($product['colors'])): ?>
                    <p class="kicker">Colour</p>
                    <div class="choices">
                        <?php foreach ($product['colors'] as $i => $color): ?>
                            <label class="choice">
                                <input type="radio" name="color" value="<?= e($color['name']) ?>"<?= $i === 0 ? ' checked' : '' ?>>
                                <span class="swatch" style="--swatch: <?= e($color['hex']) ?>" title="<?= e($color['name']) ?>"></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="buy-row">
                    <label class="kicker" for="qty">Qty</label>
                    <input class="qty" id="qty" name="qty" type="number" min="1" max="8" value="1">
                    <button class="btn" type="submit">Add to bag</button>
                </div>
            </form>
            <dl class="specs">
                <dt>Lace</dt><dd><?= e($product['lace']) ?></dd>
                <dt>Cap</dt><dd><?= e($product['cap']) ?></dd>
                <dt>Density</dt><dd><?= e($product['density']) ?></dd>
            </dl>
            <p><a class="text-btn" href="<?= e(wa_link('Hello firmlordhair, I am looking at ' . $product['name'])) ?>" target="_blank" rel="noopener">Ask about this piece</a></p>
        </div>
    </div>
</section>
<?php if ($related): ?>
<section class="section">
    <div class="wrap">
        <div class="section-head">
            <p class="eyebrow">Alongside it</p>
            <h2>From the same room.</h2>
        </div>
        <div class="grid">
            <?php foreach ($related as $item): product_card($item); endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
