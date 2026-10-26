<?php
declare(strict_types=1);

const SITE_NAME = 'firmlordhair';
const SITE_PHONE = '08063349857';
const SITE_PHONE_E164 = '2348063349857';
const SITE_EMAIL = 'firmlordhair@gmail.com';
const FREE_DELIVERY = 150000;
const DELIVERY_FEE = 3500;

function app_base(): string
{
    $script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($script === '/' || $script === '.' || $script === '') {
        return '';
    }
    return rtrim($script, '/');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'path' => app_base() !== '' ? app_base() : '/',
    ]);
    session_start();
}

require __DIR__ . '/config.php';
require __DIR__ . '/db.php';
require __DIR__ . '/catalog.php';
require __DIR__ . '/cart.php';
require __DIR__ . '/art.php';
require __DIR__ . '/view.php';
require __DIR__ . '/actions.php';
require __DIR__ . '/routes.php';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    $base = app_base();
    if ($path === '') {
        return $base === '' ? '/' : $base;
    }
    return ($base === '' ? '' : $base) . '/' . $path;
}

function asset(string $path): string
{
    $file = dirname(__DIR__) . '/assets/' . ltrim($path, '/');
    $version = is_file($file) ? (string) filemtime($file) : '1';
    return url('assets/' . ltrim($path, '/')) . '?v=' . $version;
}

function request_path(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = app_base();
    if ($base !== '' && str_starts_with($uri, $base)) {
        $uri = substr($uri, strlen($base));
    }
    $uri = '/' . trim($uri, '/');
    if ($uri === '/index.php') {
        return '/';
    }
    return $uri;
}

function redirect(string $path): never
{
    header('Location: ' . url(ltrim($path, '/')));
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_ok(): bool
{
    $sent = $_POST['csrf'] ?? '';
    $known = $_SESSION['csrf'] ?? '';
    return is_string($sent) && is_string($known) && $known !== '' && hash_equals($known, $sent);
}

function flash(string $message, string $type = 'ok'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function take_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

function customer(): ?array
{
    $customer = $_SESSION['customer'] ?? null;
    return is_array($customer) ? $customer : null;
}

function orders(): array
{
    $orders = $_SESSION['orders'] ?? [];
    return is_array($orders) ? $orders : [];
}

function find_order(string $id): ?array
{
    try {
        return order_by_id($id);
    } catch (Throwable $error) {
        return null;
    }
}

function current_back(): string
{
    $path = request_path();
    $query = $_SERVER['QUERY_STRING'] ?? '';
    if ($query !== '') {
        $path .= '?' . $query;
    }
    return $path;
}

function safe_path(string $path): string
{
    $path = trim($path);
    if ($path === '' || $path[0] !== '/' || str_starts_with($path, '//') || str_contains($path, '://')) {
        return '/';
    }
    return $path;
}

function money(int $amount): string
{
    return '₦' . number_format($amount);
}

function shipping_fee(int $subtotal): int
{
    if ($subtotal <= 0 || $subtotal >= FREE_DELIVERY) {
        return 0;
    }
    return DELIVERY_FEE;
}

function quote_price(array $product, ?string $length): array
{
    $price = (int) $product['price'];
    $compare = (int) ($product['compare'] ?? 0);
    $lengths = $product['lengths'] ?? [];
    $chosen = '';
    if ($lengths) {
        $picked = (int) ($length !== null && $length !== '' ? $length : $lengths[0]);
        if (!in_array($picked, $lengths, true)) {
            $picked = $lengths[0];
        }
        $delta = ($picked - $lengths[0]) * (int) $product['inch'];
        $price += $delta;
        if ($compare > 0) {
            $compare += $delta;
        }
        $chosen = (string) $picked;
    }
    return ['price' => $price, 'compare' => $compare, 'length' => $chosen];
}

function badge_of(array $product): string
{
    if (!empty($product['badge'])) {
        return (string) $product['badge'];
    }
    if (!empty($product['compare']) && $product['compare'] > $product['price']) {
        return 'Sale';
    }
    return '';
}

function sale_off(array $product): int
{
    $compare = (int) ($product['compare'] ?? 0);
    if ($compare <= (int) $product['price']) {
        return 0;
    }
    return (int) round((1 - $product['price'] / $compare) * 100);
}

function wa_link(string $text = ''): string
{
    $base = 'https://wa.me/' . SITE_PHONE_E164;
    if ($text === '') {
        return $base;
    }
    return $base . '?text=' . rawurlencode($text);
}
