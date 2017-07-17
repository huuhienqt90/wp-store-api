<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' ); ?>

	<?php
		/**
		 * store_api_before_main_content hook.
		 *
		 * @hooked store_api_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked store_api_breadcrumb - 20
		 */
		do_action( 'store_api_before_main_content' );
	?>

	<?php sa_get_template_part( 'content', 'single-product' ); ?>

	<?php
		/**
		 * store_api_after_main_content hook.
		 *
		 * @hooked store_api_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'store_api_after_main_content' );
	?>

	<?php
		/**
		 * store_api_sidebar hook.
		 *
		 * @hooked store_api_get_sidebar - 10
		 */
		do_action( 'store_api_sidebar' );
	?>

<?php get_footer( 'shop' ); ?>
