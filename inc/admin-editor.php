<?php
/**
 * Admin editor tweaks.
 *
 * Disables the Gutenberg block editor for the `page` post type so
 * pages render ACF field groups as the primary editing surface.
 * The block editor is kept active for `post` (blog) and `case_study`
 * where long-form, block-driven content is the point.
 *
 * Pages fall back to the classic editor. If a given page does not
 * need a content body at all, the content meta box can be removed
 * per-page via the usual `remove_meta_box()` pattern.
 *
 * @package SnazzySprocket
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'snazzy_disable_block_editor_for_pages' ) ) {
	function snazzy_disable_block_editor_for_pages( bool $use_block_editor, string $post_type ): bool {
		if ( $post_type === 'page' ) {
			return false;
		}
		return $use_block_editor;
	}
}
add_filter( 'use_block_editor_for_post_type', 'snazzy_disable_block_editor_for_pages', 10, 2 );

/**
 * Remove the post content editor (classic TinyMCE) from the `page`
 * post type. Pages are fully ACF-driven, so the empty body editor
 * is visual noise and tempts editors to paste content that would
 * never render in Twig. ACF field groups become the only editing
 * surface.
 *
 * Note: this strips editor support globally for pages. If a future
 * page needs free-form body copy, add a WYSIWYG ACF field instead
 * of re-enabling the classic editor.
 */
if ( ! function_exists( 'snazzy_remove_editor_support_for_pages' ) ) {
	function snazzy_remove_editor_support_for_pages(): void {
		remove_post_type_support( 'page', 'editor' );
	}
}
add_action( 'init', 'snazzy_remove_editor_support_for_pages', 100 );
