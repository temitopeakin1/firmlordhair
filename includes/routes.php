<?php
declare(strict_types=1);

function resolve_route(string $path): array
{
    $parts = array_values(array_filter(explode('/', trim($path, '/')), static fn (string $p): bool => $p !== ''));
    $head = $parts[0] ?? '';
    $tail = rawurldecode($parts[1] ?? '');

    return match ($head) {
        '' => page('home', 'firmlordhair', 'Luxury hair of all types: raw, donor, Vietnamese, Chinese, Cambodia, frontals, closures, packet weavon, hair products, and salon equipment.'),
        'shop' => shop_page([]),
        'categories', 'category' => categories_route($parts),
        'product' => product_page($tail),
        'search' => search_page(),
        'cart' => page('cart', 'Your bag'),
        'checkout' => page('checkout', 'Checkout'),
        'order' => order_page($tail),
        'account' => page('account', 'Your account'),
        'login' => page('login', 'Sign in'),
        'register' => page('register', 'Create your details'),
        'about' => page('about', 'The House', 'firmlordhair sells luxury hair of all types, frontals, closures, packet weavon, hair products, and salon equipment in Lagos.'),
        'contact' => page('contact', 'Contact'),
        'faq' => page('faq', 'Questions'),
        'shipping' => page('shipping', 'Shipping and returns'),
        'studio' => studio_page($tail),
        default => missing_page(),
    };
}

function page(string $view, string $title, string $description = ''): array
{
    return [
        'view' => $view,
        'status' => 200,
        'data' => [
            'title' => $title,
            'description' => $description,
        ],
    ];
}

function missing_page(): array
{
    return [
        'view' => 'missing',
        'status' => 404,
        'data' => [
            'title' => 'Page not found',
            'description' => '',
        ],
    ];
}

function studio_page(string $section): array
{
    if ($section === 'pieces') {
        return page('studio_pieces', 'Piece photos');
    }
    if ($section !== '') {
        return missing_page();
    }
    return page('studio', 'Studio desk');
}

function shop_page(array $extra): array
{
    $query = [
        'category' => (string) ($extra['category'] ?? ($_GET['category'] ?? '')),
        'group' => (string) ($extra['group'] ?? ($_GET['group'] ?? '')),
        'texture' => (string) ($_GET['texture'] ?? ''),
        'sort' => (string) ($_GET['sort'] ?? 'featured'),
        'max' => (string) ($_GET['max'] ?? ''),
        'q' => (string) ($extra['q'] ?? ($_GET['q'] ?? '')),
    ];
    $category = $query['category'] !== '' ? category_by($query['category']) : null;
    $parentGroup = '';
    if ($category !== null && in_array((string) ($category['group'] ?? ''), category_menu_groups(), true)) {
        $parentGroup = (string) $category['group'];
    } elseif ($query['group'] !== '' && in_array($query['group'], category_menu_groups(), true)) {
        $parentGroup = $query['group'];
    }
    if ($query['category'] !== '') {
        $query['group'] = '';
    }
    $groupMeta = ($category === null && $parentGroup !== '') ? category_group_meta($parentGroup) : null;
    $parentMeta = $parentGroup !== '' ? category_group_meta($parentGroup) : null;
    if ($category !== null) {
        $pillCategories = categories_in_group((string) ($category['group'] ?? '')) ?: categories();
    } elseif ($parentGroup !== '') {
        $pillCategories = categories_in_group($parentGroup);
    } else {
        $pillCategories = categories();
    }
    if ($category !== null) {
        $action = category_path($category);
    } elseif ($parentGroup !== '') {
        $action = category_group_path($parentGroup);
    } else {
        $action = 'shop';
    }
    return [
        'view' => 'shop',
        'status' => 200,
        'data' => [
            'title' => $category['name'] ?? ($groupMeta['name'] ?? 'Shop'),
            'description' => $category['line'] ?? ($groupMeta['line'] ?? 'Wigs, weaves and bundles, hair by origin, frontals, closures, and salon essentials.'),
            'heading' => $category['name'] ?? ($groupMeta['name'] ?? 'The shop'),
            'intro' => $category['line'] ?? ($groupMeta['line'] ?? 'Browse by product type, hair origin, lace pieces, or salon essentials.'),
            'products' => catalog_query($query),
            'filters' => $query,
            'pill_categories' => $pillCategories,
            'parent_group' => $parentGroup,
            'parent_meta' => $parentMeta,
            'action' => $action,
        ],
    ];
}

function categories_route(array $parts): array
{
    $first = rawurldecode($parts[1] ?? '');
    $second = rawurldecode($parts[2] ?? '');
    if ($first === '') {
        return missing_page();
    }
    if ($second !== '') {
        if (!in_array($first, category_menu_groups(), true)) {
            return missing_page();
        }
        $category = category_by($second);
        if ($category === null || (string) ($category['group'] ?? '') !== $first) {
            return missing_page();
        }
        return shop_page(['category' => $second]);
    }
    if (in_array($first, category_menu_groups(), true)) {
        return shop_page(['group' => $first]);
    }
    $category = category_by($first);
    if ($category === null) {
        return missing_page();
    }
    $group = (string) ($category['group'] ?? '');
    if (in_array($group, category_menu_groups(), true)) {
        redirect(category_path($category));
    }
    return shop_page(['category' => $first]);
}

function product_page(string $slug): array
{
    $product = $slug !== '' ? product_by($slug) : null;
    if (!$product) {
        return missing_page();
    }
    return [
        'view' => 'product',
        'status' => 200,
        'data' => [
            'title' => $product['name'],
            'description' => $product['blurb'],
            'product' => $product,
            'related' => related_products($product),
        ],
    ];
}

function search_page(): array
{
    $q = trim((string) ($_GET['q'] ?? ''));
    $built = shop_page(['q' => $q]);
    $built['view'] = 'search';
    $built['data']['action'] = 'search';
    $built['data']['title'] = $q === '' ? 'Search' : 'Search · ' . $q;
    $built['data']['heading'] = $q === '' ? 'Search the house' : 'Results for “' . $q . '”';
    $built['data']['intro'] = $q === '' ? 'Try a texture, a colour, or a piece by name.' : $built['data']['intro'];
    $built['data']['query'] = $q;
    return $built;
}

function order_page(string $id): array
{
    $order = $id !== '' ? find_order($id) : null;
    if (!$order) {
        return [
            'view' => 'order',
            'status' => 404,
            'data' => [
                'title' => 'Order',
                'description' => '',
                'order' => null,
            ],
        ];
    }
    return [
        'view' => 'order',
        'status' => 200,
        'data' => [
            'title' => 'Order ' . $order['id'],
            'description' => '',
            'order' => $order,
        ],
    ];
}

function render_page(array $resolved): void
{
    http_response_code((int) $resolved['status']);
    $data = $resolved['data'];
    $view = basename((string) $resolved['view']);
    $file = dirname(__DIR__) . '/pages/' . $view . '.php';
    if (!is_file($file)) {
        http_response_code(404);
        $view = 'missing';
        $file = dirname(__DIR__) . '/pages/missing.php';
        $data['title'] = 'Page not found';
    }
    extract($data, EXTR_SKIP);
    require __DIR__ . '/layout_top.php';
    require $file;
    require __DIR__ . '/layout_end.php';
    take_form_state();
}
