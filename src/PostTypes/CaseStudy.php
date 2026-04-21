<?php
/**
 * Case Study Custom Post Type.
 *
 * @package SnazzySprocket\PostTypes
 */

namespace SnazzySprocket\PostTypes;

class CaseStudy {

    public static function register(): void {
        $labels = [
            'name'               => __( 'Case Studies', 'snazzy-sprocket' ),
            'singular_name'      => __( 'Case Study', 'snazzy-sprocket' ),
            'menu_name'          => __( 'Case Studies', 'snazzy-sprocket' ),
            'add_new'            => __( 'Add New', 'snazzy-sprocket' ),
            'add_new_item'       => __( 'Add New Case Study', 'snazzy-sprocket' ),
            'edit_item'          => __( 'Edit Case Study', 'snazzy-sprocket' ),
            'new_item'           => __( 'New Case Study', 'snazzy-sprocket' ),
            'view_item'          => __( 'View Case Study', 'snazzy-sprocket' ),
            'search_items'       => __( 'Search Case Studies', 'snazzy-sprocket' ),
            'not_found'          => __( 'No case studies found.', 'snazzy-sprocket' ),
            'not_found_in_trash' => __( 'No case studies found in Trash.', 'snazzy-sprocket' ),
            'all_items'          => __( 'All Case Studies', 'snazzy-sprocket' ),
        ];

        register_post_type( 'case_study', [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,  // Gutenberg + REST API support
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'case-studies', 'with_front' => false ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-portfolio',
            'supports'           => [
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'custom-fields',
                'revisions',
            ],
            'template'           => [
                [ 'core/paragraph', [ 'placeholder' => 'Write the case study overview here…' ] ],
            ],
        ] );
    }
}
