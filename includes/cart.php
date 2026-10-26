<?php
declare(strict_types=1);

function cart_raw(): array
{
    $cart = $_SESSION['cart'] ?? [];
    return is_array($cart) ? $cart : [];
}

function cart_key(string $slug, string $length, string $color): string
{
    return $slug . '|' . $length . '|' . $color;
}

function cart_save(array $cart): void
{
    $_SESSION['cart'] = $cart;
}

function cart_add(string $slug, string $length, string $color, int $qty): string
{
    $product = product_by($slug);
    if (!$product) {
        return 'That piece is no longer listed.';
    }
    $quote = quote_price($product, $length);
    $length = (string) $quote['length'];
    $names = array_column($product['colors'], 'name');
    if ($color === '' && $names) {
        $color = $names[0];
    }
    if ($names && !in_array($color, $names, true)) {
        return 'Choose a colour from the list.';
    }
    $qty = max(1, $qty);
    $key = cart_key($slug, $length, $color);
    $cart = cart_raw();
    $next = (int) ($cart[$key]['qty'] ?? 0) + $qty;
    if ($next > (int) $product['stock']) {
        return 'Only ' . (int) $product['stock'] . ' left in that finish.';
    }
    $cart[$key] = [
        'slug' => $slug,
        'length' => $length,
        'color' => $color,
        'qty' => $next,
    ];
    cart_save($cart);
    $_SESSION['open_bag'] = true;
    return '';
}

function cart_set(string $key, int $qty): void
{
    $cart = cart_raw();
    if (!isset($cart[$key])) {
        return;
    }
    if ($qty <= 0) {
        unset($cart[$key]);
        cart_save($cart);
        return;
    }
    $product = product_by((string) $cart[$key]['slug']);
    if (!$product) {
        unset($cart[$key]);
        cart_save($cart);
        return;
    }
    $cart[$key]['qty'] = min($qty, (int) $product['stock']);
    cart_save($cart);
}

function cart_remove(string $key): void
{
    $cart = cart_raw();
    unset($cart[$key]);
    cart_save($cart);
}

function cart_clear(): void
{
    $_SESSION['cart'] = [];
}

function cart_lines(): array
{
    $lines = [];
    foreach (cart_raw() as $key => $row) {
        $product = product_by((string) ($row['slug'] ?? ''));
        if (!$product) {
            continue;
        }
        $quote = quote_price($product, (string) ($row['length'] ?? ''));
        $qty = max(1, (int) ($row['qty'] ?? 1));
        $lines[] = [
            'key' => (string) $key,
            'product' => $product,
            'length' => (string) ($row['length'] ?? ''),
            'color' => (string) ($row['color'] ?? ''),
            'qty' => $qty,
            'unit' => $quote['price'],
            'line' => $quote['price'] * $qty,
        ];
    }
    return $lines;
}

function cart_count(): int
{
    $count = 0;
    foreach (cart_lines() as $line) {
        $count += $line['qty'];
    }
    return $count;
}

function cart_subtotal(): int
{
    $total = 0;
    foreach (cart_lines() as $line) {
        $total += $line['line'];
    }
    return $total;
}

function take_open_bag(): bool
{
    $open = !empty($_SESSION['open_bag']);
    unset($_SESSION['open_bag']);
    return $open;
}
