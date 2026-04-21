<?php
/**
 * Case Studies Archive — listing page controller.
 *
 * Fetches all case studies and taxonomy terms for
 * the Alpine.js-powered front-end filters.
 *
 * @package SnazzySprocket
 */

use Timber\Timber;

$context = Timber::context();

// All case studies (no pagination — filtered client-side)
$context['case_studies'] = Timber::get_posts( [
    'post_type'      => 'case_study',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
] );

// Taxonomy terms for filter buttons
$context['industries']   = Timber::get_terms( [
    'taxonomy'   => 'industry',
    'hide_empty' => true,
    'orderby'    => 'name',
] );

$context['technologies'] = Timber::get_terms( [
    'taxonomy'   => 'technology',
    'hide_empty' => true,
    'orderby'    => 'name',
] );

// Archive hero copy — editable via the shadow "Case Studies" page
// (slug `case-studies`) where ACF group `group_case_studies_archive_fields`
// lives. The CPT archive owns the URL; the page record is admin-only.
$archive_page    = get_page_by_path( 'case-studies' );
$archive_page_id = $archive_page ? (int) $archive_page->ID : 0;

$context['archive_eyebrow']  = $archive_page_id
	? ( get_field( 'archive_eyebrow', $archive_page_id ) ?: 'Our work' )
	: 'Our work';

$context['archive_title']    = $archive_page_id
	? ( get_field( 'archive_headline', $archive_page_id ) ?: 'Case Studies' )
	: 'Case Studies';

$context['archive_description'] = $archive_page_id
	? ( get_field( 'archive_lede', $archive_page_id ) ?: 'A selection of the work we\'re most proud of — from brand strategy to full-stack builds.' )
	: 'A selection of the work we\'re most proud of — from brand strategy to full-stack builds.';

Timber::render( 'archive-case_study.twig', $context );
