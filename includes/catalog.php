<?php
declare(strict_types=1);

function categories(): array
{
    return [
        [
            'slug' => 'wigs',
            'name' => 'Wigs',
            'kicker' => 'Ready to wear',
            'line' => 'Ready-to-wear, glueless, lace, bob, curly, straight, and body wave wigs.',
            'group' => 'type',
        ],
        [
            'slug' => 'weaves-and-bundles',
            'name' => 'Weaves & Bundles',
            'kicker' => 'Bundles',
            'line' => 'Straight, body wave, deep wave, curly, and kinky weaves and bundles.',
            'group' => 'type',
        ],
        [
            'slug' => 'brazilian',
            'name' => 'Brazilian Hair',
            'kicker' => 'By origin',
            'line' => 'Brazilian wigs, bundles, closures, and frontals.',
            'group' => 'origin',
        ],
        [
            'slug' => 'raw-hair',
            'name' => 'Raw Hair',
            'kicker' => 'By origin',
            'line' => 'Raw hair wigs, bundles, closures, and frontals.',
            'group' => 'origin',
        ],
        [
            'slug' => 'donor',
            'name' => 'Donor Hair',
            'kicker' => 'By origin',
            'line' => 'Donor hair wigs, bundles, closures, and frontals.',
            'group' => 'origin',
        ],
        [
            'slug' => 'vietnamese',
            'name' => 'Vietnamese Hair',
            'kicker' => 'By origin',
            'line' => 'Vietnamese wigs, bundles, closures, and frontals.',
            'group' => 'origin',
        ],
        [
            'slug' => 'chinese',
            'name' => 'Chinese Hair',
            'kicker' => 'By origin',
            'line' => 'Chinese wigs, bundles, closures, and frontals.',
            'group' => 'origin',
        ],
        [
            'slug' => 'cambodia',
            'name' => 'Cambodian Hair',
            'kicker' => 'By origin',
            'line' => 'Cambodian wigs, bundles, closures, and frontals.',
            'group' => 'origin',
        ],
        [
            'slug' => 'frontals',
            'name' => 'Frontals',
            'kicker' => 'Lace pieces',
            'line' => '13×4, 13×6, HD lace, and transparent lace frontals.',
            'group' => 'piece',
        ],
        [
            'slug' => 'closures',
            'name' => 'Closures',
            'kicker' => 'Lace pieces',
            'line' => '4×4, 5×5, 6×6, HD lace, and transparent lace closures.',
            'group' => 'piece',
        ],
        [
            'slug' => 'salon-essentials',
            'name' => 'Salon Essentials',
            'kicker' => 'Tools & care',
            'line' => 'Glues, removers, caps, bands, combs, brushes, sprays, and treatments.',
            'group' => 'care',
        ],
    ];
}

function category_by(string $slug): ?array
{
    foreach (categories() as $category) {
        if ($category['slug'] === $slug) {
            return $category;
        }
    }
    return null;
}

function categories_in_group(string $group): array
{
    return array_values(array_filter(
        categories(),
        static fn (array $category): bool => ($category['group'] ?? '') === $group
    ));
}

/** Groups that appear as submenus under Categories (nested in the URL). */
function category_menu_groups(): array
{
    return ['origin', 'piece'];
}

function category_path(?array $category): string
{
    if ($category === null) {
        return 'shop';
    }
    $slug = (string) ($category['slug'] ?? '');
    $group = (string) ($category['group'] ?? '');
    if ($slug !== '' && in_array($group, category_menu_groups(), true)) {
        return 'categories/' . $group . '/' . $slug;
    }
    return $slug !== '' ? 'categories/' . $slug : 'shop';
}

function category_group_path(string $group): string
{
    if (in_array($group, category_menu_groups(), true)) {
        return 'categories/' . $group;
    }
    return 'shop?group=' . rawurlencode($group);
}

/** Simplified storefront navigation (recommended browse structure). */
function shop_nav(): array
{
    return [
        ['kind' => 'link', 'slug' => 'wigs', 'name' => 'Wigs'],
        ['kind' => 'link', 'slug' => 'weaves-and-bundles', 'name' => 'Weaves & Bundles'],
        [
            'kind' => 'menu',
            'id' => 'nav-origin',
            'name' => 'Hair by Origin',
            'group' => 'origin',
        ],
        [
            'kind' => 'menu',
            'id' => 'nav-lace',
            'name' => 'Frontals & Closures',
            'group' => 'piece',
        ],
        ['kind' => 'link', 'slug' => 'salon-essentials', 'name' => 'Salon Essentials'],
    ];
}

