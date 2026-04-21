<?php
/**
 * Homepage template controller.
 *
 * Fetches hero fields (eyebrow, headline, stats), services, featured
 * case studies, and CTA copy, then renders `front-page.twig`.
 *
 * Headlines are sanitized with `wp_kses` so editors can inject a few
 * inline tags (em / strong / br / span) without opening the door to
 * arbitrary HTML. The sanitized strings land in Twig as `*_html` and
 * are printed with `|raw` in the template.
 *
 * @package SnazzySprocket
 */

use Timber\Timber;

// `snazzy_sanitize_headline()` / `snazzy_hero_allowed_html()` live in
// `inc/helpers.php` and are loaded by functions.php at boot.

$context         = Timber::context();
$context['post'] = Timber::get_post();

// ---------------------------------------------------------------------
// Hero
// ---------------------------------------------------------------------
$hero_headline_raw = function_exists( 'get_field' ) ? get_field( 'hero_headline' ) : '';
$hero_headline     = snazzy_sanitize_headline( $hero_headline_raw );

$context['hero_eyebrow'] = function_exists( 'get_field' )
	? sanitize_text_field( (string) get_field( 'hero_eyebrow' ) )
	: '';

$context['hero_headline_html'] = $hero_headline !== ''
	? $hero_headline
	: 'We engineer<br>websites that<br><em>drive</em> results';

$context['hero_subheadline'] = function_exists( 'get_field' )
	? (string) get_field( 'hero_subheadline' )
	: '';

// Stats repeater — fall back to the Figma defaults so the sidebar
// is never empty on a fresh install.
$hero_stats_acf = function_exists( 'get_field' ) ? get_field( 'hero_stats' ) : [];

if ( is_array( $hero_stats_acf ) && ! empty( $hero_stats_acf ) ) {
	$context['hero_stats'] = $hero_stats_acf;
} else {
	$context['hero_stats'] = [
		[ 'stat_value' => '120+',  'stat_label' => 'Projects Delivered' ],
		[ 'stat_value' => '98%',   'stat_label' => 'Client Satisfaction' ],
		[ 'stat_value' => '8 yrs', 'stat_label' => 'In Business' ],
		[ 'stat_value' => '15',    'stat_label' => 'Industry Awards' ],
	];
}

// ---------------------------------------------------------------------
// Services
// ---------------------------------------------------------------------
$context['services_eyebrow'] = function_exists( 'get_field' )
	? sanitize_text_field( (string) get_field( 'services_eyebrow' ) )
	: '';

$context['services_headline_html'] = snazzy_sanitize_headline(
	function_exists( 'get_field' ) ? get_field( 'services_headline' ) : ''
);

$context['services_subheadline'] = function_exists( 'get_field' )
	? (string) get_field( 'services_subheadline' )
	: '';

$services_acf = function_exists( 'get_field' ) ? get_field( 'services' ) : null;

if ( is_array( $services_acf ) && ! empty( $services_acf ) ) {
	$context['services'] = $services_acf;
} else {
	$context['services'] = [
		[
			'service_marker'      => '—',
			'service_title'       => 'UX & UI Design',
			'service_description' => 'Research-driven design systems that balance aesthetics with usability. We create interfaces people actually enjoy using.',
		],
		[
			'service_marker'      => '—',
			'service_title'       => 'Custom Development',
			'service_description' => 'Bespoke WordPress themes and applications built for performance, accessibility, and long-term maintainability.',
		],
		[
			'service_marker'      => '—',
			'service_title'       => 'SEO & Strategy',
			'service_description' => 'Data-backed strategies that improve visibility and convert visitors. Semantic markup and technical SEO baked in from day one.',
		],
		[
			'service_marker'      => '—',
			'service_title'       => 'Managed Hosting',
			'service_description' => 'Enterprise-grade hosting, security monitoring, and ongoing maintenance so you can focus on running your business.',
		],
		[
			'service_marker'      => '—',
			'service_title'       => 'Responsive Engineering',
			'service_description' => 'Every pixel considered across every breakpoint. Mobile-first development that performs at any screen size.',
		],
		[
			'service_marker'      => '—',
			'service_title'       => 'Accessibility',
			'service_description' => 'WCAG 2.1 AA compliance built into every project. Inclusive design isn\'t an afterthought — it\'s how we work.',
		],
	];
}

// ---------------------------------------------------------------------
// Featured case studies
// ---------------------------------------------------------------------
if ( function_exists( 'get_field' ) && get_field( 'featured_case_studies' ) ) {
	$featured_ids             = get_field( 'featured_case_studies' );
	$context['featured_work'] = Timber::get_posts(
		[
			'post_type'      => 'case_study',
			'post__in'       => $featured_ids,
			'orderby'        => 'post__in',
			'posts_per_page' => 3,
		]
	);
} else {
	$context['featured_work'] = Timber::get_posts(
		[
			'post_type'      => 'case_study',
			'posts_per_page' => 3,
			'orderby'        => 'date',
			'order'          => 'DESC',
		]
	);
}

// ---------------------------------------------------------------------
// CTA
// ---------------------------------------------------------------------
$context['cta_eyebrow'] = function_exists( 'get_field' )
	? sanitize_text_field( (string) get_field( 'cta_eyebrow' ) )
	: '';

$context['cta_headline_html'] = snazzy_sanitize_headline(
	function_exists( 'get_field' ) ? get_field( 'cta_headline' ) : ''
);

$context['cta_subheadline'] = function_exists( 'get_field' )
	? (string) get_field( 'cta_subheadline' )
	: '';

Timber::render( 'front-page.twig', $context );
