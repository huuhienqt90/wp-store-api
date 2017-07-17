<?php
/**
 * Class Rewrite Rules
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'SARewriteRules') ) :
	/**
	* SARewriteRules Class
	*/
	class SARewriteRules
	{
		/**
		 * Construct class
		 */
		public function __construct()
		{
			add_action('init', array( $this, 'rules' ) );
			add_action('init', array( $this, 'tags' ), 10, 0);
		}

		/**
		 * Register all rewrite rules
		 */
		public function rules(){
			add_rewrite_rule('^group/([^/]*)/?', 'index.php?group=$matches[1]', 'top');
			add_rewrite_rule('^product/([^/]*)/?', 'index.php?product=$matches[1]', 'top');
			add_rewrite_rule('^location/([^/]*)/?', 'index.php?location=$matches[1]', 'top');
			add_rewrite_rule('^tender-type/([^/]*)/?', 'index.php?tender_type=$matches[1]', 'top');
			add_rewrite_rule('^product-type/([^/]*)/?', 'index.php?product_type=$matches[1]', 'top');
			add_rewrite_rule('^discount-key-type/([^/]*)/?', 'index.php?discount_key_type=$matches[1]', 'top');
			add_rewrite_rule('^currency-type/([^/]*)/?', 'index.php?currency_type=$matches[1]', 'top');
			add_rewrite_rule('^card-issuer-type/([^/]*)/?', 'index.php?card_issuer_type=$matches[1]', 'top');
			add_rewrite_rule('^cooking-style/([^/]*)/?', 'index.php?cooking_style=$matches[1]', 'top');
			flush_rewrite_rules();
		}

		/**
		 * Rewrite Tags
		 */
		public function tags() {
		  add_rewrite_tag('%group%', '([^&]+)');
		  add_rewrite_tag('%product%', '([^&]+)');
		  add_rewrite_tag('%location%', '([^&]+)');
		  add_rewrite_tag('%tender_type%', '([^&]+)');
		  add_rewrite_tag('%product_type%', '([^&]+)');
		  add_rewrite_tag('%order_type%', '([^&]+)');
		  add_rewrite_tag('%discount_key_type%', '([^&]+)');
		  add_rewrite_tag('%currency_type%', '([^&]+)');
		  add_rewrite_tag('%card_issuer_type%', '([^&]+)');
		  add_rewrite_tag('%cooking_style%', '([^&]+)');
		}
		
	}
endif;
new SARewriteRules();