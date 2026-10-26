<?php
declare(strict_types=1);

function handle_post(): void
{
    $action = (string) ($_POST['action'] ?? '');
    $back = safe_path((string) ($_POST['back'] ?? request_path()));

    if (!csrf_ok()) {
        flash('That form expired. Please try again.', 'err');
        redirect(ltrim($back, '/'));
    }

    match ($action) {
        'add' => post_add($back),
        'update' => post_update($back),
        'remove' => post_remove($back),
        'checkout' => post_checkout(),
        'contact' => post_contact(),
        'account' => post_account($back),
        'logout' => post_logout(),
        'studio-login' => post_studio_login($back),
        'studio-logout' => post_studio_logout(),
        'studio-status' => post_studio_status(),
        'studio-piece' => post_studio_piece(),
        'studio-photo' => post_studio_photo(),
        'studio-photo-remove' => post_studio_photo_remove(),
        default => redirect(ltrim($back, '/')),
    };
}

function post_add(string $back): never
{
    $error = cart_add(
        (string) ($_POST['slug'] ?? ''),
        (string) ($_POST['length'] ?? ''),
        (string) ($_POST['color'] ?? ''),
        (int) ($_POST['qty'] ?? 1)
    );
    if ($error !== '') {
        flash($error, 'err');
    } else {
        flash('Added to your bag.');
    }
    redirect(ltrim($back, '/'));
}

function post_update(string $back): never
{
    $qty = $_POST['qty'] ?? [];
    if (is_array($qty)) {
        foreach ($qty as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            cart_set($key, (int) $value);
        }
    }
    flash('Bag updated.');
    redirect(ltrim($back, '/'));
}

function post_remove(string $back): never
{
    cart_remove((string) ($_POST['key'] ?? ''));
    flash('Removed from your bag.');
    redirect(ltrim($back, '/'));
}

function post_checkout(): never
{
    $input = [
        'name' => trim((string) ($_POST['name'] ?? '')),
        'phone' => trim((string) ($_POST['phone'] ?? '')),
        'email' => trim((string) ($_POST['email'] ?? '')),
        'address' => trim((string) ($_POST['address'] ?? '')),
        'city' => trim((string) ($_POST['city'] ?? '')),
        'state' => trim((string) ($_POST['state'] ?? '')),
        'payment' => (string) ($_POST['payment'] ?? ''),
        'note' => trim((string) ($_POST['note'] ?? '')),
    ];
    $errors = [];
    if ($input['name'] === '') {
        $errors['name'] = 'Add the name for the parcel.';
    }
    if (!preg_match('/^[0-9+\s-]{8,16}$/', $input['phone'])) {
        $errors['phone'] = 'Add a phone number we can call.';
    }
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Add a working email.';
    }
    if ($input['address'] === '') {
        $errors['address'] = 'Add a delivery address.';
    }
    if ($input['city'] === '') {
        $errors['city'] = 'Add a city.';
    }
    if (!in_array($input['state'], states(), true)) {
        $errors['state'] = 'Choose a state.';
    }
    if (!in_array($input['payment'], ['cod', 'transfer'], true)) {
        $errors['payment'] = 'Choose how you would like to pay.';
    }
    $lines = cart_lines();
    if (!$lines) {
        $errors['cart'] = 'Your bag is empty.';
    }
    if ($errors) {
        $_SESSION['old'] = $input;
        $_SESSION['errors'] = $errors;
        flash('Check the highlighted fields.', 'err');
        redirect('checkout');
    }

    $subtotal = cart_subtotal();
    $fee = shipping_fee($subtotal);
    $items = [];
    foreach ($lines as $line) {
        $items[] = [
            'name' => $line['product']['name'],
            'slug' => $line['product']['slug'],
            'length' => $line['length'],
            'color' => $line['color'],
            'qty' => $line['qty'],
            'unit' => $line['unit'],
            'line' => $line['line'],
        ];
    }
    $order = [
        'id' => 'FLH-' . strtoupper(bin2hex(random_bytes(3))),
        'placed_at' => date('Y-m-d H:i:s'),
        'customer' => $input,
        'items' => $items,
        'subtotal' => $subtotal,
        'shipping' => $fee,
        'total' => $subtotal + $fee,
        'status' => 'Placed',
    ];
    try {
        save_order($order);
    } catch (Throwable $error) {
        flash('We could not save this order. Your bag is still here.', 'err');
        redirect('checkout');
    }
    $_SESSION['customer'] = [
        'name' => $input['name'],
        'email' => $input['email'],
        'phone' => $input['phone'],
    ];
    cart_clear();
    unset($_SESSION['old'], $_SESSION['errors']);
    redirect('order/' . $order['id']);
}

function post_contact(): never
{
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));
    $errors = [];
    if ($name === '') {
        $errors['name'] = 'Add your name.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Add a working email.';
    }
    if (strlen($message) < 8) {
        $errors['message'] = 'Tell us a little more.';
    }
    if ($errors) {
        $_SESSION['old'] = compact('name', 'email', 'message');
        $_SESSION['errors'] = $errors;
        flash('Check the highlighted fields.', 'err');
        redirect('contact');
    }
    unset($_SESSION['old'], $_SESSION['errors']);
    flash('Message received. We reply on the same day during studio hours.');
    redirect('contact');
}

