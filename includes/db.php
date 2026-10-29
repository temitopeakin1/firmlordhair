<?php
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    return $pdo;
}

function order_statuses(): array
{
    return ['Placed', 'Paid', 'Dispatched'];
}

function save_order(array $order): void
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $insert = $pdo->prepare(
            'INSERT INTO orders
                (id, placed_at, customer_name, email, phone, address, city, state_name, payment, note, subtotal, shipping, total, status)
             VALUES
                (:id, :placed_at, :customer_name, :email, :phone, :address, :city, :state_name, :payment, :note, :subtotal, :shipping, :total, :status)'
        );
        $insert->execute([
            'id' => $order['id'],
            'placed_at' => $order['placed_at'],
            'customer_name' => $order['customer']['name'],
            'email' => $order['customer']['email'],
            'phone' => $order['customer']['phone'],
            'address' => $order['customer']['address'],
            'city' => $order['customer']['city'],
            'state_name' => $order['customer']['state'],
            'payment' => $order['customer']['payment'],
            'note' => $order['customer']['note'],
            'subtotal' => $order['subtotal'],
            'shipping' => $order['shipping'],
            'total' => $order['total'],
            'status' => $order['status'],
        ]);
        $item = $pdo->prepare(
            'INSERT INTO order_items
                (order_id, slug, item_name, length_label, color_name, qty, unit_price, line_total)
             VALUES
                (:order_id, :slug, :item_name, :length_label, :color_name, :qty, :unit_price, :line_total)'
        );
        foreach ($order['items'] as $line) {
            $item->execute([
                'order_id' => $order['id'],
                'slug' => $line['slug'],
                'item_name' => $line['name'],
                'length_label' => $line['length'],
                'color_name' => $line['color'],
                'qty' => $line['qty'],
                'unit_price' => $line['unit'],
                'line_total' => $line['line'],
            ]);
        }
        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $error;
    }
}

function order_by_id(string $id): ?array
{
    $statement = db()->prepare('SELECT * FROM orders WHERE id = :id');
    $statement->execute(['id' => $id]);
    $row = $statement->fetch();
    if (!$row) {
        return null;
    }
    return hydrate_order($row, items_for_order($id));
}

function orders_for_email(string $email): array
{
    $statement = db()->prepare('SELECT * FROM orders WHERE email = :email ORDER BY placed_at DESC, id DESC');
    $statement->execute(['email' => $email]);
    return hydrate_orders($statement->fetchAll());
}

function all_orders(): array
{
    $rows = db()->query('SELECT * FROM orders ORDER BY placed_at DESC, id DESC')->fetchAll();
    return hydrate_orders($rows);
}

function set_order_status(string $id, string $status): bool
{
    if (!in_array($status, order_statuses(), true)) {
        return false;
    }
    $statement = db()->prepare('UPDATE orders SET status = :status WHERE id = :id');
    $statement->execute(['status' => $status, 'id' => $id]);
    return $statement->rowCount() > 0;
}

function items_for_order(string $id): array
{
    $statement = db()->prepare('SELECT * FROM order_items WHERE order_id = :id ORDER BY id ASC');
    $statement->execute(['id' => $id]);
    $items = [];
    foreach ($statement->fetchAll() as $row) {
        $items[] = [
            'slug' => $row['slug'],
            'name' => $row['item_name'],
            'length' => $row['length_label'],
            'color' => $row['color_name'],
            'qty' => (int) $row['qty'],
            'unit' => (int) $row['unit_price'],
            'line' => (int) $row['line_total'],
        ];
    }
    return $items;
}

function hydrate_orders(array $rows): array
{
    if (!$rows) {
        return [];
    }
    $ids = array_column($rows, 'id');
    $marks = implode(',', array_fill(0, count($ids), '?'));
    $statement = db()->prepare('SELECT * FROM order_items WHERE order_id IN (' . $marks . ') ORDER BY id ASC');
    $statement->execute($ids);
    $grouped = [];
    foreach ($statement->fetchAll() as $row) {
        $grouped[$row['order_id']][] = [
            'slug' => $row['slug'],
            'name' => $row['item_name'],
            'length' => $row['length_label'],
            'color' => $row['color_name'],
            'qty' => (int) $row['qty'],
            'unit' => (int) $row['unit_price'],
            'line' => (int) $row['line_total'],
        ];
    }
    $orders = [];
    foreach ($rows as $row) {
        $orders[] = hydrate_order($row, $grouped[$row['id']] ?? []);
    }
    return $orders;
}

function ensure_product_rows(): void
{
    static $ready = false;
    if ($ready) {
        return;
    }
    $ready = true;
    db()->exec("UPDATE products SET category = 'salon-essentials' WHERE category = 'accessories'");
    db()->exec("UPDATE products SET category = 'raw-hair' WHERE category = 'raw'");
    db()->exec("UPDATE products SET category = 'weaves-and-bundles' WHERE category = 'weaves'");
    $count = (int) db()->query('SELECT COUNT(*) FROM products')->fetchColumn();
    if ($count > 0) {
        return;
    }
    foreach (catalog_seed() as $product) {
        insert_product_row($product);
    }
}

function products_from_db(): array
{
    $rows = db()->query('SELECT * FROM products ORDER BY name ASC')->fetchAll();
    $items = [];
    foreach ($rows as $row) {
        $items[] = hydrate_product($row);
    }
    return $items;
}