/** Home “rooms” — five clear entry points, not every leaf category. */
function shop_rooms(): array
{
    return [
        [
            'slug' => 'wigs',
            'name' => 'Wigs',
            'kicker' => 'Product type',
            'line' => 'Ready-to-wear, glueless, lace, bob, curly, straight, and body wave.',
            'href' => url('categories/wigs'),
        ],
        [
            'slug' => 'weaves-and-bundles',
            'name' => 'Weaves & Bundles',
            'kicker' => 'Product type',
            'line' => 'Straight, body wave, deep wave, curly, and kinky weaves.',
            'href' => url('categories/weaves-and-bundles'),
        ],
        [
            'slug' => 'origin',
            'name' => 'Hair by Origin',
            'kicker' => 'Origins',
            'line' => 'Brazilian, raw, donor, Vietnamese, Chinese, and Cambodian hair.',
            'href' => url(category_group_path('origin')),
        ],
        [
            'slug' => 'lace',
            'name' => 'Frontals & Closures',
            'kicker' => 'Lace pieces',
            'line' => '13×4 and 13×6 frontals, plus 4×4 to 6×6 closures.',
            'href' => url(category_group_path('piece')),
        ],
        [
            'slug' => 'salon-essentials',
            'name' => 'Salon Essentials',
            'kicker' => 'Tools & care',
            'line' => 'Glues, removers, caps, bands, brushes, sprays, and treatments.',
            'href' => url('categories/salon-essentials'),
        ],
    ];
}

function category_group_meta(string $group): ?array
{
    return match ($group) {
        'origin' => [
            'name' => 'Hair by Origin',
            'line' => 'Browse Brazilian, raw, donor, Vietnamese, Chinese, and Cambodian hair.',
        ],
        'piece' => [
            'name' => 'Frontals & Closures',
            'line' => 'Lace frontals and closures in HD and transparent options.',
        ],
        'type' => [
            'name' => 'Wigs & Weaves',
            'line' => 'Shop by product type.',
        ],
        'care' => [
            'name' => 'Salon Essentials',
            'line' => 'Tools and care for installs that last.',
        ],
        default => null,
    };
}

function products(bool $refresh = false): array
{
    static $items = null;
    if ($refresh) {
        $items = null;
    }
    if ($items !== null) {
        return $items;
    }
    try {
        ensure_product_rows();
        $items = products_from_db();
        if ($items) {
            return $items;
        }
    } catch (Throwable $error) {
        $items = null;
    }
    $items = catalog_seed();
    return $items;
}