function post_account(string $back): never
{
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $errors = [];
    if ($name === '') {
        $errors['name'] = 'Add your name.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Add a working email.';
    }
    if ($errors) {
        $_SESSION['old'] = ['name' => $name, 'email' => $email, 'phone' => $phone];
        $_SESSION['errors'] = $errors;
        flash('Check the highlighted fields.', 'err');
        redirect(ltrim($back, '/'));
    }
    $_SESSION['customer'] = ['name' => $name, 'email' => $email, 'phone' => $phone];
    unset($_SESSION['old'], $_SESSION['errors']);
    flash('You are in, ' . $name . '.');
    redirect('account');
}

function post_logout(): never
{
    unset($_SESSION['customer']);
    flash('Signed out on this browser.');
    redirect('');
}

function studio_ok(): bool
{
    return !empty($_SESSION['studio']);
}

function post_studio_login(string $back): never
{
    $password = (string) ($_POST['password'] ?? '');
    $next = trim($back, '/');
    if (!in_array($next, ['studio', 'studio/pieces'], true)) {
        $next = 'studio';
    }
    if (!password_verify($password, STUDIO_PASSWORD_HASH)) {
        flash('That password does not open the studio desk.', 'err');
        redirect($next);
    }
    session_regenerate_id(true);
    $_SESSION['studio'] = true;
    redirect($next);
}

function post_studio_logout(): never
{
    unset($_SESSION['studio']);
    flash('The studio desk is locked.');
    redirect('studio');
}

function post_studio_status(): never
{
    if (!studio_ok()) {
        redirect('studio');
    }
    $id = (string) ($_POST['id'] ?? '');
    $status = (string) ($_POST['status'] ?? '');
    try {
        $saved = set_order_status($id, $status);
    } catch (Throwable $error) {
        $saved = false;
    }
    if (!$saved) {
        flash('Could not update that order.', 'err');
    } else {
        flash($id . ' is now ' . $status . '.');
    }
    redirect('studio');
}

