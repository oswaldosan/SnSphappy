<?php
/**
 * Industry Taxonomy for Case Studies.
 *
 * Allows editors to categorize case studies by client industry
 * (e.g., Healthcare, FinTech, E-commerce, Education).
 *
 * @package SnazzySprocket\Taxonomies
 */

namespace SnazzySprocket\Taxonomies;

class Industry {

    public static function register(): void {
        $labels = [
            'name'              => __( 'Industries', 'snazzy-sprocket' ),
            'singular_name'     => __( 'Industry', 'snazzy-sprocket' ),
            'search_items'      => __( 'Search Industries', 'snazzy-sprocket' ),
            'all_items'         => __( 'All Industries', 'snazzy-sprocket' ),
            'edit_item'         => __( 'Edit Industry', 'snazzy-sprocket' ),
            'update_item'       => __( 'Update Industry', 'snazzy-sprocket' ),
            'add_new_item'      => __( 'Add New Industry', 'snazzy-sprocket' ),
            'new_item_name'     => __( 'New Industry Name', 'snazzy-sprocket' ),
            'menu_name'         => __( 'Industries', 'snazzy-sprocket' ),
        ];

        register_taxonomy( 'industry', [ 'case_study' ], [
            'labels'            => $labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => [ 'slug' => 'industry', 'with_front' => false ],
        ] );
    }
}
