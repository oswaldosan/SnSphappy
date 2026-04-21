<?php
/**
 * Technology Taxonomy for Case Studies.
 *
 * Allows editors to tag case studies by technology stack used
 * (e.g., WordPress, React, Shopify, Laravel).
 *
 * @package SnazzySprocket\Taxonomies
 */

namespace SnazzySprocket\Taxonomies;

class Technology {

    public static function register(): void {
        $labels = [
            'name'              => __( 'Technologies', 'snazzy-sprocket' ),
            'singular_name'     => __( 'Technology', 'snazzy-sprocket' ),
            'search_items'      => __( 'Search Technologies', 'snazzy-sprocket' ),
            'all_items'         => __( 'All Technologies', 'snazzy-sprocket' ),
            'edit_item'         => __( 'Edit Technology', 'snazzy-sprocket' ),
            'update_item'       => __( 'Update Technology', 'snazzy-sprocket' ),
            'add_new_item'      => __( 'Add New Technology', 'snazzy-sprocket' ),
            'new_item_name'     => __( 'New Technology Name', 'snazzy-sprocket' ),
            'menu_name'         => __( 'Technologies', 'snazzy-sprocket' ),
        ];

        register_taxonomy( 'technology', [ 'case_study' ], [
            'labels'            => $labels,
            'hierarchical'      => false,  // Tag-style (non-hierarchical)
            'public'            => true,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => [ 'slug' => 'technology', 'with_front' => false ],
        ] );
    }
}
