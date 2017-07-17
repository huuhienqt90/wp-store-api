<?php

/**
 * Class WPSA Template
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WordPressStoreAPITemplate' ) ) :

	/**
	* WordPress Store API Template
	*/
	class WordPressStoreAPITemplate
	{
		
		public function __construct()
		{
			add_filter( 'template_include', array( $this, 'template_hooks' ), 99 );
		}

		public function template_hooks( $template ) {
			global $wp_query;
			if( isset($wp_query->query_vars['group'] ) ){
				$new_template = sa_locate_template( 'product-archive.php' );
				if ( '' != $new_template ) {
					return $new_template ;
				}
			}

			if( isset($wp_query->query_vars['product'] ) ){
				$new_template = sa_locate_template( 'single-product.php' );
				if ( '' != $new_template ) {
					return $new_template ;
				}
			}

			return $template;
		}
	}
endif;
new WordPressStoreAPITemplate();