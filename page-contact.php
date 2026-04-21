<?php
/**
 * Contact Page template controller.
 *
 * Passes page content, contact info from ACF options,
 * and the Contact Form 7 shortcode.
 *
 * @package SnazzySprocket
 */

use Timber\Timber;

$context         = Timber::context();
$context['post'] = Timber::get_post();

// Contact Form 7 shortcode — stored in ACF or use default
if ( function_exists( 'get_field' ) && get_field( 'contact_form_shortcode' ) ) {
    $context['contact_form'] = do_shortcode( get_field( 'contact_form_shortcode' ) );
} else {
    // Fallback: render the first CF7 form found, or a placeholder
    $cf7_forms = get_posts( [
        'post_type'      => 'wpcf7_contact_form',
        'posts_per_page' => 1,
    ] );
    if ( ! empty( $cf7_forms ) ) {
        $context['contact_form'] = do_shortcode( '[contact-form-7 id="' . $cf7_forms[0]->ID . '"]' );
    } else {
        $context['contact_form'] = '<p class="text-muted">Contact form will appear here once configured.</p>';
    }
}

Timber::render( 'page-contact.twig', $context );
