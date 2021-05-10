<?php
/**
 * Plugin Name: WPH Server Cache Helper
 * Plugin URI: https://wpherc.com/
 * Description: Automatic exclusion of pages from cache when using server cache, Nginx FastCGI or Redis Page Cache.
 * Version: 1.0
 * Author: @angelfplaza
 * Author URI: https://angelfplaza.com
 * Text Domain: wph-server-cache-helper
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
/**
 * Avoid direct calls
*/
defined('ABSPATH') or die("No direct requests for security reasons.");

/*
 * Require plugin.php
 */
if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}
/**
 * Activation and deactivation will clear most known cache systems
*/
function wph_server_cache_helper_on_activation()  {
  if ( ! current_user_can( 'activate_plugins' ) )
  return;
  $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
  check_admin_referer( "activate-plugin_{$plugin}" );

    // Server Cache Helper plugins
    // WP Engine.
    if ( class_exists( 'WpeCommon' ) ) {
        if ( method_exists( 'WpeCommon', 'purge_memcached' ) ) {
            WpeCommon::purge_memcached();
        }
        if ( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) ) {
            WpeCommon::clear_maxcdn_cache();
        }
        if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {
            WpeCommon::purge_varnish_cache();
        }
    }
    // SG Optimizer.
    if ( class_exists( 'SiteGround_Optimizer\Supercacher\Supercacher' ) ) {
        SiteGround_Optimizer\Supercacher\Supercacher::purge_cache();
    }
    // Kinsta Cache.
    if ( class_exists( 'Kinsta\Cache' ) ) {
        // $kinsta_cache object already created by Kinsta cache.php file.
        global $kinsta_cache;
        $kinsta_cache->kinsta_cache_purge->purge_complete_full_page_cache();
    }
    // Nginx helper Plugin (Gridpane and others)
    if ( class_exists( 'Nginx_Helper' ) ) {
    do_action('rt_nginx_helper_purge_all');
    }

    // Clear cache of known plugins. Just in case. 
    // LiteSpeed Cache.
    if ( class_exists( 'LiteSpeed_Cache_Purge' ) ) {
        LiteSpeed_Cache_Purge::all();
    }
    // Clear Cachify Cache
    if ( has_action('cachify_flush_cache') ) {
    do_action('cachify_flush_cache');
    }
    // Clear Super Cache
    if ( function_exists( 'wp_cache_clear_cache' ) ) {
    ob_end_clean();
    wp_cache_clear_cache();
    }
    // WP Super Cache. Not sure if this is the right one. Keep it just in case.
    if ( function_exists( 'wp_cache_clean_cache' ) ) {
        global $file_prefix;
        empty( $file_prefix ) ? $file_prefix = 'wp-cache-' : $file_prefix;
        wp_cache_clean_cache( $file_prefix, true );
    }
    // Clear W3 Total Cache
    if ( function_exists( 'w3tc_pgcache_flush' ) ) {
    ob_end_clean();
    w3tc_pgcache_flush();
    }
    // Clear WP-Rocket Cache
    if ( function_exists( 'rocket_clean_domain' ) ) {
    rocket_clean_domain();
    }
    // Clear WP Fastest Cache
    if ( isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache') ) {
    $GLOBALS['wp_fastest_cache']->deleteCache();
    }
    // WP Fastest Cache alternative method
    if ( class_exists( 'WpFastestCache' ) ) {
        $wpfc = new WpFastestCache();
        $wpfc->deleteCache();
    }
    // Autoptimize.
    if ( class_exists( 'autoptimizeCache' ) ) {
        autoptimizeCache::clearall();
    }
    // Comet Cache.
    if ( class_exists( 'comet_cache' ) ) {
        comet_cache::clear();
    }
    // Hummingbird.
    if ( class_exists( 'Hummingbird\Core\Filesystem' ) ) {
        // I would use Hummingbird\WP_Hummingbird::flush_cache( true, false ) instead, but it's disabling the page cache option in Hummingbird settings.
        Hummingbird\Core\Filesystem::instance()->clean_up();
    }
    // WP-Optimize.
    if ( class_exists( 'WP_Optimize_Cache_Commands' ) ) {
        // This function returns a response, so I'm assigning it to a variable to prevent unexpected output to the screen.
        $response = WP_Optimize_Cache_Commands::purge_page_cache();
    }
    // WP-Optimize minification files have a different cache.
    if ( class_exists( 'WP_Optimize_Minify_Cache_Functions' ) ) {
        // This function returns a response, so I'm assigning it to a variable to prevent unexpected output to the screen.
        $response = WP_Optimize_Minify_Cache_Functions::purge();
    }
}

