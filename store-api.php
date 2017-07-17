<?php
/**
 * Plugin Name: WP Store API
 * Plugin URI: http://allbestforyou.net/wordpress/plugins/wp-store-api
 * Description: A plugin to management product
 * Version: 1.0.0
 * Author: Hien(Hamilton) H.HO
 * Author URI: https://huuhienqt.info
 *
 * Text Domain: wp-store-api
 * Domain Path: /languages/
 *
 * @package StoreApi
 * @category Core
 * @author Hien(Hamilton) H.HO
 */
include "vendor/autoload.php";
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'StoreApi' ) ) :
    /**
     * Main StoreApi Class.
     *
     * @class StoreApi
     * @version 1.0.0
     */
    final class StoreApi {

        /**
         * The single instance of the class.
         *
         * @var StoreApi
         * @since 1.0.0
         */
        protected static $_instance = null;

        /**
         * Main StoreApi Instance.
         *
         * Ensures only one instance of StoreApi is loaded or can be loaded.
         *
         * @since 1.0.0
         * @static
         * @see wpsa()
         * @return wpsa - Main instance.
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * StoreApi Constructor.
         */
        public function __construct() {
            if( !session_id() ){
                session_start();
            }
            $this->define_constants();
            $this->includes();
            $this->init_hooks();

            do_action( 'woocommerce_product_group_loaded' );
        }

        /**
         * Hook into actions and filters.
         * @since  1.0.0
         */
        private function init_hooks() {
            register_activation_hook( __FILE__, array( 'WCPG_Install', 'install' ) );
            add_action('wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ));
        }

        public function enqueue_scripts(){
            wp_enqueue_script( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), '3.3.7' );
            wp_enqueue_script( 'wpsa_custom', plugin_dir_url( __FILE__ ) . 'assets/js/custom.js', array('jquery'), '1.0.0' );
            // Localize the script with new data
            $translation_array = array(
                'ajax_url' => admin_url( 'admin-ajax.php' )
            );
            wp_localize_script( 'wpsa_custom', 'ajax', $translation_array );
        }

        /**
         * Define wpsa Constants.
         */
        private function define_constants() {
            $upload_dir = wp_upload_dir();

            $this->define( 'WCPG_PLUGIN_FILE', __FILE__ );
            $this->define( 'WCPG_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
            $this->define( '__TEXTDOMAIN__', 'wp-store-api' );
        }

        /**
         * Define constant if not already set.
         *
         * @param  string $name
         * @param  string|bool $value
         */
        private function define( $name, $value ) {
            if ( ! defined( $name ) ) {
                define( $name, $value );
            }
        }

        /**
         * What type of request is this?
         *
         * @param  string $type admin, ajax, cron or frontend.
         * @return bool
         */
        private function is_request( $type ) {
            switch ( $type ) {
                case 'admin' :
                    return is_admin();
                case 'ajax' :
                    return defined( 'DOING_AJAX' );
                case 'cron' :
                    return defined( 'DOING_CRON' );
                case 'frontend' :
                    return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
            }
        }

        /**
         * Include required core files used in admin and on the frontend.
         */
        public function includes() {
            // Include required files
            include_once( 'includes/sa-functions.php' );
            include_once( 'includes/class-sa-api.php' );
            include_once( 'includes/sa-shortcodes.php' );
            include_once( 'includes/admin/class-sa-admin-menus.php' );
            include_once( 'includes/class-sa-rewrite-rules.php' );
            include_once( 'includes/class-sa-template.php' );
            include_once( 'includes/class-sa-cart.php' );
            include_once( 'includes/class-sa-post-type.php' );

            // Include required admin files.
            if ( $this->is_request( 'admin' ) ) {

            }

            // Include required frontend files.
            if ( $this->is_request( 'frontend' ) ) {
                $this->frontend_includes();
            }
        }

        /**
         * Include required admin files.
         */
        public function admin_includes(){

        }

        /**
         * Include required frontend files.
         */
        public function frontend_includes() {
            
        }

        /**
         * Get the plugin url.
         * @return string
         */
        public function plugin_url() {
            return untrailingslashit( plugins_url( '/', __FILE__ ) );
        }

        /**
         * Get the plugin path.
         * @return string
         */
        public function plugin_path() {
            return untrailingslashit( plugin_dir_path( __FILE__ ) );
        }

        /**
         * Get the template path.
         * @return string
         */
        public function template_path() {
            return apply_filters( 'store_api_template_path', 'store-api/' );
        }
    }
endif;

/**
 * Main instance of StoreApi.
 *
 * Returns the main instance of wpsa to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return StoreApi
 */
function wpsa() {
    return StoreApi::instance();
}

// Global for backwards compatibility.
$GLOBALS['store_api'] = wpsa();