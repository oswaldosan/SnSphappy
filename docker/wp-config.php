<?php
/**
 * wp-config.php — container / Railway edition.
 *
 * Reads every setting from environment variables. The official
 * `wordpress:*-apache` image ships a similar `wp-config-docker.php`,
 * but `serversideup/php` does not auto-generate a config file, so
 * we bring our own.
 *
 * Required envs (set in Railway → Variables):
 *   WORDPRESS_DB_HOST       mysql.railway.internal
 *   WORDPRESS_DB_NAME       railway
 *   WORDPRESS_DB_USER       root
 *   WORDPRESS_DB_PASSWORD   ****
 *
 * Recommended envs:
 *   WORDPRESS_TABLE_PREFIX  wp_
 *   WORDPRESS_DEBUG         0 | 1
 *   WORDPRESS_CONFIG_EXTRA  (raw PHP evaluated after constants)
 *   WORDPRESS_AUTH_KEY      (and the 7 other salts — generate once
 *                            at https://api.wordpress.org/secret-key/1.1/salt/
 *                            and paste each as its own env var)
 */

// ── 1. Database ──────────────────────────────────────────────────
define( 'DB_NAME',     getenv( 'WORDPRESS_DB_NAME' )     ?: '' );
define( 'DB_USER',     getenv( 'WORDPRESS_DB_USER' )     ?: '' );
define( 'DB_PASSWORD', getenv( 'WORDPRESS_DB_PASSWORD' ) ?: '' );
define( 'DB_HOST',     getenv( 'WORDPRESS_DB_HOST' )     ?: 'localhost' );
define( 'DB_CHARSET',  'utf8mb4' );
define( 'DB_COLLATE',  '' );

$table_prefix = getenv( 'WORDPRESS_TABLE_PREFIX' ) ?: 'wp_';

// ── 2. Authentication keys & salts ───────────────────────────────
// Pull from env if set, otherwise fall back to a deterministic
// per-host default. The fallback lets the site BOOT on first deploy
// so an admin can sign in — but you MUST replace them in env or
// every admin session will reset.
$_snazzy_salts = [
    'AUTH_KEY',
    'SECURE_AUTH_KEY',
    'LOGGED_IN_KEY',
    'NONCE_KEY',
    'AUTH_SALT',
    'SECURE_AUTH_SALT',
    'LOGGED_IN_SALT',
    'NONCE_SALT',
];
foreach ( $_snazzy_salts as $_key ) {
    $_val = getenv( "WORDPRESS_{$_key}" );
    define(
        $_key,
        $_val !== false && $_val !== ''
            ? $_val
            : 'snazzy-insecure-' . $_key . '-SET-ME-IN-ENV'
    );
}
unset( $_snazzy_salts, $_key, $_val );

// ── 3. Debug / logging ───────────────────────────────────────────
$_wp_debug = (bool) getenv( 'WORDPRESS_DEBUG' );
define( 'WP_DEBUG',         $_wp_debug );
define( 'WP_DEBUG_LOG',     $_wp_debug );
define( 'WP_DEBUG_DISPLAY', false );
unset( $_wp_debug );

// Don't allow the admin to edit PHP files live — images are
// immutable, changes must go through a new deploy.
define( 'DISALLOW_FILE_EDIT', true );

// ── 4. Railway's edge terminates TLS — trust X-Forwarded-Proto ──
if (
    isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] )
    && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
) {
    $_SERVER['HTTPS'] = 'on';
}

// ── 5. Optional raw PHP escape hatch ─────────────────────────────
$_extra = getenv( 'WORDPRESS_CONFIG_EXTRA' );
if ( $_extra ) {
    // phpcs:ignore Squiz.PHP.Eval.Discouraged — intentional; used by
    // the official WP Docker image for the same purpose.
    eval( $_extra );
}
unset( $_extra );

// ── 6. Boot ──────────────────────────────────────────────────────
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
