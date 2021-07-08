<?php
/**
 * Main class for all conditions
 *
 * @class Wcc_Conditions
 * @package Wcc_Conditions
 * @version 1.0.0
 * @author Marko Radulovic
 */

include WCC_PLUGIN_PATH . '/admin/api/class-wcc-settings.php';
if ( ! class_exists( 'Wcc_Conditions' ) ) {

	class Wcc_Conditions {
		public Wcc_Settings $api;

		public function __construct() {
			$this->api = new Wcc_Settings();

			add_action( 'template_redirect', array( $this, 'wcc_remove_add_to_cart' ) );
			add_action( 'init', array( $this, 'wcc_remove_add_to_cart_single' ) );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'wcc_remove_add_to_cart_category' ), 1 );
			add_filter( 'woocommerce_after_shop_loop_item_title', array( $this, 'wcc_remove_product_prices' ), 2 );
			add_filter( 'woocommerce_after_shop_loop_item_title', array(
				$this,
				'wcc_remove_product_prices_category'
			), 2 );

			if ( $this->api->wcc_options_check( 'prices_all' ) == 1 ) {
				add_filter( 'woocommerce_variable_sale_price_html', array(
					$this,
					'wcc_remove_product_prices_all'
				), 9999, 2 );
				add_filter( 'woocommerce_variable_price_html', array(
					$this,
					'wcc_remove_product_prices_all'
				), 9999, 2 );
				add_filter( 'woocommerce_get_price_html', array( $this, 'wcc_remove_product_prices_all' ), 9999, 2 );
			}

			if ( $this->api->wcc_options_check( 'coupon_checkout' ) == 1 ) {
				add_filter( 'woocommerce_coupons_enabled', array( $this, 'wcc_remove_coupon_code_checkout' ) );
			}

			if ( $this->api->wcc_options_check( 'coupon_cart' ) == 1 ) {
				add_filter( 'woocommerce_coupons_enabled', array( $this, 'wcc_remove_coupon_code_cart' ) );
			}

			if ( $this->api->wcc_options_check( 'description_tab' ) == 1 ) {
				add_filter( 'woocommerce_product_tabs', array( $this, 'wcc_remove_description_tab' ), 98 );
			}

			if ( $this->api->wcc_options_check( 'review_tab' ) == 1 ) {
				add_filter( 'woocommerce_product_tabs', array( $this, 'wcc_remove_review_tab' ), 98 );
			}

			if ( $this->api->wcc_options_check( 'additional_info_tab' ) == 1 ) {
				add_filter( 'woocommerce_product_tabs', array( $this, 'wcc_remove_additional_info_tab' ), 98 );
			}

			if ( $this->api->wcc_options_check( 'prices_google' ) == 1 ) {
				add_filter( 'woocommerce_structured_data_product_offer', '__return_empty_array' );
			}

			add_filter( 'woocommerce_checkout_fields', array( $this, 'wcc_remove_checkout_fields' ) );
			add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'wcc_change_name_add_to_cart_archive' ) );
			add_filter( 'woocommerce_product_single_add_to_cart_text', array(
				$this,
				'wcc_change_name_add_to_cart_single'
			) );
		}

		/**
		 * Remove Add to cart button on shop page
		 */
		public function wcc_remove_add_to_cart() {
			if ( $this->api->wcc_options_check( 'card_button_archive' ) == 1 && is_shop() ) {
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

		/**
		 * Remove product prices on shop page
		 */
		public function wcc_remove_product_prices() {
			if ( ! is_shop() ) {
				return;
			}
			if ( $this->api->wcc_options_check( 'prices_archive' ) == 1 ) {
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			}
		}

		/**
		 * Remove product prices on category page
		 */
		public function wcc_remove_product_prices_category() {
			if ( ! is_product_category() ) {
				return;
			}
			if ( $this->api->wcc_options_check( 'prices_category' ) == 1 ) {
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			}
		}

		/**
		 * Remove prices from all pages
		 *
		 * @param $price
		 * @param $product
		 */
		public function wcc_remove_product_prices_all( $price, $product ) {
			$price = '';
		}

		/**
		 * Remove coupon code from Checkout page
		 *
		 * @param $enabled
		 *
		 * @return false|mixed
		 */
		public function wcc_remove_coupon_code_checkout( $enabled ) {
			if ( is_checkout() ) {
				$enabled = false;
			}

			return $enabled;
		}

		/**
		 * Remove coupon code from Cart page
		 *
		 * @param $enabled
		 *
		 * @return false|mixed
		 */
		public function wcc_remove_coupon_code_cart( $enabled ) {
			if ( is_cart() ) {
				$enabled = false;
			}

			return $enabled;
		}

		/**
		 * Remove description tab
		 *
		 * @param $tabs
		 *
		 * @return mixed
		 */
		public function wcc_remove_description_tab( $tabs ) {
			unset( $tabs['description'] );

			return $tabs;
		}

		/**
		 * Remove review tab
		 *
		 * @param $tabs
		 *
		 * @return mixed
		 */
		public function wcc_remove_review_tab( $tabs ) {
			unset( $tabs['reviews'] );

			return $tabs;
		}

		/**
		 * Remove additional information tab
		 *
		 * @param $tabs
		 *
		 * @return mixed
		 */
		public function wcc_remove_additional_info_tab( $tabs ) {
			unset( $tabs['additional_information'] );

			return $tabs;
		}

		/**
		 * Remove specific checkout fields
		 *
		 * @param $fields
		 *
		 * @return mixed
		 */
		public function wcc_remove_checkout_fields( $fields ) {
			$get_fields = explode( ', ', $this->api->wcc_options_check( 'checkout_fields' ) );
			foreach ( $get_fields as $get_field ) {
				unset( $fields['billing'][ $get_field ] );
			}

			return $fields;
		}

		/**
		 * Changing text for Add to Cart button on Archive page
		 *
		 * @return string|void
		 */
		public function wcc_change_name_add_to_cart_archive() {
			if ( ! empty( $this->api->wcc_options_check( 'string_add_to_cart_archive' ) ) ) {
				return __( $this->api->wcc_options_check( 'string_add_to_cart_archive' ), 'wcc' );
			} else {
				return __( 'Add to cart', 'wcc' );
			}
		}

		/**
		 * Changing text for Add to Cart button on Single product page
		 *
		 * @return string|void
		 */
		public function wcc_change_name_add_to_cart_single() {
			if ( ! empty( $this->api->wcc_options_check( 'string_add_to_cart_single' ) ) ) {
				return __( $this->api->wcc_options_check( 'string_add_to_cart_single' ), 'wcc' );
			} else {
				return __( 'Add to cart', 'wcc' );
			}
		}
	}

	new Wcc_Conditions();
}