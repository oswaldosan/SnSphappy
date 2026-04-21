<?php
/**
 * Snazzy Sprocket Theme — functions.php
 *
 * Bootstraps Composer autoloading, initializes Timber 2.x,
 * and hands off to the SnazzySprocket\StarterSite class.
 *
 * Important: this theme ships Timber 2.x via Composer. Do NOT
 * activate the legacy `timber-library` plugin from WordPress.org
 * — it registers a Timber 1.x `Timber\Timber` class that shadows
 * the Composer one and breaks the theme (protected `init()` etc.).
 *
 * @package SnazzySprocket
 */

// 1. Load Composer autoloader (includes Timber + PSR-4 classes).
$snazzy_autoload = __DIR__ . '/vendor/autoload.php';
if ( ! file_exists( $snazzy_autoload ) ) {
	add_action( 'admin_notices', static function () {
		echo '<div class="error"><p>';
		echo '<strong>Snazzy Sprocket:</strong> Run <code>composer install</code> in the theme directory.';
		echo '</p></div>';
	} );
	return;
}
require_once $snazzy_autoload;

// 2. Guard against the legacy `timber-library` plugin (Timber 1.x).
//    If detected, bail with a clear message instead of a fatal error.
if ( ! class_exists( 'Timber\\Timber' ) ) {
	add_action( 'admin_notices', static function () {
		echo '<div class="error"><p>';
		echo '<strong>Snazzy Sprocket:</strong> Timber was not loaded. Check that <code>vendor/</code> is present.';
		echo '</p></div>';
	} );
	return;
}
if ( ! is_callable( [ 'Timber\\Timber', 'init' ] ) ) {
	add_action( 'admin_notices', static function () {
		echo '<div class="error"><p>';
		echo '<strong>Snazzy Sprocket:</strong> A conflicting Timber 1.x install was detected. ';
		echo 'Deactivate the <em>Timber Library</em> plugin — this theme bundles Timber 2.x.';
		echo '</p></div>';
	} );
	return;
}

// 3. Initialize Timber 2.x and set template directories.
Timber\Timber::init();
Timber::$dirname = [ 'views', 'views/partials' ];

// 4. Shared helpers used across page controllers.
//    Loaded here (once) so no controller ever needs to require another
//    controller just to pull a helper — doing that causes the required
//    controller's top-level `Timber::render()` to also run and renders
//    two pages stacked together.
require_once __DIR__ . '/inc/helpers.php';

// 5. Custom ACF location rule: `page_slug` (portable across envs).
//    ACF's stock `page` rule compares numeric post IDs which drift
//    between local / staging / prod. This lets JSON field groups
//    target pages by slug (e.g. "about", "contact") instead.
require_once __DIR__ . '/inc/acf-location.php';

// 6. Admin editor tweaks — Gutenberg off for pages, on for posts /
//    case studies. Pages are ACF-driven, so the block editor would
//    only be visual noise above the field groups.
require_once __DIR__ . '/inc/admin-editor.php';

// 7. Boot the theme.
new SnazzySprocket\StarterSite();