function catalog_seed(): array
{
    return [
        [
            'slug' => 'firm-line-wig',
            'name' => 'The Firm Line Wig',
            'category' => 'wigs',
            'texture' => 'Straight',
            'price' => 185000,
            'compare' => 243000,
            'badge' => 'Sale',
            'featured' => true,
            'stock' => 6,
            'blurb' => 'Bone-straight glueless lace with a quiet middle part.',
            'story' => 'The Firm Line is the piece the house is named for. Double-drawn straight hair, a 5×5 HD lace, and a hairline that is already plucked. It sits flat without glue and keeps its silk from the first wear through the next wash.',
            'wash' => '#FFF0F6',
            'hair' => '#1C1614',
            'lengths' => [14, 16, 18, 20, 22],
            'inch' => 7000,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#161311'],
                ['name' => 'Soft brown', 'hex' => '#4A342C'],
            ],
            'lace' => '5×5 HD',
            'density' => '200%',
            'cap' => 'Glueless, adjustable band',
        ],
        [
            'slug' => 'chestnut-blunt-bob',
            'name' => 'Chestnut Blunt Bob',
            'category' => 'wigs',
            'texture' => 'Straight',
            'price' => 66000,
            'compare' => 98000,
            'badge' => 'Sale',
            'featured' => true,
            'stock' => 9,
            'blurb' => 'A chin-grazing bob with a melted hairline.',
            'story' => 'Cut blunt, coloured chestnut through the weft, and finished so the ends do not need a trim on arrival. The 2×6 lace melts at the temples. Wear it the day it lands.',
            'wash' => '#F8EBE3',
            'hair' => '#5C3A2E',
            'lengths' => [10, 12, 14],
            'inch' => 5000,
            'colors' => [
                ['name' => 'Chestnut', 'hex' => '#5C3A2E'],
                ['name' => 'Natural black', 'hex' => '#161311'],
            ],
            'lace' => '2×6',
            'density' => '180%',
            'cap' => 'Glueless bob cap',
        ],
        [
            'slug' => 'burgundy-after-dark',
            'name' => 'Burgundy After Dark',
            'category' => 'wigs',
            'texture' => 'Straight',
            'price' => 145000,
            'compare' => 198000,
            'badge' => 'New',
            'featured' => true,
            'stock' => 4,
            'blurb' => '99J burgundy, straight, and already parted.',
            'story' => 'A deep wine straight wig for evenings that run long. The colour is even from root to tip, the lace is HD, and the part is set so you are not negotiating with a comb in the car.',
            'wash' => '#FDE8F1',
            'hair' => '#6E1E3C',
            'lengths' => [16, 18, 20, 22],
            'inch' => 8000,
            'colors' => [
                ['name' => '99J burgundy', 'hex' => '#6E1E3C'],
            ],
            'lace' => '5×5 closure',
            'density' => '180%',
            'cap' => 'Glueless, combs and strap',
        ],
        [
            'slug' => 'harbour-loose-wave',
            'name' => 'Harbour Loose Wave',
            'category' => 'wigs',
            'texture' => 'Loose wave',
            'price' => 195000,
            'compare' => 235000,
            'badge' => 'Sale',
            'featured' => false,
            'stock' => 5,
            'blurb' => 'A frontal wave that still looks like a blowout.',
            'story' => 'Loose enough to move, polished enough for work. The 13×4 frontal gives you a real hairline, and the wave is set so it does not collapse into frizz by afternoon.',
            'wash' => '#F4EFEA',
            'hair' => '#2A211C',
            'lengths' => [16, 18, 20, 22],
            'inch' => 8000,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#1A140F'],
                ['name' => 'Chestnut', 'hex' => '#6B3E2E'],
            ],
            'lace' => '13×4 frontal',
            'density' => '180%',
            'cap' => 'Glueless frontal cap',
        ],
        [
            'slug' => 'palm-body-wave',
            'name' => 'Palm Body Wave Bundle',
            'category' => 'weaves-and-bundles',
            'texture' => 'Body wave',
            'price' => 28000,
            'compare' => 42000,
            'badge' => 'Sale',
            'featured' => true,
            'stock' => 20,
            'blurb' => 'One 100g bundle with a soft S pattern.',
            'story' => 'A single double-weft bundle for a fill-in or a full install if you take three. The body wave is relaxed, not crunchy, and it finds its pattern again after a wash.',
            'wash' => '#FFF6F0',
            'hair' => '#3A2A22',
            'lengths' => [14, 16, 18, 20, 22, 24],
            'inch' => 2500,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#1A140F'],
                ['name' => 'Dark brown', 'hex' => '#3A2A22'],
            ],
            'lace' => 'Bundle only',
            'density' => '100g',
            'cap' => 'Double weft',
        ],
        [
            'slug' => 'deep-wave-trio',
            'name' => 'Deep Wave Trio',
            'category' => 'weaves-and-bundles',
            'texture' => 'Deep wave',
            'price' => 75000,
            'compare' => 98000,
            'badge' => 'Sale',
            'featured' => false,
            'stock' => 11,
            'blurb' => 'Three bundles with a defined, glossy wave.',
            'story' => 'Matched from the same dye lot so the wave lines up across the head. Deep enough to hold a style, light enough that the install does not feel like a helmet.',
            'wash' => '#F7F1EA',
            'hair' => '#241C18',
            'lengths' => [16, 18, 20, 22],
            'inch' => 4000,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#161311'],
            ],
            'lace' => 'Three bundles',
            'density' => '300g',
            'cap' => 'Machine weft',
        ],
        [
            'slug' => 'coil-and-kink',
            'name' => 'Coil & Kink Bundle',
            'category' => 'weaves-and-bundles',
            'texture' => 'Kinky',
            'price' => 32000,
            'compare' => 45000,
            'badge' => 'Sale',
            'featured' => false,
            'stock' => 14,
            'blurb' => 'A tight coil for afros and tapered installs.',
            'story' => 'Springy, matte, and full. It blends with 4C hair without the shine that gives a bundle away. One bundle is a crown; two is a statement.',
            'wash' => '#F3EEE8',
            'hair' => '#1A120E',
            'lengths' => [12, 14, 16, 18],
            'inch' => 2500,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#120E0C'],
                ['name' => 'Dark brown', 'hex' => '#3B2A22'],
            ],
            'lace' => 'Bundle only',
            'density' => '100g',
            'cap' => 'Double weft',
        ],
        [
            'slug' => 'silk-press-bundle',
            'name' => 'Silk Press Bundle',
            'category' => 'weaves-and-bundles',
            'texture' => 'Straight',
            'price' => 26000,
            'compare' => 39000,
            'badge' => 'Sale',
            'featured' => false,
            'stock' => 18,
            'blurb' => 'Straight hair with the weight of a fresh press.',
            'story' => 'Silky without being slippery. It lies flat at the root and keeps a glass finish if you wrap it at night. Sold as a single bundle so you can build the density you want.',
            'wash' => '#F8F4F2',
            'hair' => '#221A16',
            'lengths' => [14, 16, 18, 20, 22],
            'inch' => 2200,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#161311'],
            ],
            'lace' => 'Bundle only',
            'density' => '100g',
            'cap' => 'Double weft',
        ],
        [
            'slug' => 'brazilian-body-wave',
            'name' => 'Brazilian Body Wave',
            'category' => 'brazilian',
            'texture' => 'Body wave',
            'price' => 89000,
            'compare' => 120000,
            'badge' => 'House',
            'featured' => true,
            'stock' => 8,
            'blurb' => 'Three Brazilian bundles with a living wave.',
            'story' => 'Our Brazilian body wave is thick at the weft and soft at the ends. It is the set clients reorder when the first install has done a full season. Minimal shed, a wave you can still curl tighter.',
            'wash' => '#FFF1E8',
            'hair' => '#4A3024',
            'lengths' => [16, 18, 20, 22, 24],
            'inch' => 5000,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#1A140F'],
                ['name' => 'Mocha', 'hex' => '#6A4636'],
            ],
            'lace' => 'Three bundles',
            'density' => '300g',
            'cap' => 'Brazilian weft',
        ],
        [
            'slug' => 'brazilian-silk-straight',
            'name' => 'Brazilian Silk Straight',
            'category' => 'brazilian',
            'texture' => 'Straight',
            'price' => 95000,
            'compare' => 130000,
            'badge' => 'Sale',
            'featured' => true,
            'stock' => 7,
            'blurb' => 'Straight Brazilian hair that stays straight.',
            'story' => 'Not a bundle that reverts the moment the weather turns. This straight is aligned, heavy, and quiet. It photographs like a silk press and behaves like one.',
            'wash' => '#F6F3F1',
            'hair' => '#1E1814',
            'lengths' => [16, 18, 20, 22, 24],
            'inch' => 5500,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#14110F'],
                ['name' => 'Soft brown', 'hex' => '#5A4034'],
            ],
            'lace' => 'Three bundles',
            'density' => '300g',
            'cap' => 'Brazilian weft',
        ],
        [
            'slug' => 'brazilian-loose-deep',
            'name' => 'Brazilian Loose Deep',
            'category' => 'brazilian',
            'texture' => 'Loose wave',
            'price' => 102000,
            'compare' => 140000,
            'badge' => 'New',
            'featured' => false,
            'stock' => 6,
            'blurb' => 'A deeper wave with Brazilian fullness.',
            'story' => 'Between a body wave and a curl. The pattern is open, the hair is full, and it holds a rod set if you want more drama on a Friday.',
            'wash' => '#F8EEE8',
            'hair' => '#3C2A22',
            'lengths' => [18, 20, 22, 24],
            'inch' => 5500,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#1A140F'],
            ],
            'lace' => 'Three bundles',
            'density' => '300g',
            'cap' => 'Brazilian weft',
        ],
        [
            'slug' => 'brazilian-curl-set',
            'name' => 'Brazilian Curl & Closure',
            'category' => 'brazilian',
            'texture' => 'Curly',
            'price' => 118000,
            'compare' => 155000,
            'badge' => 'Sale',
            'featured' => false,
            'stock' => 5,
            'blurb' => 'Curly bundles plus a 4×4 closure, matched.',
            'story' => 'The curl is defined without being dry. The closure is cut from the same lot, so the crown does not announce itself. A complete install in one box.',
            'wash' => '#FDEFF4',
            'hair' => '#2C1814',
            'lengths' => [16, 18, 20, 22],
            'inch' => 6000,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#140E0C'],
                ['name' => 'Burgundy tint', 'hex' => '#5C2433'],
            ],
            'lace' => '4×4 closure included',
            'density' => '300g + closure',
            'cap' => 'Brazilian curl weft',
        ],
        [
            'slug' => 'raw-body-wave',
            'name' => 'Raw Body Wave Bundles',
            'category' => 'raw-hair',
            'texture' => 'Body wave',
            'price' => 125000,
            'compare' => 160000,
            'badge' => 'New',
            'featured' => true,
            'stock' => 6,
            'blurb' => 'Unprocessed raw hair with a soft, living wave.',
            'story' => 'Cuticle-aligned raw hair that keeps its pattern after wash and air-dry. Thick at the weft, soft through the ends, and built for clients who want hair that still behaves like hair.',
            'wash' => '#FFF1E8',
            'hair' => '#3A2820',
            'lengths' => [16, 18, 20, 22, 24],
            'inch' => 6500,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#1A140F'],
            ],
            'lace' => 'Three bundles',
            'density' => '300g',
            'cap' => 'Raw weft',
        ],
        [
            'slug' => 'raw-straight-wig',
            'name' => 'Raw Straight Glueless Wig',
            'category' => 'raw-hair',
            'texture' => 'Straight',
            'price' => 210000,
            'compare' => 265000,
            'badge' => 'House',
            'featured' => false,
            'stock' => 4,
            'blurb' => 'A raw straight unit with a clean, unprocessed finish.',
            'story' => 'No heavy coating. The lace is pre-plucked, the hair is raw straight, and the cap is glueless so you can lift it off after a long day without drama.',
            'wash' => '#F6F3F1',
            'hair' => '#1C1614',
            'lengths' => [14, 16, 18, 20],
            'inch' => 0,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#14110F'],
            ],
            'lace' => '5×5 HD lace',
            'density' => '180%',
            'cap' => 'Glueless',
        ],
        [
            'slug' => 'donor-silk-straight',
            'name' => 'Donor Silk Straight',
            'category' => 'donor',
            'texture' => 'Straight',
            'price' => 145000,
            'compare' => 185000,
            'badge' => 'Sale',
            'featured' => true,
            'stock' => 5,
            'blurb' => 'Single-donor straight hair with a quiet silk finish.',
            'story' => 'One donor lot, matched length to length. Soft, heavy, and slow to tangle — the straight clients ask for when they want length without a coated shine.',
            'wash' => '#F8F4F2',
            'hair' => '#221A16',
            'lengths' => [18, 20, 22, 24],
            'inch' => 7000,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#161311'],
                ['name' => 'Soft brown', 'hex' => '#5A4034'],
            ],
            'lace' => 'Three bundles',
            'density' => '300g',
            'cap' => 'Donor weft',
        ],
        [
            'slug' => 'donor-loose-wave',
            'name' => 'Donor Loose Wave',
            'category' => 'donor',
            'texture' => 'Loose wave',
            'price' => 138000,
            'compare' => 175000,
            'badge' => '',
            'featured' => false,
            'stock' => 5,
            'blurb' => 'Donor hair with an open, soft wave.',
            'story' => 'The wave sits between body and deep. It holds a wrap overnight and still looks full when you shake it out in the morning.',
            'wash' => '#F8EEE8',
            'hair' => '#3C2A22',
            'lengths' => [16, 18, 20, 22],
            'inch' => 6800,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#1A140F'],
            ],
            'lace' => 'Three bundles',
            'density' => '300g',
            'cap' => 'Donor weft',
        ],
        [
            'slug' => 'vietnamese-bone-straight',
            'name' => 'Vietnamese Bone Straight',
            'category' => 'vietnamese',
            'texture' => 'Straight',
            'price' => 98000,
            'compare' => 130000,
            'badge' => 'New',
            'featured' => true,
            'stock' => 7,
            'blurb' => 'Fine Vietnamese straight with a glass press look.',
            'story' => 'Light at the hand, dense at the weft. It lies flat at the root and keeps a clean straight line after wash if you wrap it while damp.',
            'wash' => '#F6F3F1',
            'hair' => '#1E1814',
            'lengths' => [16, 18, 20, 22, 24],
            'inch' => 5200,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#14110F'],
            ],
            'lace' => 'Three bundles',
            'density' => '300g',
            'cap' => 'Vietnamese weft',
        ],
        [
            'slug' => 'vietnamese-body-wave',
            'name' => 'Vietnamese Body Wave',
            'category' => 'vietnamese',
            'texture' => 'Body wave',
            'price' => 105000,
            'compare' => 140000,
            'badge' => '',
            'featured' => false,
            'stock' => 6,
            'blurb' => 'Vietnamese body wave with a soft, fine pattern.',
            'story' => 'The wave is gentle and the hair is silky. Ideal for clients who want movement without a heavy curl set.',
            'wash' => '#FFF1E8',
            'hair' => '#4A3024',
            'lengths' => [16, 18, 20, 22],
            'inch' => 5400,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#1A140F'],
                ['name' => 'Mocha', 'hex' => '#6A4636'],
            ],
            'lace' => 'Three bundles',
            'density' => '300g',
            'cap' => 'Vietnamese weft',
        ],
        [
            'slug' => 'chinese-thick-straight',
            'name' => 'Chinese Thick Straight',
            'category' => 'chinese',
            'texture' => 'Straight',
            'price' => 92000,
            'compare' => 125000,
            'badge' => 'House',
            'featured' => true,
            'stock' => 8,
            'blurb' => 'Chinese luxury straight with a thick, polished hand.',
            'story' => 'Full, shiny, and steady under heat. This is the straight that photographs dense and stays aligned after a silk press finish.',
            'wash' => '#F8F4F2',
            'hair' => '#221A16',
            'lengths' => [16, 18, 20, 22, 24],
            'inch' => 5000,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#161311'],
            ],
            'lace' => 'Three bundles',
            'density' => '300g',
            'cap' => 'Chinese weft',
        ],
        [
            'slug' => 'chinese-body-wave',
            'name' => 'Chinese Body Wave',
            'category' => 'chinese',
            'texture' => 'Body wave',
            'price' => 99000,
            'compare' => 135000,
            'badge' => 'Sale',
            'featured' => false,
            'stock' => 7,
            'blurb' => 'Chinese body wave with volume and shine.',
            'story' => 'A fuller wave pattern with the polished finish Chinese luxury hair is known for. Holds a wrap and still looks thick at the ends.',
            'wash' => '#FFF1E8',
            'hair' => '#3A2820',
            'lengths' => [16, 18, 20, 22, 24],
            'inch' => 5200,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#1A140F'],
            ],
            'lace' => 'Three bundles',
            'density' => '300g',
            'cap' => 'Chinese weft',
        ],
        [
            'slug' => 'cambodian-deep-wave',
            'name' => 'Cambodian Deep Wave',
            'category' => 'cambodia',
            'texture' => 'Deep wave',
            'price' => 112000,
            'compare' => 148000,
            'badge' => 'New',
            'featured' => true,
            'stock' => 6,
            'blurb' => 'Cambodian deep wave with lasting pattern and shine.',
            'story' => 'The wave is defined without feeling stiff. Cambodian stock here keeps body after wash and looks rich under soft light.',
            'wash' => '#F8EEE8',
            'hair' => '#3C2A22',
            'lengths' => [16, 18, 20, 22, 24],
            'inch' => 5800,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#1A140F'],
            ],
            'lace' => 'Three bundles',
            'density' => '300g',
            'cap' => 'Cambodian weft',
        ],
        [
            'slug' => 'cambodian-curly-bundle',
            'name' => 'Cambodian Curly Bundle',
            'category' => 'cambodia',
            'texture' => 'Curly',
            'price' => 42000,
            'compare' => 55000,
            'badge' => '',
            'featured' => false,
            'stock' => 10,
            'blurb' => 'A single Cambodian curly bundle for custom density.',
            'story' => 'Defined curls with moisture left in the fibre. Add as many as you need for a full install that still moves.',
            'wash' => '#FDEFF4',
            'hair' => '#2C1814',
            'lengths' => [14, 16, 18, 20],
            'inch' => 2800,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#140E0C'],
            ],
            'lace' => 'Bundle only',
            'density' => '100g',
            'cap' => 'Cambodian weft',
        ],
        [
            'slug' => 'hd-13x4-frontal',
            'name' => 'HD 13×4 Frontal',
            'category' => 'frontals',
            'texture' => 'Straight',
            'price' => 55000,
            'compare' => 72000,
            'badge' => 'New',
            'featured' => true,
            'stock' => 12,
            'blurb' => 'A 13×4 HD lace frontal for a clean melt.',
            'story' => 'Pre-plucked hairline, HD lace, and enough ear-to-ear coverage for a natural part. Match it to your bundles for a finished install.',
            'wash' => '#FFF0F6',
            'hair' => '#1C1614',
            'lengths' => [14, 16, 18, 20],
            'inch' => 2500,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#161311'],
            ],
            'lace' => '13×4 HD',
            'density' => '130%',
            'cap' => 'Frontal only',
        ],
        [
            'slug' => 'transparent-13x6-frontal',
            'name' => 'Transparent 13×6 Frontal',
            'category' => 'frontals',
            'texture' => 'Body wave',
            'price' => 68000,
            'compare' => 88000,
            'badge' => 'Sale',
            'featured' => false,
            'stock' => 9,
            'blurb' => 'Deeper 13×6 transparent lace with body wave.',
            'story' => 'More parting space, softer lace, and a body-wave pattern that blends into most installs. Built for clients who want a deeper melt.',
            'wash' => '#FFF1E8',
            'hair' => '#3A2820',
            'lengths' => [16, 18, 20],
            'inch' => 2800,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#1A140F'],
            ],
            'lace' => '13×6 transparent',
            'density' => '130%',
            'cap' => 'Frontal only',
        ],
        [
            'slug' => 'hd-5x5-closure',
            'name' => 'HD 5×5 Closure',
            'category' => 'closures',
            'texture' => 'Straight',
            'price' => 32000,
            'compare' => 42000,
            'badge' => 'House',
            'featured' => true,
            'stock' => 14,
            'blurb' => 'A 5×5 HD closure for a flexible part.',
            'story' => 'Wider than a classic 4×4, so you can shift the part without the lace announcing itself. Soft, pre-plucked, and ready to melt.',
            'wash' => '#F6F3F1',
            'hair' => '#1E1814',
            'lengths' => [12, 14, 16, 18],
            'inch' => 1800,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#14110F'],
            ],
            'lace' => '5×5 HD',
            'density' => '130%',
            'cap' => 'Closure only',
        ],
        [
            'slug' => 'silk-4x4-closure',
            'name' => 'Silk Base 4×4 Closure',
            'category' => 'closures',
            'texture' => 'Body wave',
            'price' => 28000,
            'compare' => 36000,
            'badge' => '',
            'featured' => false,
            'stock' => 16,
            'blurb' => 'A 4×4 silk-base closure with body wave.',
            'story' => 'Silk base for a scalp-like finish at the crown. Body-wave pattern that blends cleanly into matching bundles.',
            'wash' => '#FFF1E8',
            'hair' => '#4A3024',
            'lengths' => [12, 14, 16, 18],
            'inch' => 1600,
            'colors' => [
                ['name' => 'Natural black', 'hex' => '#1A140F'],
            ],
            'lace' => '4×4 silk base',
            'density' => '130%',
            'cap' => 'Closure only',
        ],
        [
            'slug' => 'melt-and-hold',
            'name' => 'Melt & Hold Spray',
            'category' => 'salon-essentials',
            'texture' => 'Care',
            'price' => 8500,
            'compare' => 0,
            'badge' => '',
            'featured' => true,
            'stock' => 30,
            'blurb' => 'A light mist that lays lace without the crust.',
            'story' => 'Mist, press with a scarf, and leave it. The spray is flexible, so the hairline still moves when you do. One bottle covers a month of daily wear.',
            'wash' => '#FFF0F6',
            'hair' => '#FF1493',
            'lengths' => [],
            'inch' => 0,
            'colors' => [
                ['name' => 'Clear mist', 'hex' => '#F4D5E4'],
            ],
            'lace' => '100ml',
            'density' => 'Flexible hold',
            'cap' => 'Lace and edges',
        ],
        [
            'slug' => 'silk-grip-band',
            'name' => 'Silk Grip Band',
            'category' => 'salon-essentials',
            'texture' => 'Care',
            'price' => 6500,
            'compare' => 0,
            'badge' => '',
            'featured' => false,
            'stock' => 40,
            'blurb' => 'A soft band that keeps a glueless cap in place.',
            'story' => 'Wear it under the wig on long days. The silk face does not snag the lace, and the grip side stays put through harmattan and harmattan-adjacent air conditioning.',
            'wash' => '#F8E8EF',
            'hair' => '#C4006A',
            'lengths' => [],
            'inch' => 0,
            'colors' => [
                ['name' => 'Blush', 'hex' => '#F3B6D0'],
                ['name' => 'Ink', 'hex' => '#1A1A1A'],
            ],
            'lace' => 'One size',
            'density' => 'Adjustable',
            'cap' => 'Silk and elastic',
        ],
        [
            'slug' => 'marble-wig-stand',
            'name' => 'Marble Wig Stand',
            'category' => 'salon-essentials',
            'texture' => 'Care',
            'price' => 12000,
            'compare' => 15000,
            'badge' => 'Sale',
            'featured' => false,
            'stock' => 15,
            'blurb' => 'A weighted stand so the style keeps its shape overnight.',
            'story' => 'Canvas head, marble-look base, and enough weight that it does not wander off a dressing table. The piece airs out instead of sleeping in a bag.',
            'wash' => '#F4F1EF',
            'hair' => '#E7E1DE',
            'lengths' => [],
            'inch' => 0,
            'colors' => [
                ['name' => 'Warm stone', 'hex' => '#E6D9D2'],
            ],
            'lace' => '22 inch head',
            'density' => 'Weighted base',
            'cap' => 'Canvas and resin',
        ],
        [
            'slug' => 'edge-ritual-set',
            'name' => 'Edge Ritual Set',
            'category' => 'salon-essentials',
            'texture' => 'Care',
            'price' => 9500,
            'compare' => 0,
            'badge' => 'New',
            'featured' => false,
            'stock' => 22,
            'blurb' => 'A fine brush and a silk scarf for the hairline.',
            'story' => 'The brush is small enough for baby hairs and firm enough for a laid edge. The scarf wraps the hairline while the melt sets. Two tools, one calm morning.',
            'wash' => '#FFE8F2',
            'hair' => '#1A1A1A',
            'lengths' => [],
            'inch' => 0,
            'colors' => [
                ['name' => 'Rose gold', 'hex' => '#E7B7A4'],
            ],
            'lace' => 'Brush + scarf',
            'density' => 'Travel pouch',
            'cap' => 'Edges and wrap',
        ],
    ];
}

