</main>
<footer class="site-footer">
    <div class="wrap footer-grid">
        <div class="footer-about">
            <a class="brand footer-brand" href="<?= e(url()) ?>" aria-label="firmlordhair">
                <?= file_get_contents(dirname(__DIR__) . '/assets/images/logo.svg') ?>
            </a>
            <p>Firmlordhair is your trusted destination for premium-quality hair and beauty essentials.<br>We offer a wide selection of raw and donor hair, Vietnamese, Chinese, and Cambodian hair, frontals, closures, packet weave-ons, hair care products</p>
        </div>
        <div>
            <h2>Shop</h2>
            <?php foreach (shop_nav() as $item): ?>
                <?php if ($item['kind'] === 'link'): ?>
                    <a href="<?= e(url(category_path(category_by($item['slug'])))) ?>"><?= e($item['name']) ?></a>
                <?php else: ?>
                    <a href="<?= e(url(category_group_path($item['group']))) ?>"><?= e($item['name']) ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div>
            <h2>Help</h2>
            <a href="<?= e(url('shipping')) ?>">Shipping & returns</a>
            <a href="<?= e(url('faq')) ?>">Questions</a>
            <a href="<?= e(url('contact')) ?>">Contact</a>
            <a href="<?= e(url('account')) ?>">Your orders</a>
        </div>
        <div class="footer-contact">
            <section>
                <h2>Store Address</h2>
                <p>Firmlordhair Studio<br>11, koseh Street Balogun Market Lagos-island.</p>
            </section>
            <section>
                <h2>Store Email</h2>
                <p><a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a><br><a href="mailto:ladyfirmlord@gmail.com">ladyfirmlord@gmail.com</a></p>
            </section>
            <section>
                <h2>Phone Number</h2>
                <p class="footer-phones"><a href="tel:+<?= e(SITE_PHONE_E164) ?>"><?= e(SITE_PHONE) ?></a><span aria-hidden="true">,</span><a href="tel:+2348035371154">08035371154</a></p>
            </section>
        </div>
    </div>
    <div class="wrap footer-base">
        <p>© <?= date('Y') ?> Firmlordhair</p>
    </div>
</footer>

<div class="scrim" data-close-bag></div>
<aside class="drawer" id="bag" aria-labelledby="bag-title">
    <div class="drawer-head">
        <h2 id="bag-title">Your bag</h2>
        <button type="button" data-close-bag>Close</button>
    </div>
    <?php $lines = cart_lines(); ?>
    <?php if (!$lines): ?>
        <p class="drawer-empty">Your bag is empty. The edit is waiting.</p>
        <a class="btn" href="<?= e(url('shop')) ?>">Shop the house</a>
    <?php else: ?>
        <ul class="drawer-lines">
            <?php foreach ($lines as $line): ?>
                <li>
                    <a href="<?= e(url('product/' . $line['product']['slug'])) ?>"><?= e($line['product']['name']) ?></a>
                    <p><?php
                        $bits = array_filter([$line['length'] !== '' ? $line['length'] . '"' : '', $line['color']]);
                        echo e(implode(' · ', $bits) . ' · ' . $line['qty']);
                    ?></p>
                    <strong><?= e(money($line['line'])) ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="drawer-foot">
            <p><span>Subtotal</span><strong><?= e(money(cart_subtotal())) ?></strong></p>
            <a class="btn" href="<?= e(url('cart')) ?>">View bag</a>
            <a class="btn btn-dark" href="<?= e(url('checkout')) ?>">Checkout</a>
        </div>
    <?php endif; ?>
</aside>

<div class="search-layer" id="search-layer" hidden>
    <form class="search-panel" action="<?= e(url('search')) ?>" method="get" role="search">
        <label for="q">Search</label>
        <input id="q" name="q" type="search" placeholder="Bob, burgundy, Brazilian, spray…" autofocus>
        <button class="btn" type="submit">Search</button>
        <button class="text-btn" type="button" data-close-search>Close</button>
    </form>
</div>

<a class="fab" href="<?= e(wa_link('Hello firmlordhair, I need help choosing a piece.')) ?>" target="_blank" rel="noopener" aria-label="WhatsApp">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12.04 2C6.58 2 2.15 6.4 2.15 11.83c0 1.74.46 3.44 1.34 4.94L2 22l5.39-1.41a10 10 0 0 0 4.65 1.18h.01c5.46 0 9.89-4.4 9.89-9.83C21.94 6.4 17.5 2 12.04 2zm5.76 13.94c-.24.68-1.4 1.3-1.94 1.38-.5.08-1.12.11-1.81-.11-.41-.14-.95-.31-1.64-.61-2.89-1.25-4.77-4.16-4.92-4.35-.14-.19-1.18-1.57-1.18-3 0-1.42.75-2.12 1.01-2.41.26-.29.57-.36.76-.36.19 0 .38 0 .55.01.18.01.41-.07.64.49.24.58.81 2 .88 2.15.07.14.12.31.02.5-.09.19-.14.31-.28.48-.14.17-.29.37-.42.5-.14.14-.28.29-.12.56.16.27.7 1.16 1.51 1.88 1.04.92 1.91 1.21 2.18 1.35.27.14.43.12.59-.07.16-.19.68-.79.86-1.06.18-.27.36-.22.6-.13.24.09 1.54.73 1.8.86.26.13.44.2.5.31.07.11.07.64-.17 1.32z"/></svg>
</a>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
