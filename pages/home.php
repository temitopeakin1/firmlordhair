<?php
declare(strict_types=1);
$featured = array_slice(featured_products(), 0, 4);
$sale = array_slice(sale_products(), 0, 4);
$notes = reviews();
$slides = [
    [asset('images/home-banner.jpg'), 'Woman lifting a lace wig in a bright room', 'Luxury hair of all types', 'Hair with a firm hold on beauty.', true],
    [asset('images/home-banner-2.jpg'), 'Woman in a blush silk dress with long body-wave hair', 'Raw, donor, Vietnamese, Chinese, Cambodia', 'Patterns that keep their shape.', false],
    [asset('images/home-banner-3.jpg'), 'Woman holding a straight weft in a bright room', 'Frontals, closures, and packet weavon', 'Full, silky, and slow to shed.', false],
];
?>
<section class="banner" aria-roledescription="carousel" aria-label="House edits">
    <div class="banner-track">
        <?php foreach ($slides as $slide): ?>
            <article class="banner-slide">
                <img src="<?= e($slide[0]) ?>" alt="<?= e($slide[1]) ?>">
                <div class="banner-copy">
                    <p><?= e($slide[2]) ?></p>
                    <?php if ($slide[4]): ?>
                        <h1 class="banner-title"><?= e($slide[3]) ?></h1>
                    <?php else: ?>
                        <p class="banner-title"><?= e($slide[3]) ?></p>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <div class="banner-dots">
        <?php foreach ($slides as $i => $slide): ?>
            <button type="button"<?= $i === 0 ? ' class="is-on" aria-current="true"' : '' ?> aria-label="Show slide <?= $i + 1 ?>"></button>
        <?php endforeach; ?>
    </div>
</section>
<div class="marquee" aria-hidden="true">
    <div class="marquee-track">
        <?php for ($i = 0; $i < 2; $i++): ?>
            <span>Raw hair</span><span>Donor hair</span><span>Vietnamese hair</span><span>Chinese luxury hair</span><span>Cambodia hair</span><span>Frontals</span><span>Closures</span><span>Packet weavon</span><span>Hair products</span><span>Salon equipment</span>
        <?php endfor; ?>
    </div>
</div>

<section class="section">
    <div class="wrap">
        <div class="section-head">
            <p class="eyebrow">The edit</p>
            <h2>Shop the house.</h2>
        </div>
        <div class="tiles tiles-rooms">
            <?php foreach (shop_rooms() as $room): ?>
                <a class="tile tile-<?= e($room['slug']) ?>" href="<?= e($room['href']) ?>">
                    <span><?= e($room['kicker']) ?></span>
                    <h3><?= e($room['name']) ?></h3>
                    <p><?= e($room['line']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-tight">
    <div class="wrap">
        <div class="section-head">
            <p class="eyebrow">In rotation</p>
            <h2>House favourites.</h2>
            <a href="<?= e(url('shop')) ?>">View all</a>
        </div>
        <div class="grid">
            <?php foreach ($featured as $product): product_card($product); endforeach; ?>
        </div>
    </div>
</section>

<section class="story">
    <div class="wrap story-grid">
        <figure class="story-panel">
            <img
                src="<?= e(asset('images/home-banner-2.jpg')) ?>"
                alt="Long body-wave luxury hair in soft studio light"
                width="900"
                height="1125"
            >
            <figcaption class="story-panel-copy">
                <p class="eyebrow">The House · Lagos</p>
                <p class="story-brand">Firmlordhair</p>
                <h2>Cut to sit. Made to stay.</h2>
                <p class="story-line">Luxury hair finished for installs that keep their shape - not just for the first wash.</p>
            </figcaption>
        </figure>
        <div class="prose">
            <p>We sell luxury hair of all types: raw and donor hair, Vietnamese, Chinese luxury, and Cambodian hair — chosen by origin and feel, not only by the label.</p>
            <p>Frontals, closures, packet weavon, hair products, and salon equipment sit beside the hair, so a Lagos install can leave complete and last.</p>
            <a class="text-btn" href="<?= e(url('about')) ?>">Read the house notes</a>
        </div>
    </div>
</section>

<?php if ($sale): ?>
<section class="section section-blush">
    <div class="wrap">
        <div class="section-head">
            <p class="eyebrow">This week</p>
            <h2>Big deals on a budget.</h2>
        </div>
        <div class="grid">
            <?php foreach ($sale as $product): product_card($product); endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="wrap">
        <div class="section-head reviews-head">
            <p class="eyebrow">Real hair. Real confidence.</p>
            <h2>Beautiful hair. Effortless confidence.</h2>
            <p class="reviews-lead">Don’t just take our word for it. See what our customers have to say.</p>
        </div>
        <ul class="review-highlights" aria-label="What customers notice">
            <?php foreach (review_highlights() as $highlight): ?>
                <li><?= e($highlight) ?></li>
            <?php endforeach; ?>
        </ul>
        <div class="reviews-rail">
            <div class="reviews" data-reviews-track tabindex="0" aria-label="Customer reviews">
                <?php foreach ($notes as $review): ?>
                    <figure class="review reveal" data-review-slide>
                        <div class="stars" aria-label="<?= (int) $review['stars'] ?> out of 5"><?= stars((int) $review['stars']) ?></div>
                        <blockquote>
                            <p class="review-quote"><?= e($review['quote']) ?></p>
                            <p class="review-body"><?= e($review['text']) ?></p>
                        </blockquote>
                        <figcaption>— <?= e($review['name']) ?>, <?= e($review['place']) ?></figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
            <div class="reviews-dots" data-reviews-dots aria-hidden="true">
                <?php foreach ($notes as $i => $review): ?>
                    <button type="button"<?= $i === 0 ? ' class="is-on"' : '' ?> aria-label="Show review <?= $i + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