function product_by(string $slug): ?array
{
    foreach (products() as $product) {
        if ($product['slug'] === $slug) {
            return $product;
        }
    }
    return null;
}

function textures(): array
{
    $found = [];
    foreach (products() as $product) {
        $found[$product['texture']] = true;
    }
    $names = array_keys($found);
    sort($names);
    return $names;
}

function catalog_query(array $query): array
{
    $items = products();
    $category = (string) ($query['category'] ?? '');
    $group = (string) ($query['group'] ?? '');
    $texture = (string) ($query['texture'] ?? '');
    $max = (int) ($query['max'] ?? 0);
    $needle = strtolower(trim((string) ($query['q'] ?? '')));

    if ($category !== '') {
        $items = array_values(array_filter($items, static fn (array $p): bool => $p['category'] === $category));
    } elseif ($group !== '') {
        $slugs = array_column(categories_in_group($group), 'slug');
        $items = array_values(array_filter(
            $items,
            static fn (array $p): bool => in_array($p['category'], $slugs, true)
        ));
    }
    if ($texture !== '') {
        $items = array_values(array_filter($items, static fn (array $p): bool => $p['texture'] === $texture));
    }
    if ($max > 0) {
        $items = array_values(array_filter($items, static fn (array $p): bool => (int) $p['price'] <= $max));
    }
    if ($needle !== '') {
        $items = array_values(array_filter($items, static function (array $p) use ($needle): bool {
            $hay = strtolower($p['name'] . ' ' . $p['blurb'] . ' ' . $p['texture'] . ' ' . $p['category']);
            return str_contains($hay, $needle);
        }));
    }

    $sort = (string) ($query['sort'] ?? 'featured');
    usort($items, static function (array $a, array $b) use ($sort): int {
        return match ($sort) {
            'price-asc' => $a['price'] <=> $b['price'],
            'price-desc' => $b['price'] <=> $a['price'],
            'name' => strcmp($a['name'], $b['name']),
            default => ($b['featured'] <=> $a['featured']) ?: strcmp($a['name'], $b['name']),
        };
    });

    return $items;
}

