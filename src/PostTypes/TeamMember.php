<?php
/**
 * Team Member Custom Post Type.
 *
 * @package SnazzySprocket\PostTypes
 */

namespace SnazzySprocket\PostTypes;

class TeamMember {

    public static function register(): void {
        $labels = [
            'name'               => __( 'Team Members', 'snazzy-sprocket' ),
            'singular_name'      => __( 'Team Member', 'snazzy-sprocket' ),
            'menu_name'          => __( 'Team', 'snazzy-sprocket' ),
            'add_new'            => __( 'Add New', 'snazzy-sprocket' ),
            'add_new_item'       => __( 'Add New Team Member', 'snazzy-sprocket' ),
            'edit_item'          => __( 'Edit Team Member', 'snazzy-sprocket' ),
            'new_item'           => __( 'New Team Member', 'snazzy-sprocket' ),
            'view_item'          => __( 'View Team Member', 'snazzy-sprocket' ),
            'search_items'       => __( 'Search Team Members', 'snazzy-sprocket' ),
            'not_found'          => __( 'No team members found.', 'snazzy-sprocket' ),
            'not_found_in_trash' => __( 'No team members found in Trash.', 'snazzy-sprocket' ),
            'all_items'          => __( 'All Team Members', 'snazzy-sprocket' ),
        ];

        register_post_type( 'team_member', [
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,  // No individual pages, shown on About
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,
            'query_var'          => false,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 6,
            'menu_icon'          => 'dashicons-groups',
            'supports'           => [
                'title',
                'thumbnail',
                'custom-fields',
                'page-attributes', // For ordering
            ],
        ] );
    }
}
