<?php
/**
 * Single Case Study template controller.
 *
 * Fetches the case study, its ACF fields, related taxonomies,
 * and up to 2 related case studies for the "More Work" section.
 *
 * @package SnazzySprocket
 */

use Timber\Timber;

$context         = Timber::context();
$post            = Timber::get_post();
$context['post'] = $post;

// Related case studies — same industry, excluding current
$industries = wp_get_post_terms( $post->ID, 'industry', [ 'fields' => 'ids' ] );

$context['related_work'] = Timber::get_posts( [
    'post_type'      => 'case_study',
    'posts_per_page' => 2,
    'post__not_in'   => [ $post->ID ],
    'tax_query'      => ! empty( $industries ) ? [
        [
            'taxonomy' => 'industry',
            'field'    => 'term_id',
            'terms'    => $industries,
        ],
    ] : [],
] );

Timber::render( 'single-case_study.twig', $context );
