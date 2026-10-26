<?php
declare(strict_types=1);
$authed = studio_ok();
$orders = [];
$dbError = '';
if ($authed) {
    try {
        $orders = all_orders();
    } catch (Throwable $error) {
        $dbError = 'The order list could not be opened. Check that MySQL is running.';
    }
}
?>
<section class="page-hero">
    <div class="narrow">
        <p class="eyebrow">Studio</p>
        <h1><?= $authed ? 'Orders' : 'Studio desk' ?></h1>
        <?php if (!$authed): ?>
            <p class="lede">Orders placed on the shop are kept here.</p>
            <form class="form" method="post" action="<?= e(url('studio')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="studio-login">
                <input type="hidden" name="back" value="/studio">
                <label class="field">Password
                    <input name="password" type="password" autocomplete="current-password" required>
                </label>
                <button class="btn" type="submit">Open the desk</button>
            </form>
        <?php endif; ?>
    </div>
</section>
<?php if ($authed): ?>
<section class="section section-tight">
    <div class="wrap">
        <nav class="desk-nav" aria-label="Studio">
            <a class="is-on" href="<?= e(url('studio')) ?>">Orders</a>
            <a href="<?= e(url('studio/pieces')) ?>">Pieces</a>
        </nav>
        <form method="post" action="<?= e(url('studio')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="studio-logout">
            <input type="hidden" name="back" value="/studio">
            <button class="text-btn" type="submit">Lock the desk</button>
        </form>
        <?php if ($dbError !== ''): ?>
            <p><?= e($dbError) ?></p>
        <?php elseif (!$orders): ?>
            <p>No orders yet. They appear here after checkout.</p>
        <?php else: ?>
            <div class="studio-list">
                <?php foreach ($orders as $order): ?>
                    <article class="studio-card">
                        <header>
                            <h2><a href="<?= e(url('order/' . $order['id'])) ?>"><?= e($order['id']) ?></a></h2>
                            <strong><?= e(money((int) $order['total'])) ?></strong>
                        </header>
                        <p class="studio-meta"><?= e($order['placed']) ?> · <?= e($order['customer']['payment'] === 'transfer' ? 'Bank transfer' : 'Cash on delivery') ?></p>
                        <p><?= e($order['customer']['name']) ?><br><?= e($order['customer']['phone']) ?> · <?= e($order['customer']['email']) ?><br><?= e($order['customer']['address']) ?>, <?= e($order['customer']['city']) ?>, <?= e($order['customer']['state']) ?></p>
                        <?php if ($order['customer']['note'] !== ''): ?>
                            <p class="studio-meta"><?= e($order['customer']['note']) ?></p>
                        <?php endif; ?>
                        <ul class="studio-items">
                            <?php foreach ($order['items'] as $item): ?>
                                <li><?= e($item['name']) ?> × <?= (int) $item['qty'] ?><?php
                                    $bits = array_filter([$item['length'] !== '' ? $item['length'] . '"' : '', $item['color']]);
                                    echo $bits ? ' · ' . e(implode(' · ', $bits)) : '';
                                ?> — <?= e(money((int) $item['line'])) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <form class="studio-actions" method="post" action="<?= e(url('studio')) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="studio-status">
                            <input type="hidden" name="back" value="studio">
                            <input type="hidden" name="id" value="<?= e($order['id']) ?>">
                            <label class="kicker" for="status-<?= e($order['id']) ?>">Status</label>
                            <select id="status-<?= e($order['id']) ?>" name="status">
                                <?php foreach (order_statuses() as $status): ?>
                                    <option value="<?= e($status) ?>"<?= $order['status'] === $status ? ' selected' : '' ?>><?= e($status) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-small" type="submit">Save</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
