<?php
declare(strict_types=1);
$person = customer();
$orders = [];
if ($person) {
    try {
        $orders = orders_for_email((string) $person['email']);
    } catch (Throwable $error) {
        $orders = [];
    }
}
?>
<section class="page-hero">
    <div class="wrap">
        <?php crumbs([['Home', '/'], ['Account', '']]); ?>
        <h1><?= $person ? 'Hello, ' . e($person['name']) : 'Your account' ?></h1>
        <p class="lede">Sign in with the email from checkout to see orders saved for that address.</p>
    </div>
</section>
<section class="section section-tight">
    <div class="wrap account">
        <?php if (!$person): ?>
            <div class="empty">
                <h2>Sign in to see this visit’s orders.</h2>
                <p>Use the name and email you checked out with.</p>
                <a class="btn" href="<?= e(url('login')) ?>">Sign in</a>
                <a class="btn btn-ghost" href="<?= e(url('register')) ?>">Create details</a>
            </div>
        <?php else: ?>
            <div class="account-card">
                <h2>Details</h2>
                <p><?= e($person['name']) ?><br><?= e($person['email']) ?><?php if (!empty($person['phone'])): ?><br><?= e($person['phone']) ?><?php endif; ?></p>
                <form method="post" action="<?= e(url('account')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="logout">
                    <input type="hidden" name="back" value="account">
                    <button class="text-btn" type="submit">Sign out</button>
                </form>
            </div>
            <div>
                <h2>Orders</h2>
                <?php if (!$orders): ?>
                    <p>No saved orders for this email yet.</p>
                    <a class="btn" href="<?= e(url('shop')) ?>">Shop the edit</a>
                <?php else: ?>
                    <ul class="order-list">
                        <?php foreach ($orders as $order): ?>
                            <li>
                                <a href="<?= e(url('order/' . $order['id'])) ?>"><strong><?= e($order['id']) ?></strong></a>
                                <span><?= e($order['placed']) ?></span>
                                <span><?= e($order['status']) ?></span>
                                <strong><?= e(money((int) $order['total'])) ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