function hydrate_product(array $row): array
{
    $lengths = json_decode((string) $row['lengths_json'], true);
    $colors = json_decode((string) $row['colors_json'], true);
    return [
        'slug' => $row['slug'],
        'name' => $row['name'],
        'category' => $row['category'],
        'texture' => $row['texture'],
        'price' => (int) $row['price'],
        'compare' => (int) $row['compare_price'],
        'badge' => $row['badge'],
        'featured' => (int) $row['featured'] === 1,
        'stock' => (int) $row['stock'],
        'blurb' => $row['blurb'],
        'story' => $row['story'],
        'wash' => $row['wash'],
        'hair' => $row['hair'],
        'lengths' => is_array($lengths) ? array_map('intval', $lengths) : [],
        'inch' => (int) $row['inch_price'],
        'colors' => is_array($colors) ? $colors : [],
        'lace' => $row['lace'],
        'density' => $row['density'],
        'cap' => $row['cap'],
    ];
}

function insert_product_row(array $product): void
{
    $statement = db()->prepare(
        'INSERT INTO products
            (slug, name, category, texture, price, compare_price, badge, featured, stock, blurb, story, wash, hair, lengths_json, inch_price, colors_json, lace, density, cap)
         VALUES
            (:slug, :name, :category, :texture, :price, :compare_price, :badge, :featured, :stock, :blurb, :story, :wash, :hair, :lengths_json, :inch_price, :colors_json, :lace, :density, :cap)'
    );
    $statement->execute(product_row_values($product));
}

function update_product_row(array $product): void
{
    $statement = db()->prepare(
        'UPDATE products SET
            name = :name, category = :category, texture = :texture, price = :price, compare_price = :compare_price,
            badge = :badge, featured = :featured, stock = :stock, blurb = :blurb, story = :story, wash = :wash, hair = :hair,
            lengths_json = :lengths_json, inch_price = :inch_price, colors_json = :colors_json, lace = :lace, density = :density, cap = :cap
         WHERE slug = :slug'
    );
    $statement->execute(product_row_values($product));
}

function product_row_values(array $product): array
{
    return [
        'slug' => $product['slug'],
        'name' => $product['name'],
        'category' => $product['category'],
        'texture' => $product['texture'],
        'price' => (int) $product['price'],
        'compare_price' => (int) ($product['compare'] ?? 0),
        'badge' => (string) ($product['badge'] ?? ''),
        'featured' => !empty($product['featured']) ? 1 : 0,
        'stock' => (int) $product['stock'],
        'blurb' => $product['blurb'],
        'story' => $product['story'],
        'wash' => $product['wash'],
        'hair' => $product['hair'],
        'lengths_json' => json_encode(array_values($product['lengths'] ?? []), JSON_THROW_ON_ERROR),
        'inch_price' => (int) ($product['inch'] ?? 0),
        'colors_json' => json_encode(array_values($product['colors'] ?? []), JSON_THROW_ON_ERROR),
        'lace' => $product['lace'],
        'density' => $product['density'],
        'cap' => $product['cap'],
    ];
}

function all_product_photos(): array
{
    static $loaded = false;
    static $photos = [];
    if ($loaded) {
        return $photos;
    }
    $loaded = true;
    try {
        $rows = db()->query('SELECT slug, slot, filename FROM product_images')->fetchAll();
    } catch (Throwable $error) {
        return [];
    }
    foreach ($rows as $row) {
        $filename = basename((string) $row['filename']);
        if (!preg_match('/^[a-z0-9-]+-[0-2]\.(jpg|png|webp)$/', $filename)) {
            continue;
        }
        $photos[(string) $row['slug']][(int) $row['slot']] = $filename;
    }
    return $photos;
}

function product_photos(string $slug): array
{
    $photos = all_product_photos()[$slug] ?? [];
    if ($photos === []) {
        $photos = product_photos_from_disk($slug);
    }
    ksort($photos);
    return $photos;
}

/** Use files in assets/images/products when the DB has no photo rows (common after deploy). */
function product_photos_from_disk(string $slug): array
{
    $slug = preg_replace('/[^a-z0-9-]/', '', strtolower($slug)) ?? '';
    if ($slug === '') {
        return [];
    }
    $dir = dirname(__DIR__) . '/assets/images/products';
    if (!is_dir($dir)) {
        return [];
    }
    $photos = [];
    foreach ([0, 1, 2] as $slot) {
        foreach (['jpg', 'png', 'webp'] as $ext) {
            $filename = $slug . '-' . $slot . '.' . $ext;
            if (is_file($dir . '/' . $filename)) {
                $photos[$slot] = $filename;
                break;
            }
        }
    }
    return $photos;
}

function save_product_photo(string $slug, int $slot, string $filename): void
{
    $statement = db()->prepare(
        'INSERT INTO product_images (slug, slot, filename) VALUES (:slug, :slot, :filename)
         ON DUPLICATE KEY UPDATE filename = VALUES(filename)'
    );
    $statement->execute(['slug' => $slug, 'slot' => $slot, 'filename' => $filename]);
}

function delete_product_photo(string $slug, int $slot): ?string
{
    $photos = product_photos($slug);
    $filename = $photos[$slot] ?? null;
    $statement = db()->prepare('DELETE FROM product_images WHERE slug = :slug AND slot = :slot');
    $statement->execute(['slug' => $slug, 'slot' => $slot]);
    return $filename;
}

function hydrate_order(array $row, array $items): array
{
    return [
        'id' => $row['id'],
        'placed' => date('j M Y, g:i a', strtotime((string) $row['placed_at'])),
        'customer' => [
            'name' => $row['customer_name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'address' => $row['address'],
            'city' => $row['city'],
            'state' => $row['state_name'],
            'payment' => $row['payment'],
            'note' => $row['note'],
        ],
        'items' => $items,
        'subtotal' => (int) $row['subtotal'],
        'shipping' => (int) $row['shipping'],
        'total' => (int) $row['total'],
        'status' => $row['status'],
    ];
}
