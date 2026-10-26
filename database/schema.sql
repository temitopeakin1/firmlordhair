CREATE TABLE IF NOT EXISTS orders (
    id VARCHAR(16) NOT NULL,
    placed_at DATETIME NOT NULL,
    customer_name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL,
    phone VARCHAR(32) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(80) NOT NULL,
    state_name VARCHAR(80) NOT NULL,
    payment VARCHAR(16) NOT NULL,
    note TEXT NOT NULL,
    subtotal INT NOT NULL,
    shipping INT NOT NULL,
    total INT NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'Placed',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
    id INT NOT NULL AUTO_INCREMENT,
    order_id VARCHAR(16) NOT NULL,
    slug VARCHAR(80) NOT NULL,
    item_name VARCHAR(160) NOT NULL,
    length_label VARCHAR(16) NOT NULL,
    color_name VARCHAR(80) NOT NULL,
    qty INT NOT NULL,
    unit_price INT NOT NULL,
    line_total INT NOT NULL,
    PRIMARY KEY (id),
    KEY order_items_order_id (order_id),
    CONSTRAINT order_items_order_fk FOREIGN KEY (order_id) REFERENCES orders (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    slug VARCHAR(80) NOT NULL,
    name VARCHAR(160) NOT NULL,
    category VARCHAR(40) NOT NULL,
    texture VARCHAR(40) NOT NULL,
    price INT NOT NULL,
    compare_price INT NOT NULL DEFAULT 0,
    badge VARCHAR(40) NOT NULL DEFAULT '',
    featured TINYINT NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    blurb VARCHAR(255) NOT NULL,
    story TEXT NOT NULL,
    wash VARCHAR(16) NOT NULL,
    hair VARCHAR(16) NOT NULL,
    lengths_json VARCHAR(255) NOT NULL,
    inch_price INT NOT NULL DEFAULT 0,
    colors_json TEXT NOT NULL,
    lace VARCHAR(80) NOT NULL,
    density VARCHAR(80) NOT NULL,
    cap VARCHAR(120) NOT NULL,
    PRIMARY KEY (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_images (
    slug VARCHAR(80) NOT NULL,
    slot TINYINT UNSIGNED NOT NULL,
    filename VARCHAR(180) NOT NULL,
    PRIMARY KEY (slug, slot)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
