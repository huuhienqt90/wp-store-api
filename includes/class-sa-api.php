<?php
/**
 * Class API
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'ProductGroupAPI' ) ) :

    /**
    * 
    */
    class ProductGroupAPI
    {
        
        /**
         * The single instance of the class.
         *
         * @var ProductGroupAPI
         * @since 1.0.0
         */
        protected static $_instance = null;

        protected $PointUrl = 'http://xero-connect.com:5233/';
        public $code = '201'; 
        public $message = 'Done'; 

        /**
         * Main ProductGroupAPI Instance.
         *
         * Ensures only one instance of ProductGroupAPI is loaded or can be loaded.
         *
         * @since 1.0.0
         * @static
         * @see product_api()
         * @return ProductGroupAPI - Main instance.
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function post($name = '', $data = null, $scope='Database/'){
            ob_start();
            $PointUrl = get_option('wpsa_api_server_url', $this->PointUrl);
            $url = $PointUrl.$scope.$name;
            $jsonDataEncoded = json_encode($data);

            $response = \Httpful\Request::post($url)
                ->sendsJson()
                ->body($jsonDataEncoded)
                ->send();
            return $response->body;
        }

        /**
         * ProductGroupAPI Constructor.
         */
        public function __construct() {
            
        }

    }
endif;

/**
 * Main instance of ProductGroupAPI.
 *
 * @since  1.0.0
 * @return ProductGroupAPI
 */
function product_api() {
    return ProductGroupAPI::instance();
}

// Global for backwards compatibility.
$GLOBALS['product_api'] = product_api();