function related_products(array $product, int $limit = 4): array
{
    $out = [];
    foreach (products() as $item) {
        if ($item['slug'] === $product['slug']) {
            continue;
        }
        if ($item['category'] === $product['category']) {
            $out[] = $item;
        }
    }
    return array_slice($out, 0, $limit);
}

function featured_products(): array
{
    return array_values(array_filter(products(), static fn (array $p): bool => !empty($p['featured'])));
}

function sale_products(): array
{
    return array_values(array_filter(products(), static fn (array $p): bool => sale_off($p) > 0));
}

function reviews(): array
{
    return [
        [
            'name' => 'Adaeze O.',
            'place' => 'Lekki',
            'stars' => 5,
            'quote' => 'A flawless fit, right from the first try.',
            'text' => 'The Firm Line laid perfectly flat on my first attempt. I wore it to a Thursday meeting, and my hair looked so natural that nobody even questioned it. Exactly the effortless look I wanted.',
        ],
        [
            'name' => 'Kemi A.',
            'place' => 'Abuja',
            'stars' => 5,
            'quote' => 'Three weeks later, and I’m still in love with it.',
            'text' => 'My Brazilian body wave arrived exactly as expected, and the quality did not disappoint. Three weeks in, the ends are still soft, the hair feels beautiful, and the delivery took just two days. Definitely worth it.',
        ],
        [
            'name' => 'Ifeoma N.',
            'place' => 'Enugu',
            'stars' => 5,
            'quote' => 'They got my length right. My bob is everything!',
            'text' => 'I sent them a photo on WhatsApp, and they recommended 18 inches. They got it spot-on! My bob looks so good and fits the exact style I had in mind. I love when a brand understands what you want.',
        ],
        [
            'name' => 'Tolu S.',
            'place' => 'Ibadan',
            'stars' => 4,
            'quote' => 'That grip band made all the difference.',
            'text' => 'I never realised how much I needed a good grip band until I tried this one. My glueless wig finally stays in place instead of shifting throughout the day. Such a simple accessory, but what a difference!',
        ],
    ];
}

function review_highlights(): array
{
    return [
        'Natural-looking results',
        'Premium hair quality',
        'Personalised hair advice',
        'Reliable delivery',
    ];
}

function states(): array
{
    return [
        'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno',
        'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT', 'Gombe', 'Imo',
        'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos', 'Nasarawa',
        'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers', 'Sokoto', 'Taraba',
        'Yobe', 'Zamfara',
    ];
}
