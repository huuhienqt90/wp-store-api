<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'StoreApiCart' ) ) :
    class StoreApiCart{
        /**
         * The single instance of the class.
         *
         * @var StoreApiCart
         * @since 1.0.0
         */
        protected static $_instance = null;
        public static $subtotal = 0;
        public static $total = 0;
        public static $shipping = 0;
        public static $tax = 0;

        /**
         * Main StoreApiCart Instance.
         *
         * Ensures only one instance of ProductGroupAPI is loaded or can be loaded.
         *
         * @since 1.0.0
         * @static
         * @see product_api()
         * @return StoreApiCart - Main instance.
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * StoreApiCart Constructor.
         */
        public function __construct() {
            add_action( 'init', array( __CLASS__, 'init' ) );
            add_shortcode('sa_cart', array( __CLASS__, 'short_code_cart') );
            add_shortcode('sa_checkout', array( __CLASS__, 'short_code_checkout') );
        }

        /**
         * Init function
         */
        public function init(){
            if( isset( $_POST['add_to_cart']) ){
                $data = [];
                if( isset($_POST['data']) ){
                    foreach ($_POST['data'] as $key=>$val){
                        $data[] = ['label' => $key, 'value' => $val];
                    }
                }
                self::add_to_cart($_POST['product_id'], $_POST['quantity'], $data);
                add_action('store_api_message', function(){
                    ?>
                    <div class="container">
                        <div class="alert alert-success" role="alert">Add product to cart successfully! <strong><a href="<?php echo home_url('/cart'); ?>">View cart</a></strong></div>
                    </div>
                    <?php
                });
            }

            // Remove product from cart
            if( isset( $_REQUEST['remove_cart'] ) && isset( $_SESSION['cart']['items'][$_REQUEST['remove_cart']] ) ){
                unset($_SESSION['cart']['items'][$_REQUEST['remove_cart']]);
                wp_redirect(home_url('/cart'));
                exit();
            }

            // Update cart items
            if( isset( $_REQUEST['update_cart']) && isset( $_SESSION['cart']['items'] ) ){
                foreach( $_POST['quantity'] as $prid => $value ){
                    if( $value <= 0 ){
                        self::remove_product_in_cart($prid);
                    }else{
                        self::update_product_cart($prid, $value);
                    }
                }
            }

            if( isset( $_POST['checkout']) ){
                wp_redirect(home_url('/checkout'));
                exit();
            }

            if( isset( $_POST['process_checkout']) ){
                self::sa_empty_cart();
                wp_redirect(home_url('/thank-you'));
                exit();
            }
        }

        /**
         * Add product to cart
         *
         * @param int $product_id
         * @param int $quantity
         */
        public function add_to_cart($product_id = 0, $quantity = 1, $data = [] ){
            global $product_api, $store_api;
            $sellPriceLevel = get_option('wpsa_sell_price_level', 0);
            $sellPriceIndex = get_option('wpsa_sell_price_index', 0);
            $productDetail = $product_api->post('GetProductDetail/JSON', ['ProductId'=>$product_id, 'SellpriceIndex'=>$sellPriceIndex, 'SellpriceLevel'=>$sellPriceLevel]);
            $title = $description = '';
            $price = 0;
            $image = $store_api->plugin_url().'/assets/images/no-image.png';
            if( isset($productDetail->Content) && count($productDetail->Content) ){
                $price = !empty($productDetail->Content->SoldBys[0]->SellPrice) && !empty($productDetail->Content->SoldBys[0]->SellPrice) ? $productDetail->Content->SoldBys[0]->SellPrice->Price : 0;
                $title = $productDetail->Content->Name;
                $image = isset($productDetail->Content->SoldBys[0]->AdvancedSetup->ProductImages) && !empty($productDetail->Content->SoldBys[0]->AdvancedSetup->ProductImages) ? 'http://dev.cloudsales.xero-connect.com/UserData/'.$productDetail->Content->SoldBys[0]->AdvancedSetup->ProductImages : $store_api->plugin_url().'/assets/images/no-image.png';
            }
            if( isset( $_SESSION['cart']['items'][$product_id] ) ){
                $_SESSION['cart']['items'][$product_id]['quantity'] += $quantity;
            }else{
                $_SESSION['cart']['items'][$product_id] = [
                    'quantity' => $quantity,
                    'price' => $price,
                    'name' => $title,
                    'image' => $image
                ];
            }
            if( count( $data ) > 0 ){
                $_SESSION['cart']['items'][$product_id]['data'] = $data;
            }
            self::update_cart_data();
        }

        /**
         * Update product cart
         *
         * @param int $product_id
         * @param int $quantity
         */
        public function update_product_cart($product_id = 0, $quantity = 1){
            if( $quantity <= 0 && isset( $_SESSION['cart']['items'][$product_id] ) ){
                unset($_SESSION['cart']['items'][$product_id]);
            }elseif( isset( $_SESSION['cart']['items'][$product_id] ) ){
                $_SESSION['cart']['items'][$product_id]['quantity'] = $quantity;
            }
            self::update_cart_data();
        }

        /**
         * Remove product in cart
         *
         * @param int $product_id
         */
        public function remove_product_in_cart($product_id = 0){
            if( isset($_SESSION['cart']['items'][$product_id]) ){
                unset($_SESSION['cart']['items'][$product_id]);
                self::update_cart_data();
            }
        }
        /**
         * Update cart data
         */
        public function update_cart_data(){
            if( count($_SESSION['cart']['items']) ){
                foreach($_SESSION['cart']['items'] as $prid => $data){
                    self::$subtotal += $data['price'] * $data['quantity'];
                }
                $_SESSION['cart']['subtotal'] = self::$subtotal;
                $_SESSION['cart']['total'] = self::$subtotal + self::$shipping + self::$tax;
            }
        }

        /**
         * Empty cart
         */
        public function sa_empty_cart(){
            if( isset( $_SESSION['cart'] ) ){
                unset($_SESSION['cart']);
            }
        }

        /**
         * Short code show cart page
         *
         * @param $attr
         * @return string
         */
        public function short_code_cart($attr){
            $new_template = sa_get_template_html( 'cart.php' );
            if ( '' != $new_template ) {
                return $new_template ;
            }
            return $new_template;
        }

        /**
         * Short code show checkout page
         *
         * @param $attr
         * @return string
         */
        public function short_code_checkout($attr){
            $new_template = sa_get_template_html( 'checkout.php' );
            if ( '' != $new_template ) {
                return $new_template ;
            }
            return $new_template;
        }
    }
endif;
/**
 * Main instance of StoreApiCart.
 *
 * @since  1.0.0
 * @return StoreApiCart
 */
function sa_cart() {
    return StoreApiCart::instance();
}

// Global for backwards compatibility.
$GLOBALS['sa_cart'] = sa_cart();