function wph_server_cache_helper_on_deactivation() {
  if ( ! current_user_can( 'activate_plugins' ) )
  return;
  $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
  check_admin_referer( "deactivate-plugin_{$plugin}" );
    // Server Cache Helper plugins
    // WP Engine.
    if ( class_exists( 'WpeCommon' ) ) {
        if ( method_exists( 'WpeCommon', 'purge_memcached' ) ) {
            WpeCommon::purge_memcached();
        }
        if ( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) ) {
            WpeCommon::clear_maxcdn_cache();
        }
        if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {
            WpeCommon::purge_varnish_cache();
        }
    }
    // SG Optimizer.
    if ( class_exists( 'SiteGround_Optimizer\Supercacher\Supercacher' ) ) {
        SiteGround_Optimizer\Supercacher\Supercacher::purge_cache();
    }
    // Kinsta Cache.
    if ( class_exists( 'Kinsta\Cache' ) ) {
        // $kinsta_cache object already created by Kinsta cache.php file.
        global $kinsta_cache;
        $kinsta_cache->kinsta_cache_purge->purge_complete_full_page_cache();
    }
    // Nginx helper Plugin (Gridpane and others)
    if ( class_exists( 'Nginx_Helper' ) ) {
    do_action('rt_nginx_helper_purge_all');
    }

    // Clear cache of plugins. Just in case. 
    // LiteSpeed Cache.
    if ( class_exists( 'LiteSpeed_Cache_Purge' ) ) {
        LiteSpeed_Cache_Purge::all();
    }
    // Clear Cachify Cache
    if ( has_action('cachify_flush_cache') ) {
    do_action('cachify_flush_cache');
    }
    // Clear Super Cache
    if ( function_exists( 'wp_cache_clear_cache' ) ) {
    ob_end_clean();
    wp_cache_clear_cache();
    }
    // WP Super Cache.
    if ( function_exists( 'wp_cache_clean_cache' ) ) {
        global $file_prefix;

        empty( $file_prefix ) ? $file_prefix = 'wp-cache-' : $file_prefix;
        wp_cache_clean_cache( $file_prefix, true );
    }
    // Clear W3 Total Cache
    if ( function_exists( 'w3tc_pgcache_flush' ) ) {
    ob_end_clean();
    w3tc_pgcache_flush();
    }
    // Clear WP-Rocket Cache
    if ( function_exists( 'rocket_clean_domain' ) ) {
    rocket_clean_domain();
    }
    // Clear WP Fastest Cache
    if ( isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache') ) {
    $GLOBALS['wp_fastest_cache']->deleteCache();
    }
    // WP Fastest Cache alternative method
    if ( class_exists( 'WpFastestCache' ) ) {
        $wpfc = new WpFastestCache();
        $wpfc->deleteCache();
    }
    // Autoptimize.
    if ( class_exists( 'autoptimizeCache' ) ) {
        autoptimizeCache::clearall();
    }
    // Comet Cache.
    if ( class_exists( 'comet_cache' ) ) {
        comet_cache::clear();
    }
    // Hummingbird.
    if ( class_exists( 'Hummingbird\Core\Filesystem' ) ) {
        // I would use Hummingbird\WP_Hummingbird::flush_cache( true, false ) instead, but it's disabling the page cache option in Hummingbird settings.
        Hummingbird\Core\Filesystem::instance()->clean_up();
    }
    // WP-Optimize.
    if ( class_exists( 'WP_Optimize_Cache_Commands' ) ) {
        // This function returns a response, so I'm assigning it to a variable to prevent unexpected output to the screen.
        $response = WP_Optimize_Cache_Commands::purge_page_cache();
    }
    // WP-Optimize minification files have a different cache.
    if ( class_exists( 'WP_Optimize_Minify_Cache_Functions' ) ) {
        // This function returns a response, so I'm assigning it to a variable to prevent unexpected output to the screen.
        $response = WP_Optimize_Minify_Cache_Functions::purge();
    }
}

register_activation_hook( __FILE__, 'wph_server_cache_helper_on_activation' );
register_deactivation_hook( __FILE__, 'wph_server_cache_helper_on_deactivation' );

/**
 * Localization
*/
load_plugin_textdomain( 'wph-server-cache-helper', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );


/**
 * Init
*/

function wph_server_cache_helper() {
     if ( !defined( 'WP_CLI' ) || !WP_CLI ) {   // if we are running wp cli we do not run our code
        add_filter( 'template_redirect', 'wph_donotcache_and_headers', 99999 );
     }  
}
add_action('init', 'wph_server_cache_helper', 99999);


