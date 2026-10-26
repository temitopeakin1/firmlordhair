<section class="page-hero">
    <div class="wrap">
        <?php crumbs([['Home', '/'], ['Contact', '']]); ?>
        <h1>Talk to the studio.</h1>
        <p class="lede">Length questions, colour matches, and orders. We reply during studio hours.</p>
    </div>
</section>
<section class="section section-tight">
    <div class="wrap split">
        <form class="form" method="post" action="<?= e(url('contact')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="contact">
            <input type="hidden" name="back" value="contact">
            <?php foreach (['name' => 'Name', 'email' => 'Email'] as $key => $label):
                $error = field_error($key); ?>
                <label class="field<?= $error ? ' is-bad' : '' ?>">
                    <?= e($label) ?>
                    <input name="<?= e($key) ?>" type="<?= $key === 'email' ? 'email' : 'text' ?>" value="<?= e(old_value($key)) ?>" required>
                    <?php if ($error): ?><small><?= e($error) ?></small><?php endif; ?>
                </label>
            <?php endforeach; ?>
            <label class="field<?= field_error('message') ? ' is-bad' : '' ?>">
                Message
                <textarea name="message" rows="5" required><?= e(old_value('message')) ?></textarea>
                <?php if ($msg = field_error('message')): ?><small><?= e($msg) ?></small><?php endif; ?>
            </label>
            <button class="btn" type="submit">Send</button>
        </form>
        <aside class="summary">
            <h2>Visit or call</h2>
            <p>Firmlordhair Studio<br>11, koseh Street Balogun Market Lagos-island.</p>
            <p class="hours">Mon–Sat, 9:00–18:00</p>
            <p><a href="tel:+2348063349857">08063349857</a><br><a href="tel:+2348035371154">08035371154</a><br><a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></p>
            <a class="btn btn-dark" href="<?= e(wa_link('Hello firmlordhair')) ?>" target="_blank" rel="noopener">WhatsApp</a>
        </aside>
    </div>
</section>
