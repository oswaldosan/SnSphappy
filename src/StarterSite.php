<?php
/**
 * The main theme class. Handles setup, global context,
 * asset enqueuing, and Twig filter/function registration.
 *
 * @package SnazzySprocket
 */

namespace SnazzySprocket;

use Timber\Timber;
use Timber\Site;
use Twig\Environment;
use SnazzySprocket\PostTypes\CaseStudy;
use SnazzySprocket\PostTypes\TeamMember;
use SnazzySprocket\Taxonomies\Industry;
use SnazzySprocket\Taxonomies\Technology;

class StarterSite extends Site {

    public function __construct() {
        // WordPress hooks
        add_action( 'after_setup_theme', [ $this, 'theme_setup' ] );
        add_action( 'init',              [ $this, 'register_content_types' ] );
        add_action( 'wp_enqueue_scripts',[ $this, 'enqueue_assets' ] );
        add_action( 'acf/init',          [ $this, 'register_acf_options' ] );

        // Timber filters
        add_filter( 'timber/context',    [ $this, 'add_to_context' ] );
        add_filter( 'timber/twig',       [ $this, 'add_to_twig' ] );

        parent::__construct();
    }

    /**
     * Theme supports and nav menus.
     */
    public function theme_setup(): void {
        // Theme supports
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'menus' );
        add_theme_support( 'html5', [
            'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script',
        ] );
        add_theme_support( 'align-wide' );
        add_theme_support( 'responsive-embeds' );
        add_theme_support( 'editor-styles' );
        add_theme_support( 'wp-block-styles' );

        // Custom image sizes
        add_image_size( 'card-thumbnail',  600, 400, true );
        add_image_size( 'hero-image',     1600, 900, true );
        add_image_size( 'team-headshot',   400, 400, true );

