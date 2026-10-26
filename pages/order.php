<?php
declare(strict_types=1);
/** @var array|null $order */
if (!$order): ?>
<section class="page-hero">
    <div class="narrow">
        <h1>We cannot find that order.</h1>
        <p class="lede">Check the order number, or look it up from the account you checked out with.</p>
        <a class="btn" href="<?= e(url('account')) ?>">Your account</a>
    </div>
</section>
<?php else: ?>
<section class="page-hero">
    <div class="wrap">
        <?php crumbs([['Home', '/'], ['Account', '/account'], [$order['id'], '']]); ?>
        <p class="eyebrow">Order placed</p>
        <h1><?= e($order['id']) ?></h1>
        <p class="lede">Thank you, <?= e($order['customer']['name']) ?>. This order is saved for the studio.</p>
    </div>
</section>
<section class="section section-tight">
    <div class="wrap order-layout">
        <div>
            <?php foreach ($order['items'] as $item): ?>
                <?php $piece = product_by((string) $item['slug']); ?>
                <article class="cart-line">
                    <div class="cart-art"><?= $piece ? piece_media($piece) : '' ?></div>
                    <div>
                        <h3><?= e($item['name']) ?></h3>
                        <p class="kicker"><?php
                            $bits = array_filter([$item['length'] !== '' ? $item['length'] . '"' : '', $item['color'], '× ' . $item['qty']]);
                            echo e(implode(' · ', $bits));
                        ?></p>
                    </div>
                    <strong><?= e(money((int) $item['line'])) ?></strong>
                </article>
            <?php endforeach; ?>
        </div>
        <aside class="summary">
            <h2><?= $order['customer']['payment'] === 'transfer' ? 'Transfer' : 'Cash on delivery' ?></h2>
            <?php if ($order['customer']['payment'] === 'transfer'): ?>
                <p>Send <?= e(money((int) $order['total'])) ?> to<br><strong>0123456789</strong><br>Demo Bank · firmlordhair<br>Use <?= e($order['id']) ?> as the narration.</p>
            <?php else: ?>
                <p>Have <?= e(money((int) $order['total'])) ?> ready when the rider arrives.</p>
            <?php endif; ?>
            <p><?= e($order['customer']['address']) ?><br><?= e($order['customer']['city']) ?>, <?= e($order['customer']['state']) ?></p>
            <p><span>Status</span><strong><?= e($order['status']) ?></strong></p>
            <a class="btn" href="<?= e(url('shop')) ?>">Continue shopping</a>
        </aside>
    </div>
</section>
<?php endif; ?>
