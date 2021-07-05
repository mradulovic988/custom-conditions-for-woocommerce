<?php

include WCC_PLUGIN_PATH . '/admin/api/class-wcc-settings.php';
if ( ! class_exists( 'Wcc_Conditions' ) ) {

	class Wcc_Conditions {
		protected $api;

		public function __construct() {
			$this->api = new Wcc_Settings();

			add_action( 'init', array( $this, 'wcc_remove_add_to_cart' ) );
			add_action( 'init', array( $this, 'wcc_remove_add_to_cart_single' ) );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'wcc_remove_add_to_cart_category' ), 1 );
		}

		/**
		 * Remove Add to cart button on shop page
		 */
		public function wcc_remove_add_to_cart() {
			if ( $this->api->wcc_options_check( 'card_button_archive' ) == 1 ) {
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
			}
		}

		/**
		 * Remove Add to cart button on Single product page
		 */
		public function wcc_remove_add_to_cart_single() {
			if ( $this->api->wcc_options_check( 'card_button_single' ) == 1 ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			}
		}

		/**
		 * Remove Add to cart button on Category product page
		 */
		public function wcc_remove_add_to_cart_category() {
			if ( $this->api->wcc_options_check( 'card_button_category' ) == 1 && is_product_category() ) {
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
			}
		}
	}

	new Wcc_Conditions();
}