        // Navigation menus
        register_nav_menus( [
            'primary'   => __( 'Primary Navigation', 'snazzy-sprocket' ),
            'footer'    => __( 'Footer Navigation',  'snazzy-sprocket' ),
        ] );
    }

    /**
     * Register Custom Post Types and Taxonomies.
     */
    public function register_content_types(): void {
        CaseStudy::register();
        TeamMember::register();
        Industry::register();
        Technology::register();
    }

    /**
     * Enqueue Tailwind CSS and app JS — works with Vite dev server or built assets.
     */
    public function enqueue_assets(): void {
        $theme_uri = get_template_directory_uri();
        $theme_dir = get_template_directory();

        // Google Fonts — Syne (display) + DM Sans (body).
        // Weights pulled from the Figma typography spec:
        //   Syne: 700 (bold), 800 (extra-bold)
        //   DM Sans: 400, 500, 600, 700 (+ italic 400)
        wp_enqueue_style(
            'snazzy-google-fonts',
            'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Syne:wght@700;800&display=swap',
            [],
            null
        );

        // Check for Vite manifest (production build)
        $manifest_path = $theme_dir . '/dist/.vite/manifest.json';

        if ( file_exists( $manifest_path ) ) {
            // Production — use built assets
            $manifest = json_decode( file_get_contents( $manifest_path ), true );

            if ( isset( $manifest['src/css/main.css'] ) ) {
                wp_enqueue_style(
                    'snazzy-main',
                    $theme_uri . '/dist/' . $manifest['src/css/main.css']['file'],
                    [],
                    null
                );
            }

            if ( isset( $manifest['src/js/app.js'] ) ) {
                wp_enqueue_script(
                    'snazzy-app',
                    $theme_uri . '/dist/' . $manifest['src/js/app.js']['file'],
                    [],
                    null,
                    true
                );
            }
        } else {
            // Development — load from Vite dev server
            // If Vite isn't running, fall back to the CSS file directly
            wp_enqueue_style(
                'snazzy-main',
                $theme_uri . '/dist/assets/main.css',
                [],
                filemtime( $theme_dir . '/src/css/main.css' )
            );

            wp_enqueue_script(
                'snazzy-app',
                $theme_uri . '/dist/assets/app.js',
                [],
                filemtime( $theme_dir . '/src/js/app.js' ),
                true
            );
        }
    }

    /**
     * Global Timber context — available in every Twig template.
     */
    public function add_to_context( array $context ): array {
        // Menus
        $context['menu_primary'] = Timber::get_menu( 'primary' );
        $context['menu_footer']  = Timber::get_menu( 'footer' );

        // Site info
        $context['site']         = $this;
        $context['current_year'] = date( 'Y' );

        // Theme asset URL — reliable across Timber versions; prefer this
        // over `site.theme.link` inside Twig templates when you need to
        // point at static assets under /assets.
        $context['theme_url']    = get_stylesheet_directory_uri();

        // ACF Options page fields (if ACF Pro is available)
        if ( function_exists( 'get_field' ) ) {
            $context['options'] = [
                'phone'          => get_field( 'company_phone', 'option' ) ?: '+1 (555) 123-4567',
                'email'          => get_field( 'company_email', 'option' ) ?: 'hello@snazzysprocket.dev',
                'address'        => get_field( 'company_address', 'option' ) ?: '123 Innovation Drive, Suite 400, San Francisco, CA 94107',
                'social_twitter' => get_field( 'social_twitter', 'option' ) ?: '#',
                'social_linkedin'=> get_field( 'social_linkedin', 'option' ) ?: '#',
                'social_github'  => get_field( 'social_github', 'option' ) ?: '#',
                'social_dribbble'=> get_field( 'social_dribbble', 'option' ) ?: '#',
                'footer_text'    => get_field( 'footer_text', 'option' ) ?: 'Crafting digital experiences that move the needle.',
            ];
        }

        return $context;
    }

    /**
     * Register ACF Options page for global site settings.
     */
    public function register_acf_options(): void {
        if ( function_exists( 'acf_add_options_page' ) ) {
            acf_add_options_page( [
                'page_title' => 'Snazzy Sprocket Settings',
                'menu_title' => 'Site Settings',
                'menu_slug'  => 'site-settings',
                'capability' => 'edit_posts',
                'icon_url'   => 'dashicons-admin-generic',
                'redirect'   => false,
            ] );
        }
    }

    /**
     * Add custom Twig filters, functions, and globals.
     *
     * The `timber/twig` hook can be dispatched more than once per
     * request. Twig throws a LogicException if the same name is
     * re-registered, or if the Environment's extensions have already
     * been initialized. We swallow both safely so subsequent
     * invocations are a no-op — do NOT call `getFilter()` here, that
     * triggers extension initialization and blocks further adds.
     */
    public function add_to_twig( Environment $twig ): Environment {
        // `{{ text|excerpt(120) }}` — plain-text, HTML-stripped,
        // ellipsis-terminated. Timber ships `{{ post.excerpt }}` for
        // Post objects; this filter handles raw strings (e.g. ACF
        // textareas rendered in meta tags).
        try {
            $twig->addFilter(
                new \Twig\TwigFilter(
                    'excerpt',
                    static function ( string $text, int $length = 120 ): string {
                        $text = wp_strip_all_tags( $text );
                        if ( mb_strlen( $text ) <= $length ) {
                            return $text;
                        }
                        return rtrim( mb_substr( $text, 0, $length ) ) . '…';
                    }
                )
            );
        } catch ( \LogicException $e ) {
            // Already registered or extensions frozen — safe to ignore.
        }

        // Twig globals — available in every template, even those
        // rendered through `{% include ... only %}` where the parent
        // context is intentionally blocked.
        try {
            $twig->addGlobal( 'theme_url', get_stylesheet_directory_uri() );
        } catch ( \LogicException $e ) {
            // Environment frozen — safe to ignore.
        }

        return $twig;
    }
}
