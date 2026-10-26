(function () {
    const body = document.body;

    const menu = document.querySelector('.nav-toggle');
    const nav = document.getElementById('site-nav');
    function setMenu(open) {
        if (!menu || !nav) return;
        nav.classList.toggle('is-open', open);
        menu.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    if (menu && nav) {
        function isMobileNav() {
            return window.matchMedia('(max-width: 1100px)').matches;
        }

        function setItemOpen(item, open) {
            item.classList.toggle('is-open', open);
            const btn = item.querySelector(':scope > .nav-sub-toggle');
            if (btn) btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (!open) {
                item.querySelectorAll('.nav-item.has-sub.is-open').forEach(function (child) {
                    setItemOpen(child, false);
                });
            }
        }

        function clearOpenMenus(root) {
            (root || nav).querySelectorAll('.nav-item.has-sub.is-open').forEach(function (item) {
                item.classList.remove('is-open');
                const btn = item.querySelector(':scope > .nav-sub-toggle');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            });
        }

        menu.addEventListener('click', function () {
            const open = !nav.classList.contains('is-open');
            if (!open) clearOpenMenus();
            setMenu(open);
        });

        nav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (!isMobileNav()) return;
                clearOpenMenus();
                setMenu(false);
            });
        });

        nav.querySelectorAll('.nav-sub-toggle').forEach(function (button) {
            button.addEventListener('click', function (event) {
                if (!isMobileNav()) return;
                event.preventDefault();
                event.stopPropagation();
                const item = button.closest('.nav-item.has-sub');
                if (!item || item.querySelector(':scope > .nav-sub-toggle') !== button) return;

                const parent = item.parentElement;
                const willOpen = !item.classList.contains('is-open');

                // Close sibling accordions at this level only
                Array.from(parent.children).forEach(function (other) {
                    if (other === item || !other.classList || !other.classList.contains('has-sub')) return;
                    setItemOpen(other, false);
                });

                setItemOpen(item, willOpen);

                if (willOpen) {
                    requestAnimationFrame(function () {
                        item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    });
                }
            });
        });

        window.addEventListener('resize', function () {
            if (!isMobileNav()) {
                setMenu(false);
                clearOpenMenus();
            }
        });
    }

    function setBag(open) {
        body.classList.toggle('bag-open', open);
        const bag = document.getElementById('bag');
        if (bag) bag.setAttribute('aria-hidden', open ? 'false' : 'true');
    }

    document.querySelectorAll('[data-open-bag]').forEach(function (button) {
        button.addEventListener('click', function () { setBag(true); });
    });
    document.querySelectorAll('[data-close-bag]').forEach(function (button) {
        button.addEventListener('click', function () { setBag(false); });
    });
    if (body.classList.contains('bag-open')) setBag(true);

    const search = document.getElementById('search-layer');
    const searchInput = search ? search.querySelector('input') : null;
    document.querySelectorAll('[data-open-search]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (!search) return;
            search.hidden = false;
            if (searchInput) searchInput.focus();
        });
    });
    document.querySelectorAll('[data-close-search]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (search) search.hidden = true;
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            setBag(false);
            if (search) search.hidden = true;
            setMenu(false);
        }
    });

    const price = document.querySelector('.price-live');
    const lengthInputs = document.querySelectorAll('input[name="length"]');
    function naira(amount) {
        return '₦' + Math.round(amount).toLocaleString('en-NG');
    }
    function paintPrice() {
        if (!price) return;
        const base = Number(price.dataset.base || 0);
        const compare = Number(price.dataset.compare || 0);
        const step = Number(price.dataset.step || 0);
        const min = Number(price.dataset.min || 0);
        const chosen = document.querySelector('input[name="length"]:checked');
        const inches = chosen ? Number(chosen.value) : min;
        const extra = min ? (inches - min) * step : 0;
        const now = base + extra;
        const was = compare > 0 ? compare + extra : 0;
        price.innerHTML = '<strong>' + naira(now) + '</strong>' + (was > now ? '<s>' + naira(was) + '</s>' : '');
    }
    lengthInputs.forEach(function (input) {
        input.addEventListener('change', paintPrice);
    });

    document.querySelectorAll('[data-thumb]').forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            const id = thumb.getAttribute('data-thumb');
            document.querySelectorAll('[data-thumb]').forEach(function (item) {
                item.classList.toggle('is-on', item === thumb);
            });
            document.querySelectorAll('[data-frame]').forEach(function (frame) {
                frame.classList.toggle('is-on', frame.getAttribute('data-frame') === id);
            });
        });
    });

    const banner = document.querySelector('.banner');
    const track = banner ? banner.querySelector('.banner-track') : null;
    const slides = banner ? banner.querySelectorAll('.banner-slide') : [];
    const dots = banner ? banner.querySelectorAll('.banner-dots button') : [];
    if (banner && track && slides.length > 1) {
        let index = 0;
        let timer = 0;
        const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
        function go(next) {
            index = (next + slides.length) % slides.length;
            track.style.transform = 'translateX(' + (-index * 100) + '%)';
            slides.forEach(function (slide, i) {
                slide.setAttribute('aria-hidden', i === index ? 'false' : 'true');
            });
            dots.forEach(function (dot, i) {
                const on = i === index;
                dot.classList.toggle('is-on', on);
                if (on) dot.setAttribute('aria-current', 'true');
                else dot.removeAttribute('aria-current');
            });
        }
        function stop() {
            window.clearInterval(timer);
        }
        function start() {
            stop();
            if (motion.matches) return;
            timer = window.setInterval(function () { go(index + 1); }, 5000);
        }
        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () {
                go(i);
                start();
            });
        });
        let startX = 0;
        banner.addEventListener('touchstart', function (event) {
            startX = event.changedTouches[0].clientX;
        }, { passive: true });
        banner.addEventListener('touchend', function (event) {
            const delta = event.changedTouches[0].clientX - startX;
            if (Math.abs(delta) < 40) return;
            go(index + (delta < 0 ? 1 : -1));
            start();
        }, { passive: true });
        banner.addEventListener('mouseenter', stop);
        banner.addEventListener('mouseleave', start);
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) stop();
            else start();
        });
        go(0);
        start();
    }

    const reviewsTrack = document.querySelector('[data-reviews-track]');
    const reviewSlides = reviewsTrack ? reviewsTrack.querySelectorAll('[data-review-slide]') : [];
    const reviewDots = document.querySelectorAll('[data-reviews-dots] button');
    const reviewsQuery = window.matchMedia('(max-width: 980px)');
    if (reviewsTrack && reviewSlides.length > 1) {
        let reviewIndex = 0;
        let reviewTimer = 0;
        const motion = window.matchMedia('(prefers-reduced-motion: reduce)');

        function reviewCardWidth() {
            const card = reviewSlides[0];
            const styles = window.getComputedStyle(reviewsTrack);
            const gap = parseFloat(styles.columnGap || styles.gap || '0') || 0;
            return card.getBoundingClientRect().width + gap;
        }

        function goReview(next) {
            if (!reviewsQuery.matches) return;
            reviewIndex = (next + reviewSlides.length) % reviewSlides.length;
            reviewsTrack.scrollTo({
                left: reviewCardWidth() * reviewIndex,
                behavior: motion.matches ? 'auto' : 'smooth',
            });
            reviewDots.forEach(function (dot, i) {
                dot.classList.toggle('is-on', i === reviewIndex);
            });
        }

        function stopReviews() {
            window.clearInterval(reviewTimer);
        }

        function startReviews() {
            stopReviews();
            if (!reviewsQuery.matches || motion.matches) return;
            reviewTimer = window.setInterval(function () {
                goReview(reviewIndex + 1);
            }, 4200);
        }

        reviewDots.forEach(function (dot, i) {
            dot.addEventListener('click', function () {
                goReview(i);
                startReviews();
            });
        });

        let reviewTouch = 0;
        reviewsTrack.addEventListener('touchstart', function (event) {
            reviewTouch = event.changedTouches[0].clientX;
            stopReviews();
        }, { passive: true });
        reviewsTrack.addEventListener('touchend', function (event) {
            const delta = event.changedTouches[0].clientX - reviewTouch;
            if (Math.abs(delta) >= 40) {
                goReview(reviewIndex + (delta < 0 ? 1 : -1));
            }
            startReviews();
        }, { passive: true });

        reviewsTrack.addEventListener('scroll', function () {
            if (!reviewsQuery.matches) return;
            const width = reviewCardWidth() || 1;
            const nearest = Math.round(reviewsTrack.scrollLeft / width);
            reviewIndex = Math.max(0, Math.min(reviewSlides.length - 1, nearest));
            reviewDots.forEach(function (dot, i) {
                dot.classList.toggle('is-on', i === reviewIndex);
            });
        }, { passive: true });

        reviewsQuery.addEventListener('change', function () {
            if (reviewsQuery.matches) {
                goReview(0);
                startReviews();
            } else {
                stopReviews();
                reviewsTrack.scrollLeft = 0;
            }
        });

        if (reviewsQuery.matches) {
            goReview(0);
            startReviews();
        }
    }

    document.querySelectorAll('form[data-auto-submit]').forEach(function (form) {
        form.querySelectorAll('select').forEach(function (select) {
            select.addEventListener('change', function () {
                form.requestSubmit ? form.requestSubmit() : form.submit();
            });
        });
    });

    (function initScrollReveals() {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const root = document.getElementById('content') || document.body;

        function mark(el) {
            if (!el || el.nodeType !== 1 || el.classList.contains('reveal')) return;
            el.classList.add('reveal');
        }

        // Consistent targets across pages (skip hero/banner above the fold)
        root.querySelectorAll([
            '.section-head',
            '.tile',
            '.story-panel',
            '.story-grid > .prose',
            '.review-highlights > li',
            '.page-hero > .wrap > :not(.crumbs)',
            '.page-hero > .narrow > :not(.crumbs)',
            '.prose-grid > *',
            '.empty',
        ].join(',')).forEach(mark);

        // Stagger siblings in common grids
        root.querySelectorAll('.grid, .tiles, .review-highlights, .reviews').forEach(function (group) {
            Array.prototype.forEach.call(group.children, function (child, index) {
                mark(child);
                child.style.setProperty('--reveal-delay', Math.min(index, 8) * 70 + 'ms');
            });
        });

        // Soft stagger inside section heads (eyebrow → title → link)
        root.querySelectorAll('.section-head').forEach(function (head) {
            Array.prototype.forEach.call(head.children, function (child, index) {
                child.style.setProperty('--reveal-delay', index * 90 + 'ms');
                mark(child);
            });
            // Head itself shouldn't also fade as a block if children reveal
            head.classList.remove('reveal');
        });

        const reveals = root.querySelectorAll('.reveal');
        if (!reveals.length) return;

        if (reduceMotion || !('IntersectionObserver' in window)) {
            reveals.forEach(function (node) { node.classList.add('in'); });
            return;
        }

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('in');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });

        reveals.forEach(function (node) { observer.observe(node); });
    })();
})();