function post_studio_piece(): never
{
    if (!studio_ok()) {
        redirect('studio');
    }
    $slug = trim((string) ($_POST['slug'] ?? ''));
    $existing = $slug !== '' ? product_by($slug) : null;
    if ($slug !== '' && !$existing) {
        flash('That piece is no longer in the shop.', 'err');
        redirect('studio/pieces');
    }
    try {
        [$product, $error] = piece_from_post($existing);
        if ($error !== '') {
            flash($error, 'err');
            redirect('studio/pieces');
        }
        if ($existing) {
            update_product_row($product);
            $message = $product['name'] . ' is updated.';
        } else {
            insert_product_row($product);
            $message = $product['name'] . ' is in the shop.';
        }
        products(true);
        $photo = $_FILES['photo'] ?? [];
        if ((int) ($photo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $photoError = store_product_photo($product['slug'], 0, $photo);
            if ($photoError !== '') {
                flash($message . ' ' . $photoError, 'err');
                redirect('studio/pieces');
            }
            $message .= ' Shop card photo saved.';
        }
        flash($message);
    } catch (Throwable $failure) {
        flash('The piece could not be saved. Check that MySQL is running.', 'err');
    }
    redirect('studio/pieces');
}

function piece_from_post(?array $existing): array
{
    $name = trim((string) ($_POST['name'] ?? ''));
    $category = (string) ($_POST['category'] ?? '');
    $texture = trim((string) ($_POST['texture'] ?? ''));
    $price = (int) preg_replace('/\D/', '', (string) ($_POST['price'] ?? ''));
    $compare = (int) preg_replace('/\D/', '', (string) ($_POST['compare'] ?? ''));
    $stock = (int) ($_POST['stock'] ?? 0);
    $inch = (int) preg_replace('/\D/', '', (string) ($_POST['inch'] ?? ''));
    $blurb = trim((string) ($_POST['blurb'] ?? ''));
    $story = trim((string) ($_POST['story'] ?? ''));
    $lace = trim((string) ($_POST['lace'] ?? ''));
    $density = trim((string) ($_POST['density'] ?? ''));
    $cap = trim((string) ($_POST['cap'] ?? ''));
    $badge = (string) ($_POST['badge'] ?? '');
    if (!in_array($badge, ['', 'Sale', 'New'], true)) {
        $badge = '';
    }
    if ($name === '') {
        return [[], 'Add a name.'];
    }
    if (!category_by($category)) {
        return [[], 'Choose a category for this piece.'];
    }
    if ($texture === '') {
        return [[], 'Add a texture.'];
    }
    if ($price < 1) {
        return [[], 'Add a price in naira.'];
    }
    if ($stock < 0) {
        return [[], 'Stock cannot be below zero.'];
    }
    if ($blurb === '') {
        return [[], 'Add a short line for the shop card.'];
    }
    preg_match_all('/\d+/', (string) ($_POST['lengths'] ?? ''), $lengthMatches);
    $lengths = array_values(array_unique(array_map('intval', $lengthMatches[0])));
    sort($lengths);
    $colors = [];
    foreach (preg_split('/\r\n|\r|\n/', (string) ($_POST['colors'] ?? '')) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $parts = array_map('trim', explode('|', $line));
        $colorName = $parts[0];
        $hex = $parts[1] ?? '#1C1614';
        if (!preg_match('/^#[0-9A-Fa-f]{3}([0-9A-Fa-f]{3})?$/', $hex)) {
            $hex = '#1C1614';
        }
        if ($colorName !== '') {
            $colors[] = ['name' => $colorName, 'hex' => $hex];
        }
    }
    if (!$colors) {
        return [[], 'Add at least one colour, like Natural black | #161311.'];
    }
    $slug = $existing['slug'] ?? unique_piece_slug($name);
    return [[
        'slug' => $slug,
        'name' => $name,
        'category' => $category,
        'texture' => $texture,
        'price' => $price,
        'compare' => $compare > $price ? $compare : 0,
        'badge' => $badge,
        'featured' => isset($_POST['featured']),
        'stock' => $stock,
        'blurb' => $blurb,
        'story' => $story,
        'wash' => $existing['wash'] ?? '#FFF0F6',
        'hair' => $existing['hair'] ?? $colors[0]['hex'],
        'lengths' => $lengths,
        'inch' => $inch,
        'colors' => $colors,
        'lace' => $lace,
        'density' => $density,
        'cap' => $cap,
    ], ''];
}

function unique_piece_slug(string $name): string
{
    $base = strtolower($name);
    $base = preg_replace('/[^a-z0-9]+/', '-', $base) ?? '';
    $base = trim($base, '-');
    $base = $base === '' ? 'piece' : substr($base, 0, 70);
    $slug = $base;
    $n = 2;
    while (product_by($slug)) {
        $slug = substr($base, 0, 66) . '-' . $n;
        $n++;
    }
    return $slug;
}

function post_studio_photo(): never
{
    if (!studio_ok()) {
        redirect('studio');
    }
    $slug = (string) ($_POST['slug'] ?? '');
    $slot = (int) ($_POST['slot'] ?? 0);
    try {
        $error = store_product_photo($slug, $slot, $_FILES['photo'] ?? []);
    } catch (Throwable $failure) {
        $error = 'The image could not be saved. Check that MySQL is running.';
    }
    $name = product_by($slug)['name'] ?? 'that piece';
    flash($error === '' ? 'Photo saved for ' . $name . '.' : $error, $error === '' ? 'ok' : 'err');
    redirect('studio/pieces');
}

function post_studio_photo_remove(): never
{
    if (!studio_ok()) {
        redirect('studio');
    }
    $slug = (string) ($_POST['slug'] ?? '');
    $slot = (int) ($_POST['slot'] ?? 0);
    try {
        $filename = delete_product_photo($slug, $slot);
    } catch (Throwable $failure) {
        flash('The photo could not be removed.', 'err');
        redirect('studio/pieces');
    }
    if ($filename) {
        $path = dirname(__DIR__) . '/assets/images/products/' . $filename;
        if (is_file($path)) {
            unlink($path);
        }
        flash('Photo removed.');
    }
    redirect('studio/pieces');
}

function store_product_photo(string $slug, int $slot, array $file): string
{
    if (!product_by($slug)) {
        return 'That piece is not in the shop.';
    }
    if ($slot < 0 || $slot > 2) {
        return 'Choose photo 1, 2, or 3.';
    }
    $uploadError = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($uploadError === UPLOAD_ERR_NO_FILE) {
        return 'Choose an image to upload.';
    }
    if ($uploadError !== UPLOAD_ERR_OK) {
        return 'That image did not finish uploading.';
    }
    if ((int) ($file['size'] ?? 0) > 4 * 1024 * 1024) {
        return 'Use an image under 4 MB.';
    }
    $tmp = (string) ($file['tmp_name'] ?? '');
    $mime = is_file($tmp) ? (string) mime_content_type($tmp) : '';
    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => '',
    };
    if ($ext === '' || !is_uploaded_file($tmp)) {
        return 'Use a JPG, PNG, or WebP image.';
    }
    $dir = dirname(__DIR__) . '/assets/images/products';
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        return 'The image folder could not be created.';
    }
    $filename = $slug . '-' . $slot . '.' . $ext;
    $dest = $dir . '/' . $filename;
    foreach (['jpg', 'png', 'webp'] as $old) {
        $previous = $dir . '/' . $slug . '-' . $slot . '.' . $old;
        if ($previous !== $dest && is_file($previous)) {
            unlink($previous);
        }
    }
    if (!move_uploaded_file($tmp, $dest)) {
        return 'The image could not be saved.';
    }
    save_product_photo($slug, $slot, $filename);
    return '';
}
