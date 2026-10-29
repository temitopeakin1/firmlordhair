<?php
declare(strict_types=1);

/**
 * Copy this file to config.php and fill in real values.
 * config.php is gitignored — do not commit secrets.
 *
 * Generate a studio password hash:
 *   php -r "echo password_hash('your-password', PASSWORD_DEFAULT), PHP_EOL;"
 */

const DB_HOST = '127.0.0.1';
const DB_NAME = 'firmlordhair';
const DB_USER = 'root';
const DB_PASS = '';
const STUDIO_PASSWORD_HASH = '$2y$12$replaceWithYourPasswordHash';
