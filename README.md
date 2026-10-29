# firmlordhair

Lagos luxury hair ecommerce storefront — wigs, weaves & bundles, hair by origin, frontals, closures, and salon essentials — plus a password-protected **Studio desk** for orders and product photos.

Live site: [firmlordhair.com.ng](https://firmlordhair.com.ng)

---

## Stack

| Layer | Choice |
| --- | --- |
| Backend | Vanilla PHP 8+ (no framework) |
| Database | MySQL / MariaDB (PDO) |
| Frontend | Server-rendered PHP, `assets/css/app.css`, `assets/js/app.js` |
| Server | Apache (`mod_rewrite` for pretty URLs) |
| Local | XAMPP (`http://localhost/firmlordhair`) |

---

## Features

- Storefront: home, shop, nested **Categories** (`/categories/...`), product detail, search, cart, checkout, account
- Category groups in the nav: product type, **Hair by Origin**, **Frontals & Closures**, salon essentials
- Cart + checkout with delivery rules (free delivery threshold / fee in `includes/bootstrap.php`)
- **Studio desk** (`/studio`, `/studio/pieces`): orders, CRUD pieces, photo upload
- Product images under `assets/images/products/` (DB `product_images` + filesystem fallback)
- Scroll reveals, mobile accordion nav, banner carousel

---

## Project layout

```
firmlordhair/
├── index.php                 # Front controller
├── .htaccess                 # DirectoryIndex (local rewrite often lives in htdocs/.htaccess)
├── .gitignore                # Keeps includes/config.php out of git
├── database/
│   └── schema.sql            # orders, order_items, products, product_images
├── includes/
│   ├── bootstrap.php         # App constants, sessions, helpers
│   ├── config.example.php    # Template for secrets (commit this)
│   ├── config.php            # Local/production secrets (gitignored)
│   ├── routes.php            # URL → page view
│   ├── catalog.php           # Categories, nav, seed catalogue
│   ├── db.php                # PDO + products/orders/photos
│   ├── actions.php           # POST handlers (cart, studio, auth)
│   ├── cart.php / art.php / view.php
│   ├── layout_top.php / layout_end.php
├── pages/                    # One PHP view per route
├── assets/
│   ├── css/app.css
│   ├── js/app.js
│   └── images/               # Banners, logo, products/
└── scripts/
    └── seed_empty_categories.php
```

---

## Local setup (XAMPP)

### 1. Place the project

```text
C:\xampp\htdocs\firmlordhair
```

### 2. Create the database

In phpMyAdmin or MySQL:

```sql
CREATE DATABASE firmlordhair CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Import schema:

```bash
mysql -u root firmlordhair < database/schema.sql
```

Or use phpMyAdmin → Import → `database/schema.sql`.

### 3. Configure secrets

```bash
copy includes\config.example.php includes\config.php
```

Edit `includes/config.php`:

```php
const DB_HOST = '127.0.0.1';
const DB_NAME = 'firmlordhair';
const DB_USER = 'root';
const DB_PASS = '';
const STUDIO_PASSWORD_HASH = '...'; // see Studio password below
```

`includes/config.php` is **gitignored**. Never commit real passwords or production DB credentials.

### 4. URL rewriting (local)

Pretty URLs for this app are usually handled by **`C:\xampp\htdocs\.htaccess`** (parent of the project), not only the app’s `.htaccess`. The parent rules should route `/firmlordhair` and `/firmlordhair/*` to `firmlordhair/index.php`.

Ensure Apache has `mod_rewrite` enabled and `AllowOverride` permits `.htaccess`.

### 5. Open the site

```text
http://localhost/firmlordhair
```

### 6. Optional: seed products

If the catalogue tables are empty, run:

```bash
php scripts/seed_empty_categories.php
```

(Requires a working `config.php` and MySQL.)

---

## Studio desk

| URL | Purpose |
| --- | --- |
| `/studio` | Orders |
| `/studio/pieces` | Add/edit pieces and photos |

### Studio password

Password is verified against `STUDIO_PASSWORD_HASH` in `includes/config.php`.

Generate a new hash:

```bash
php -r "echo password_hash('your-new-password', PASSWORD_DEFAULT), PHP_EOL;"
```

Paste the output into `STUDIO_PASSWORD_HASH`, then save. Update production `config.php` separately if needed.

---

## Main routes

| Path | Page |
| --- | --- |
| `/` | Home |
| `/shop` | Shop (filters / sort) |
| `/categories/{slug}` | Category (e.g. wigs) |
| `/categories/origin/{slug}` | Origin leaf (e.g. brazilian) |
| `/categories/piece/{slug}` | Lace leaf (e.g. frontals) |
| `/product/{slug}` | Product detail |
| `/search?q=` | Search |
| `/cart`, `/checkout` | Bag & checkout |
| `/about` | The House |
| `/studio`, `/studio/pieces` | Studio desk |

Legacy `/category/...` still resolves; nested origin/piece URLs are preferred.

---

## Product images

- Files live in `assets/images/products/`  
  Naming: `{slug}-{slot}.{jpg|png|webp}` (slot `0`–`2`; slot `0` is the shop card)
- Rows in `product_images` map slug + slot → filename
- If the DB has no row, the app also looks for matching files on disk
- Studio → Pieces can upload/replace photos (writes file + DB)

**Deploy tip:** Upload the full `assets/images/products/` folder. Missing files show SVG placeholders instead of photos.

---

## Production deploy (cPanel / public_html)

1. Upload the project into `public_html` (or a subdomain docroot).
2. Create MySQL DB + user in cPanel; import `database/schema.sql`.
3. Create `includes/config.php` on the server (from `config.example.php`) with **production** DB credentials and studio hash.
4. Add Apache rewrite in `public_html/.htaccess` (document root):

```apache
DirectoryIndex index.php

<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteRule ^index\.php$ - [L]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^ index.php [L,QSA]
</IfModule>
```

5. Upload `pages/` completely (including `home.php`, `missing.php`, etc.).
6. Upload `assets/` including `images/products/`.
7. Confirm folder permissions allow PHP to write uploaded photos into `assets/images/products/` if Studio uploads are used.

Do **not** rely on git to push `config.php` — it is ignored on purpose.

---

## Security notes

- Keep `includes/config.php` out of the repository
- Use a strong studio password on production; rotate if an old hash was ever committed
- `assets/images/products/.htaccess` blocks executing PHP inside the products image folder
- Studio actions require an authenticated studio session + CSRF tokens

---

## Development notes

- Front controller: every request → `index.php` → `resolve_route()` → `pages/{view}.php`
- Categories and nav structure are defined in PHP (`includes/catalog.php`), not a categories table
- Products / orders / images are MySQL; catalogue seed helpers live in `catalog.php`
- Site contact defaults (phone, email, delivery fees) are constants in `includes/bootstrap.php`

---

## Requirements

- PHP 8.0+ (8.2 recommended)
- MySQL 5.7+ / MariaDB with `utf8mb4`
- Apache with `mod_rewrite` (or equivalent front-controller rewrite)

---

## License / ownership

Private storefront for **firmlordhair**. All rights reserved unless otherwise agreed.
