<section class="page-hero">
    <div class="narrow">
        <?php crumbs([['Home', '/'], ['Shipping', '']]); ?>
        <h1>Shipping and returns.</h1>
        <p class="lede">We pack hair so the wave survives the road, and we say clearly what can come back.</p>
    </div>
</section>
<section class="section section-tight">
    <div class="wrap prose-grid">
        <article class="prose">
            <h2>Delivery</h2>
            <p>Lagos orders placed before 2pm on a studio day leave the same afternoon when the rider has room. Other states go out the next studio morning. Delivery is <?= e(money(DELIVERY_FEE)) ?>, and it is free from <?= e(money(FREE_DELIVERY)) ?>.</p>
            <p>You get a message when the parcel is with the rider. Please have someone available at the address; hair does not sit well in a hot gatehouse.</p>
            <h2>Payment</h2>
            <p>Cash on delivery, or a transfer to the studio account shown on your order page. We dispatch transfers after the alert lands.</p>
        </article>
        <article class="prose">
            <h2>Returns</h2>
            <p>Unworn pieces, still in the wrap, can return within seven days. Installed units, cut wefts, and anything that has been coloured are final. Accessories that have been opened stay with you.</p>
            <p>Write to <?= e(SITE_EMAIL) ?> with your order number. We send the return steps the same studio day.</p>
            <a class="btn" href="<?= e(url('contact')) ?>">Ask about an order</a>
        </article>
    </div>
</section>
