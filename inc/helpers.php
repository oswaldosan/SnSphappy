<?php
/**
 * Shared helpers for page controllers.
 *
 * Loaded once from `functions.php`. NEVER require `front-page.php` or
 * any other `page-*.php` file from another controller — those files
 * have top-level executable code that ends in `Timber::render(...)`,
 * which would render a second page alongside the intended one.
 *
 * @package SnazzySprocket
 */

/**
 * Allowed inline HTML for hero / services / CTA headlines.
 *
 * Editors can wrap a word in `<em>` or `<strong>` to highlight it in
 * accent green (CSS forces `font-style: normal` so emphasis reads as
 * color-only), drop a `<br>` to force a line break, or use a classed
 * `<span>` to opt into a more specific treatment.
 *
 * @return array<string, array<string, array<int, string>|bool>>
 */
if ( ! function_exists( 'snazzy_hero_allowed_html' ) ) {
	function snazzy_hero_allowed_html(): array {
		return [
			'em'     => [],
			'strong' => [],
			'br'     => [],
			'span'   => [ 'class' => true ],
		];
	}
}

/**
 * Safely sanitize an ACF text/textarea value that is allowed to
 * contain a small set of inline tags.
 *
 * @param mixed $value Raw value from ACF / post meta.
 */
if ( ! function_exists( 'snazzy_sanitize_headline' ) ) {
	function snazzy_sanitize_headline( $value ): string {
		if ( ! is_string( $value ) || $value === '' ) {
			return '';
		}
		return wp_kses( $value, snazzy_hero_allowed_html() );
	}
}
