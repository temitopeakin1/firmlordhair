<?php
declare(strict_types=1);
$lines = cart_lines();
$subtotal = cart_subtotal();
$delivery = shipping_fee($subtotal);
?>
<section class="page-hero">
    <div class="wrap">
        <?php crumbs([['Home', '/'], ['Bag', '']]); ?>
        <h1>Your bag</h1>
    </div>
</section>
<section class="section section-tight">
    <div class="wrap<?= $lines ? ' split' : '' ?>">
        <?php if (!$lines): ?>
            <div class="empty">
                <h2>Nothing here yet.</h2>
                <p>The edit is still on the shop floor.</p>
                <a class="btn" href="<?= e(url('shop')) ?>">Shop the house</a>
            </div>
        <?php else: ?>
            <form method="post" action="<?= e(url('cart')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="back" value="<?= e(current_back()) ?>">
                <?php foreach ($lines as $line): ?>
                    <article class="cart-line">
                        <a class="cart-art" href="<?= e(url('product/' . $line['product']['slug'])) ?>"><?= piece_media($line['product']) ?></a>
                        <div>
                            <h3><a href="<?= e(url('product/' . $line['product']['slug'])) ?>"><?= e($line['product']['name']) ?></a></h3>
                            <p class="kicker"><?php
                                $bits = array_filter([$line['length'] !== '' ? $line['length'] . '"' : '', $line['color']]);
                                echo e($bits ? implode(' · ', $bits) : $line['product']['texture']);
                            ?></p>
                            <label class="kicker" for="qty-<?= e($line['key']) ?>">Qty</label>
                            <input class="qty" id="qty-<?= e($line['key']) ?>" type="number" min="0" max="8" name="qty[<?= e($line['key']) ?>]" value="<?= (int) $line['qty'] ?>">
                        </div>
                        <strong><?= e(money($line['line'])) ?></strong>
                    </article>
                <?php endforeach; ?>
                <button class="text-btn" type="submit">Update quantities</button>
            </form>
            <aside class="summary">
                <h2>Summary</h2>
                <p><span>Subtotal</span><strong><?= e(money($subtotal)) ?></strong></p>
                <p><span>Delivery</span><strong><?= $delivery === 0 ? 'Free' : e(money($delivery)) ?></strong></p>
                <p><span>Total</span><strong><?= e(money($subtotal + $delivery)) ?></strong></p>
                <p class="kicker">Free delivery from <?= e(money(FREE_DELIVERY)) ?>.</p>
                <a class="btn" href="<?= e(url('checkout')) ?>">Checkout</a>
            </aside>
        <?php endif; ?>
    </div>
</section>
