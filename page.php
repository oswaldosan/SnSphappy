<?php
/**
 * Generic page template controller.
 *
 * @package SnazzySprocket
 */

use Timber\Timber;

$context         = Timber::context();
$context['post'] = Timber::get_post();

Timber::render( 'page.twig', $context );
