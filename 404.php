<?php
/**
 * 404 template controller.
 *
 * @package SnazzySprocket
 */

use Timber\Timber;

$context = Timber::context();
Timber::render( '404.twig', $context );
