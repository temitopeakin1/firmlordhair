<?php
declare(strict_types=1);

function piece_color_lines(array $product): string
{
    $lines = [];
    foreach ($product['colors'] ?? [] as $color) {
        $lines[] = $color['name'] . ' | ' . $color['hex'];
    }
    return implode("\n", $lines);
}

function piece_edit_form(array $product = []): void
{
    $slug = $product['slug'] ?? '';
    $id = $slug !== '' ? $slug : 'new';
    ?>
    <form class="piece-edit" method="post" action="<?= e(url('studio/pieces')) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="studio-piece">
        <input type="hidden" name="back" value="/studio/pieces">
        <input type="hidden" name="slug" value="<?= e($slug) ?>">
        <label class="field">Name
            <input name="name" value="<?= e($product['name'] ?? '') ?>" required>
        </label>
        <label class="field">Room
            <select name="category">
                <?php foreach (categories() as $category): ?>
                    <option value="<?= e($category['slug']) ?>"<?= ($product['category'] ?? '') === $category['slug'] ? ' selected' : '' ?>><?= e($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="field">Texture
            <input name="texture" value="<?= e($product['texture'] ?? '') ?>" required>
        </label>
        <label class="field">Price (₦)
            <input name="price" inputmode="numeric" value="<?= (int) ($product['price'] ?? 0) ?: '' ?>" required>
        </label>
        <label class="field">Compare price (₦)
            <input name="compare" inputmode="numeric" value="<?= (int) ($product['compare'] ?? 0) ?>">
        </label>
        <label class="field">Stock
            <input name="stock" type="number" min="0" value="<?= (int) ($product['stock'] ?? 0) ?>">
        </label>
        <label class="field">Lengths, inches
            <input name="lengths" value="<?= e(implode(', ', $product['lengths'] ?? [])) ?>" placeholder="14, 16, 18">
        </label>
        <label class="field">Extra per inch (₦)
            <input name="inch" inputmode="numeric" value="<?= (int) ($product['inch'] ?? 0) ?>">
        </label>
        <label class="field">Badge
            <select name="badge">
                <?php foreach (['' => 'None', 'Sale' => 'Sale', 'New' => 'New'] as $value => $label): ?>
                    <option value="<?= e($value) ?>"<?= ($product['badge'] ?? '') === $value ? ' selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="field">Lace
            <input name="lace" value="<?= e($product['lace'] ?? '') ?>">
        </label>
        <label class="field">Density
            <input name="density" value="<?= e($product['density'] ?? '') ?>">
        </label>
        <label class="field">Cap
            <input name="cap" value="<?= e($product['cap'] ?? '') ?>">
        </label>
        <label class="field wide">Colours, one per line
            <textarea name="colors" rows="3" required><?= e(piece_color_lines($product)) ?></textarea>
        </label>
        <label class="field wide">Short line
            <input name="blurb" value="<?= e($product['blurb'] ?? '') ?>" required>
        </label>
        <label class="field wide">Story
            <textarea name="story" rows="4"><?= e($product['story'] ?? '') ?></textarea>
        </label>
        <label class="field wide">Shop card photo<?= $slug === '' ? '' : ' (optional — replaces photo 1)' ?>
            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp">
        </label>
        <label class="field wide">
            <input type="checkbox" name="featured"<?= !empty($product['featured']) ? ' checked' : '' ?>> Show on the home page
        </label>
        <button class="btn btn-small" type="submit"><?= $slug === '' ? 'Add piece' : 'Save piece' ?></button>
    </form>
    <?php
}

$authed = studio_ok();
?>
<section class="page-hero">
    <div class="narrow">
        <p class="eyebrow">Studio</p>
        <h1><?= $authed ? 'Pieces' : 'Studio desk' ?></h1>
        <?php if (!$authed): ?>
            <p class="lede">Sign in to edit the shop.</p>
            <form class="form" method="post" action="<?= e(url('studio')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="studio-login">
                <input type="hidden" name="back" value="/studio/pieces">
                <label class="field">Password
                    <input name="password" type="password" autocomplete="current-password" required>
                </label>
                <button class="btn" type="submit">Open the desk</button>
            </form>
        <?php else: ?>
            <p class="lede">Add name, price, description, and a shop card photo when you create a piece. Extra photos can be added on each piece below.</p>
        <?php endif; ?>
    </div>
</section>
<?php if ($authed): ?>
<section class="section section-tight">
    <div class="wrap">
        <nav class="desk-nav" aria-label="Studio">
            <a href="<?= e(url('studio')) ?>">Orders</a>
            <a class="is-on" href="<?= e(url('studio/pieces')) ?>">Pieces</a>
        </nav>
        <article class="photo-card">
            <div>
                <h2>New piece</h2>
                <?php piece_edit_form(); ?>
            </div>
        </article>
        <div class="photo-grid">
            <?php foreach (categories() as $category): ?>
                <h2><?= e($category['name']) ?></h2>
                <?php foreach (products() as $product): ?>
                    <?php if ($product['category'] !== $category['slug']) { continue; } ?>
                    <?php $photos = product_photos($product['slug']); ?>
                    <article class="photo-card">
                        <div class="photo-preview">
                            <?php if ($photos): ?>
                                <img src="<?= e(asset('images/products/' . $photos[array_key_first($photos)])) ?>" alt="">
                            <?php else: ?>
                                <?= piece_art($product) ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3><?= e($product['name']) ?></h3>
                            <form method="post" action="<?= e(url('studio/pieces')) ?>" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="studio-photo">
                                <input type="hidden" name="back" value="/studio/pieces">
                                <input type="hidden" name="slug" value="<?= e($product['slug']) ?>">
                                <label class="kicker" for="slot-<?= e($product['slug']) ?>">Place</label>
                                <select id="slot-<?= e($product['slug']) ?>" name="slot">
                                    <option value="0">Photo 1 · shop card</option>
                                    <option value="1">Photo 2</option>
                                    <option value="2">Photo 3</option>
                                </select>
                                <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required>
                                <button class="btn btn-small" type="submit">Save photo</button>
                            </form>
                            <?php if ($photos): ?>
                                <ul class="studio-items">
                                    <?php foreach ($photos as $slot => $filename): ?>
                                        <li>
                                            Photo <?= (int) $slot + 1 ?>
                                            <form method="post" action="<?= e(url('studio/pieces')) ?>">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="studio-photo-remove">
                                                <input type="hidden" name="back" value="/studio/pieces">
                                                <input type="hidden" name="slug" value="<?= e($product['slug']) ?>">
                                                <input type="hidden" name="slot" value="<?= (int) $slot ?>">
                                                <button class="text-btn" type="submit">Remove</button>
                                            </form>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <?php piece_edit_form($product); ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