/**
 *  Prevent caching on server if the post/page has the DONOTCACHEPAGE constant defined.
 */
function wph_donotcache_and_headers() {
    global $post;
    // We check if this is a single posts (any type) or a page. If not, do nothing.
    if ( ! is_singular() ) {
        return;
    }
    // here we check if there is a "DONOTCACHEPAGE" defined constant. If we do not find it, then we do nothing.
    if ( ! defined( 'DONOTCACHEPAGE' ) ) {
       return;
    }
    /**
    * Extra Headers. We apply this to all. 
    */
    // Sets the nocache headers to prevent caching by browsers and some server and proxies that respect those headers.
    nocache_headers();
    // Adding no-store value to Cache-Control header for additional enforcement.
    add_filter( 'nocache_headers', function() {
        return array(
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
            'Expires'       => 'Wed, 14 Sep 1977 05:00:00 GMT',
            'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
            'X-Accel-Expires' => '0'
        );
    } );

    // Populate other constants in case they have not been set. This will only prevent it to be cached by plugins but can be useful.
    // We do not check DONOTCACHEPAGE becuase if it is not defined, we won´t be here.
    // Prevent object caching.
    if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
        define( 'DONOTCACHEOBJECT', true );
    }
    // Prevent database caching.
    if ( ! defined( 'DONOTCACHEDB' ) ) {
        define( 'DONOTCACHEDB', true );
    }
    // LiteSpeed Page Cache.
    if ( ! defined( 'LSCACHE_NO_CACHE' ) ) {
        define( 'LSCACHE_NO_CACHE', true );
    }
    // LiteSpeed Object Cache.
    if ( ! defined( 'LSCWP_OBJECT_CACHE' ) ) {
        define( 'LSCWP_OBJECT_CACHE', false );
    }

    // WP Engine.
    if ( class_exists( 'WpeCommon' ) ) {
        /*
         * We use their special cookie for exclusion.
         * Value is not important really, but I'm using a nonce anyway.
         */
        setcookie( 'wpengine_no_cache', wp_create_nonce( 'wphercules' ), 0, "/$post->post_name/" ); // Will expire at the end of the session (when the browser closes).
        header( 'wph-no-cache: true', false ); // WPengine does not have any custom headers. We are adding WPHercules No Cache header just in case you can talk with their support team to add this.  
        return; // Header and cookie setup, nothing else to do, we can return.
    }
    // Kinsta Cache. 
    if ( class_exists( 'Kinsta\Cache' ) && ! is_admin() && ! is_user_logged_in() ) {
        /*
         * https://kinsta.com/blog/wordpress-cookies-php-sessions/#cookies-wordpress-caching
         * They only exclude the cookie "woocommerce_items_in_cart" and the "edd_items_in_cart" and the login ones.
         */
        setcookie( 'wordpress_logged_in_' . wp_hash( 'wpherculesthisisarandomelongtext' ), 1, 0, "/$post->post_name/" ); // Will expire at the end of the session (when the browser closes).
        header( 'wph-no-cache: true', false ); // Kinsta does not have any custom headers. Adding WPHercules No Cache header just in case you can talk with their support team to add this. 
        return; // Header and cookie setup, nothing else to do, we can return.
    }
    // SG Optimizer cookie. This will turn off both x-cache and proxy-cache.
    if ( class_exists( 'SiteGround_Optimizer\Supercacher\Supercacher' ) ) {
        header( 'X-Cache-Enabled: False', true );
        setcookie( 'wpSGCacheBypass', 1, 0, "/$post->post_name/" ); // Will expire at the end of the session (when the browser closes).
        return; // Header and cookie setup, nothing else to do, we can return.
    }
    // Gridpane cookie and header and any other Nginx servers.
    if (  defined( 'GRIDPANE' ) ) { // Gridpane uses Nginx Helper plugin but also their own constand on wp-config.php file.
        header( 'skip-gp-cache: true', false ); // Gridpane Cache header.
        setcookie( 'wordpress_no_cache', 1, 0, "/$post->post_name/", '', true, true ); // Will expire at the end of the session (when the browser closes).
        return; // Header and cookie setup, nothing else to do, we can return..
    }
    // if we are here, it means we have not recognised the server. We run our custom solution then (for example Runcloud or any custom made server).
    // By default we setup the cookie named wph_no_cache, and we need to add it to the server configuration to make sure it is excluded.
    setcookie( 'wph_no_cache', 1, 0, "/$post->post_name/", '', true, true ); // Will expire at the end of the session (when the browser closes).
    header( 'wph-no-cache: true', false ); // Adding WPHercules No Cache header.
}
