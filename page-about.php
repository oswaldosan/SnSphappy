<?php
/**
 * About Page template controller.
 *
 * Loads every ACF field backing the About page plus the full team roster.
 * Falls back to sensible Figma-spec defaults so a freshly installed site
 * (or one missing meta) still renders a complete page.
 *
 * Headlines that allow inline `<em>` / `<strong>` / `<br>` are run through
 * `snazzy_sanitize_headline()` (declared in front-page.php) and exposed as
 * `*_html` context keys, printed with `|raw` in the template.
 *
 * @package SnazzySprocket
 */

use Timber\Timber;

// `snazzy_sanitize_headline()` lives in `inc/helpers.php` and is
// loaded by functions.php at boot — NEVER require `front-page.php`
// here, its top-level `Timber::render()` would render the homepage
// on top of this page.

$context         = Timber::context();
$context['post'] = Timber::get_post();

/**
 * Tiny helper — pulls an ACF string field, trims it, and falls back.
 */
$acf_text = static function ( string $key, string $fallback = '' ): string {
	if ( ! function_exists( 'get_field' ) ) {
		return $fallback;
	}
	$value = get_field( $key );
	$value = is_string( $value ) ? trim( $value ) : '';
	return $value !== '' ? $value : $fallback;
};

/**
 * Sanitize + fallback for headlines that allow limited inline HTML.
 */
$acf_headline = static function ( string $key, string $fallback ): string {
	if ( ! function_exists( 'get_field' ) ) {
		return $fallback;
	}
	$value = snazzy_sanitize_headline( get_field( $key ) );
	return $value !== '' ? $value : $fallback;
};

// ---------------------------------------------------------------------
// Hero
// ---------------------------------------------------------------------
$context['about_eyebrow']       = $acf_text( 'about_eyebrow', 'About us' );
$context['about_headline_html'] = $acf_headline(
	'about_headline',
	"We're the team behind your next big launch"
);
$context['about_subheadline'] = $acf_text(
	'about_subheadline',
	"Snazzy Sprocket started with a simple belief: the web should be fast, beautiful, and accessible to everyone. Eight years later, we're still proving it — one project at a time."
);

// ---------------------------------------------------------------------
// Our Story
// ---------------------------------------------------------------------
$context['story_eyebrow']  = $acf_text( 'story_eyebrow', 'Our story' );
$context['story_headline'] = $acf_text(
	'story_headline',
	'From side project to full-service agency'
);
$story_content_raw         = function_exists( 'get_field' ) ? get_field( 'story_content' ) : '';
$context['story_content']  = is_string( $story_content_raw ) && $story_content_raw !== ''
	? $story_content_raw
	: implode( "\n", [
		'<p>What started as two developers freelancing out of a co-working space has grown into a team of 10 specialists spanning design, engineering, and strategy.</p>',
		"<p>We've worked with startups finding product-market fit, mid-market companies scaling their digital presence, and enterprise organizations modernizing legacy platforms.</p>",
		'<p>Our approach is simple: understand the business problem first, then build the right solution — not the trendiest one. We write clean code, ship on time, and pick up the phone when things break.</p>',
	] );

$story_image             = function_exists( 'get_field' ) ? get_field( 'story_image' ) : null;
$context['story_image']  = is_array( $story_image ) ? $story_image : null;

// ---------------------------------------------------------------------
// Values
// ---------------------------------------------------------------------
$context['values_eyebrow']  = $acf_text( 'values_eyebrow', 'Our values' );
$context['values_headline'] = $acf_text( 'values_headline', 'What drives every decision' );

$values_acf = function_exists( 'get_field' ) ? get_field( 'values' ) : null;
if ( is_array( $values_acf ) && ! empty( $values_acf ) ) {
	$context['values'] = $values_acf;
} else {
	$context['values'] = [
		[
			'value_title'       => 'Ship with Purpose',
			'value_description' => "Every feature, every line of code should solve a real problem for real users. If it doesn't move the needle, it doesn't ship.",
		],
		[
			'value_title'       => 'Radical Candor',
			'value_description' => 'We tell clients what they need to hear, not just what they want to hear. Honest collaboration builds better products.',
		],
		[
			'value_title'       => 'Craft Over Hype',
			'value_description' => "We'd rather build it right than build it fast. Quality compounds over time and outlasts every trend.",
		],
		[
			'value_title'       => 'Access for All',
			'value_description' => 'The web belongs to everyone. Accessibility and performance are non-negotiable baseline requirements.',
		],
	];
}

// ---------------------------------------------------------------------
// Team
// ---------------------------------------------------------------------
$context['team_eyebrow']     = $acf_text( 'team_eyebrow', 'The team' );
$context['team_headline']    = $acf_text( 'team_headline', 'Meet the people behind the pixels' );
$context['team_subheadline'] = $acf_text(
	'team_subheadline',
	'A tight-knit crew of designers, developers, and strategists who care deeply about the work.'
);

$context['team'] = Timber::get_posts( [
	'post_type'      => 'team_member',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
] );

// ---------------------------------------------------------------------
// Join CTA
// ---------------------------------------------------------------------
$context['join_headline']     = $acf_text( 'join_headline', 'Want to join the team?' );
$context['join_lede']         = $acf_text(
	'join_lede',
	"We're always looking for talented people who care about craft. Check out our open roles."
);
$context['join_button_label'] = $acf_text( 'join_button_label', 'View Open Positions' );
$context['join_button_url']   = $acf_text(
	'join_button_url',
	home_url( '/contact' )
);

Timber::render( 'page-about.twig', $context );
