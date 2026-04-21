<?php
/**
 * Custom ACF location rule: Page Slug.
 *
 * ACF ships a `page` rule that compares `$post->ID` against a
 * numeric value. That is NOT portable across environments — page
 * IDs drift between local / staging / prod. This file adds a
 * `page_slug` rule so field groups can target a page by its
 * (stable) slug instead:
 *
 *   "param": "page_slug", "operator": "==", "value": "about"
 *
 * @package SnazzySprocket
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'snazzy_acf_page_slug_types' ) ) {
	function snazzy_acf_page_slug_types( array $choices ): array {
		$choices['Page']['page_slug'] = 'Page Slug';
		return $choices;
	}
}
add_filter( 'acf/location/rule_types', 'snazzy_acf_page_slug_types' );

if ( ! function_exists( 'snazzy_acf_page_slug_values' ) ) {
	function snazzy_acf_page_slug_values( array $choices ): array {
		$pages = get_pages( [
			'sort_column' => 'menu_order,post_title',
			'post_status' => [ 'publish', 'draft' ],
		] );
		foreach ( (array) $pages as $page ) {
			if ( ! empty( $page->post_name ) ) {
				$choices[ $page->post_name ] = $page->post_title . ' (' . $page->post_name . ')';
			}
		}
		return $choices;
	}
}
add_filter( 'acf/location/rule_values/page_slug', 'snazzy_acf_page_slug_values' );

if ( ! function_exists( 'snazzy_acf_page_slug_match' ) ) {
	function snazzy_acf_page_slug_match( bool $match, array $rule, array $options ): bool {
		$post_id = $options['post_id'] ?? 0;
		if ( ! $post_id ) {
			return false;
		}

		$post = get_post( $post_id );
		if ( ! $post || $post->post_type !== 'page' ) {
			return false;
		}

		$slug  = (string) $post->post_name;
		$value = (string) ( $rule['value'] ?? '' );

		return $rule['operator'] === '=='
			? $slug === $value
			: $slug !== $value;
	}
}
add_filter( 'acf/location/rule_match/page_slug', 'snazzy_acf_page_slug_match', 10, 3 );
