<?php
declare(strict_types=1);
$lines = cart_lines();
$subtotal = cart_subtotal();
$delivery = shipping_fee($subtotal);
$person = customer() ?? [];
?>
<section class="page-hero">
    <div class="wrap">
        <?php crumbs([['Home', '/'], ['Bag', '/cart'], ['Checkout', '']]); ?>
        <h1>Checkout</h1>
        <p class="lede">Pay when the parcel arrives, or transfer before we dispatch. Nothing is charged on this page.</p>
    </div>
</section>
<section class="section section-tight">
    <div class="wrap checkout">
        <?php if (!$lines): ?>
            <div class="empty">
                <h2>Your bag is empty.</h2>
                <a class="btn" href="<?= e(url('shop')) ?>">Return to the shop</a>
            </div>
        <?php else: ?>
            <form class="form" method="post" action="<?= e(url('checkout')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="checkout">
                <input type="hidden" name="back" value="checkout">
                <div class="form-grid">
                    <?php
                    $fields = [
                        'name' => 'Full name',
                        'email' => 'Email',
                        'phone' => 'Phone',
                        'address' => 'Address',
                        'city' => 'City',
                    ];
                    foreach ($fields as $key => $label):
                        $error = field_error($key);
                        $type = $key === 'email' ? 'email' : 'text';
                        ?>
                        <label class="field<?= $error ? ' is-bad' : '' ?>">
                            <?= e($label) ?>
                            <input name="<?= e($key) ?>" type="<?= e($type) ?>" value="<?= e(old_value($key, (string) ($person[$key] ?? ''))) ?>" required>
                            <?php if ($error): ?><small><?= e($error) ?></small><?php endif; ?>
                        </label>
                    <?php endforeach; ?>
                    <label class="field<?= field_error('state') ? ' is-bad' : '' ?>">
                        State
                        <select name="state" required>
                            <option value="">Choose</option>
                            <?php foreach (states() as $state): ?>
                                <option value="<?= e($state) ?>"<?= old_value('state', 'Lagos') === $state ? ' selected' : '' ?>><?= e($state) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($err = field_error('state')): ?><small><?= e($err) ?></small><?php endif; ?>
                    </label>
                </div>
                <fieldset>
                    <legend class="kicker">Payment</legend>
                    <label class="choice"><input type="radio" name="payment" value="cod"<?= old_value('payment', 'cod') === 'cod' ? ' checked' : '' ?>><span>Cash on delivery</span></label>
                    <label class="choice"><input type="radio" name="payment" value="transfer"<?= old_value('payment') === 'transfer' ? ' checked' : '' ?>><span>Bank transfer</span></label>
                </fieldset>
                <label class="field">
                    Note for the studio
                    <textarea name="note" rows="3"><?= e(old_value('note')) ?></textarea>
                </label>
                <button class="btn" type="submit">Place order</button>
            </form>
            <aside class="summary">
                <h2>Your pieces</h2>
                <?php foreach ($lines as $line): ?>
                    <p><span><?= e($line['product']['name']) ?> × <?= (int) $line['qty'] ?></span><strong><?= e(money($line['line'])) ?></strong></p>
                <?php endforeach; ?>
                <p><span>Delivery</span><strong><?= $delivery === 0 ? 'Free' : e(money($delivery)) ?></strong></p>
                <p><span>Total</span><strong><?= e(money($subtotal + $delivery)) ?></strong></p>
                <p class="pay-note">Transfer details appear on the order page. Cash is collected when the rider arrives.</p>
            </aside>
        <?php endif; ?>
    </div>
</section>
