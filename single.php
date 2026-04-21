<?php
/**
 * Default single post template controller.
 *
 * @package SnazzySprocket
 */

use Timber\Timber;

$context         = Timber::context();
$context['post'] = Timber::get_post();

Timber::render( 'single.twig', $context );
