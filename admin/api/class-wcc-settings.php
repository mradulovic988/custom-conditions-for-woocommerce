<?php
/**
 * Main class for all API Settings communication
 *
 * @class Wcc_Settings
 * @package Wcc_Settings
 * @version 1.0.0
 * @author Marko Radulovic
 */

if ( ! class_exists( 'Wcc_Settings' ) ) {
	class Wcc_Settings {
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'wcc_register_submenu_page' ) );
			add_action( 'admin_init', array( $this, 'wcc_register_settings' ) );
			add_action( 'admin_notices', array( $this, 'wcc_show_error_notice' ) );
		}

		public function wcc_show_error_notice() {
			if ( isset( $_GET['settings-updated'] ) ) {
				$message = __( 'You have successfully saved your settings.', 'wcc' );
				add_settings_error( 'wcc_settings_fields', 'success', $message, 'success' );
			}
		}

		public function wcc_register_submenu_page() {
			add_submenu_page(
				'woocommerce',
				__( 'WC Conditions', 'wcc' ),
				__( 'WC Conditions', 'wcc' ),
				'manage_options',
				'woocommerce-conditions',
				array( $this, 'wcc_register_submenu_page_callback' )
			);
		}

		protected function wcc_settings_fields( string $type, string $id, string $class, string $name, string $value, $placeholder = '', $description = '', $min = '', $max = '', $required = '' ) {
			switch ( $type ) {
				case 'text':
					echo '<input type="text" id="' . $id . '" class="' . $class . '" name="wcc_settings_fields[' . $name . ']" value="' . $value . '" placeholder="' . __( $placeholder, 'wcc' ) . '" ' . $required . '><small class="wcc-field-desc">' . __( $description, 'wcc' ) . '</small>';
					break;
				case 'number':
					echo '<input type="number" id="' . $id . '" class="' . $class . '" name="wcc_settings_fields[' . $name . ']" value="' . $value . '" placeholder="' . __( $placeholder, 'wcc' ) . '" min="' . $min . '" max="' . $max . '"><small class="wcc-field-desc">' . __( $description, 'wcc' ) . '</small>';
					break;
				case 'checkbox':
					echo '<label class="wcc-switch" for="' . $id . '"><input type="checkbox" id="' . $id . '" class="' . $class . '" name="wcc_settings_fields[' . $name . ']" value="1" ' . $value . '><span class="wcc-slider wcc-round"></span></label><small class="wcc-field-desc">' . $description . '</small>';
					break;
				case 'url':
					echo '<input type="url" id="' . $id . '" class="' . $class . '" name="wcc_settings_fields[' . $name . ']" value="' . $value . '"placeholder="' . __( $placeholder, 'wcc' ) . '" ' . $required . '><small class="wcc-field-desc">' . __( $description, 'wcc' ) . '</small>';
					break;
				case 'color':
					echo '<input type="color" id="' . $id . '" class="' . $class . '" name="wcc_settings_fields[' . $name . ']" value="' . $value . '">';
					break;
				case 'textarea':
					echo '<textarea class="' . $class . '" name="wcc_settings_fields[' . $name . ']" placeholder="' . __( $placeholder, 'wcc' ) . '" id="' . $id . '" rows="7" ' . $required . '>' . $value . '</textarea>';
					break;
			}
		}

		public function wcc_options_check( string $id ): string {
			$options = get_option( 'wcc_settings_fields' );

			return ( ! empty( $options[ $id ] ) ? $options[ $id ] : '' );
		}

		public function wcc_option_check_radio_btn( string $id ): string {
			$options = get_option( 'wcc_settings_fields' );

			return isset( $options[ $id ] ) ? checked( 1, $options[ $id ], false ) : '';
		}

		public function wcc_register_submenu_page_callback() {
			?>
            <div class="wrap">
                <form action="options.php" method="post">

					<?php
					settings_errors( 'wcc_settings_fields' );
					wp_nonce_field( 'wcc_dashboard_save', 'wcc_form_save_name' );
					settings_fields( 'wcc_settings_fields' );
					do_settings_sections( 'wcc_settings_section_one' );
					do_settings_sections( 'wcc_settings_section_product_prices' );
					do_settings_sections( 'wcc_settings_section_coupon' );
					do_settings_sections( 'wcc_settings_section_description_tabs' );
					do_settings_sections( 'wcc_settings_section_checkout_fields' );
					do_settings_sections( 'wcc_settings_section_two' );

					?>
                    <div class="wcc-loading-wrapper">
						<?php
						submit_button(
							__( 'Save changes', 'wcc' ),
							'primary',
							'wcc_save_changes_btn',
							true,
							array( 'id' => 'wcc-save-changes-btn' )
						);
						?>
                        <div class="wcc-loader"></div>
                    </div>

                </form>

				<?php
				if ( ! isset( $_POST['wcc_form_save_name'] ) ||
				     ! wp_verify_nonce( $_POST['wcc_form_save_name'], 'wcc_dashboard_save' ) ) {
					return;
				}
				?>
            </div>
			<?php
		}

		public function wcc_register_settings() {

			register_setting( 'wcc_settings_fields', 'wcc_settings_fields', 'wcc_sanitize_callback' );

			// Adding sections
			add_settings_section( 'wcc_section_id', __( 'Add to Cart button', 'wcc' ), array(
				$this,
				'wcc_settings_section_callback'
			), 'wcc_settings_section_one' );

			add_settings_section( 'wcc_section_id', __( 'Product prices', 'wcc' ), array(
				$this,
				'wcc_settings_section_callback_product_prices'
			), 'wcc_settings_section_product_prices' );

			add_settings_section( 'wcc_section_id', __( 'Coupon Code', 'wcc' ), array(
				$this,
				'wcc_settings_section_callback_coupon'
			), 'wcc_settings_section_coupon' );

			add_settings_section( 'wcc_section_id', __( 'Description tabs', 'wcc' ), array(
				$this,
				'wcc_settings_section_callback_description_tabs'
			), 'wcc_settings_section_description_tabs' );

			add_settings_section( 'wcc_section_id', __( 'Checkout fields', 'wcc' ), array(
				$this,
				'wcc_settings_section_callback_checkout_fields'
			), 'wcc_settings_section_checkout_fields' );

			add_settings_section( 'wcc_section_id', __( 'Add to Cart button text', 'wcc' ), array(
				$this,
				'wcc_settings_section_strings_callback'
			), 'wcc_settings_section_two' );

			// Add to cart fields
			add_settings_field( 'wcc_section_id_card_button_archive', __( 'Hide Add to Cart button - Archive (Shop) page', 'wcc' ), array(
				$this,
				'wcc_section_id_card_button_archive'
			), 'wcc_settings_section_one', 'wcc_section_id' );

			add_settings_field( 'wcc_section_id_card_button_single', __( 'Hide Add to Cart button - Single product page', 'wcc' ), array(
				$this,
				'wcc_section_id_card_button_single'
			), 'wcc_settings_section_one', 'wcc_section_id' );

			add_settings_field( 'wcc_section_id_card_button_category', __( 'Hide Add to Cart button - Category product page', 'wcc' ), array(
				$this,
				'wcc_section_id_card_button_category'
			), 'wcc_settings_section_one', 'wcc_section_id' );

			// Product prices fields
			add_settings_field( 'wcc_section_id_prices_all', __( 'Hide All Product prices', 'wcc' ), array(
				$this,
				'wcc_section_id_prices_all'
			), 'wcc_settings_section_product_prices', 'wcc_section_id' );

			add_settings_field( 'wcc_section_id_prices_archive', __( 'Hide Product prices - Archive (Shop) page', 'wcc' ), array(
				$this,
				'wcc_section_id_prices_archive'
			), 'wcc_settings_section_product_prices', 'wcc_section_id' );

			add_settings_field( 'wcc_section_id_prices_category', __( 'Hide Product prices - Category product page', 'wcc' ), array(
				$this,
				'wcc_section_id_prices_category'
			), 'wcc_settings_section_product_prices', 'wcc_section_id' );

			add_settings_field( 'wcc_section_id_prices_google', __( 'Hide Product prices from Google search', 'wcc' ), array(
				$this,
				'wcc_section_id_prices_google'
			), 'wcc_settings_section_product_prices', 'wcc_section_id' );

			// Coupon Code fields
			add_settings_field( 'wcc_section_id_coupon_checkout', __( 'Hide Coupon Code - Checkout page', 'wcc' ), array(
				$this,
				'wcc_section_id_coupon_checkout'
			), 'wcc_settings_section_coupon', 'wcc_section_id' );

			add_settings_field( 'wcc_section_id_coupon_cart', __( 'Hide Coupon Code - Cart page', 'wcc' ), array(
				$this,
				'wcc_section_id_coupon_cart'
			), 'wcc_settings_section_coupon', 'wcc_section_id' );

			// Description tabs
			add_settings_field( 'wcc_section_id_description_tab', __( 'Hide Description Tab', 'wcc' ), array(
				$this,
				'wcc_section_id_description_tab'
			), 'wcc_settings_section_description_tabs', 'wcc_section_id' );

			add_settings_field( 'wcc_section_id_review_tab', __( 'Hide Reviews Tab', 'wcc' ), array(
				$this,
				'wcc_section_id_review_tab'
			), 'wcc_settings_section_description_tabs', 'wcc_section_id' );

			add_settings_field( 'wcc_section_id_additional_info_tab', __( 'Hide Additional Information Tab', 'wcc' ), array(
				$this,
				'wcc_section_id_additional_info_tab'
			), 'wcc_settings_section_description_tabs', 'wcc_section_id' );

			add_settings_field( 'wcc_section_id_checkout_fields', __( 'Hide Specific Checkout field', 'wcc' ), array(
				$this,
				'wcc_section_id_checkout_fields'
			), 'wcc_settings_section_checkout_fields', 'wcc_section_id' );

			add_settings_field( 'wcc_section_id_string_add_to_cart_archive', __( 'Change name for Add to Cart - Archive page', 'wcc' ), array(
				$this,
				'wcc_section_id_string_add_to_cart_archive'
			), 'wcc_settings_section_two', 'wcc_section_id' );

			add_settings_field( 'wcc_section_id_string_add_to_cart_single', __( 'Change name for Add to Cart - Single page', 'wcc' ), array(
				$this,
				'wcc_section_id_string_add_to_cart_single'
			), 'wcc_settings_section_two', 'wcc_section_id' );

		}

		public function wcc_settings_section_callback() {
			_e( 'Manage visibility for Add to Cart button.', 'wcc' );
			echo '<hr>';
		}

		public function wcc_settings_section_callback_product_prices() {
			_e( 'Manage visibility for Product prices.', 'wcc' );
			echo '<hr>';
		}

		public function wcc_settings_section_callback_coupon() {
			_e( 'Manage visibility for Coupon code.', 'wcc' );
			echo '<hr>';
		}

		public function wcc_settings_section_callback_description_tabs() {
			_e( 'Manage visibility for Description tabs.', 'wcc' );
			echo '<hr>';
		}

		public function wcc_settings_section_callback_checkout_fields() {
			_e( 'Manage visibility for Checkout fields.', 'wcc' );
			echo '<br>';
			_e( 'Use checkout fields slugs from the table below.', 'wcc' );
			?>
            <pre class="wcc-pre-code-table">
First Name  - billing_first_name
Last Name   - billing_last_name
Company     - billing_company
Address 1   - billing_address_1
Address 2   - billing_address_2
City        - billing_city
Postcode    - billing_postcode
Country     - billing_country
State       - billing_state
Phone       - billing_phone
Email       - billing_email
            </pre>
			<?php
			echo '<hr>';
		}

		public function wcc_settings_section_strings_callback() {
			// CHANGE DESCRIPTION LATER
			_e( 'Change text for Add to Cart button.', 'wcc' );
			echo '<hr>';
		}

		public function wcc_section_id_card_button_archive() {
			$this->wcc_settings_fields( 'checkbox', 'wcc-card-button-archive', 'wcc-switch-input', 'card_button_archive', $this->wcc_option_check_radio_btn( 'card_button_archive' ) );
		}

		public function wcc_section_id_card_button_single() {
			$this->wcc_settings_fields( 'checkbox', 'wcc-card-button-single', 'wcc-switch-input', 'card_button_single', $this->wcc_option_check_radio_btn( 'card_button_single' ) );
		}

		public function wcc_section_id_card_button_category() {
			$this->wcc_settings_fields( 'checkbox', 'wcc-card-button-category', 'wcc-switch-input', 'card_button_category', $this->wcc_option_check_radio_btn( 'card_button_category' ) );
		}

		public function wcc_section_id_prices_all() {
			$this->wcc_settings_fields( 'checkbox', 'wcc-prices-all', 'wcc-switch-input', 'prices_all', $this->wcc_option_check_radio_btn( 'prices_all' ) );
		}

		public function wcc_section_id_prices_archive() {
			$this->wcc_settings_fields( 'checkbox', 'wcc-prices-archive', 'wcc-switch-input', 'prices_archive', $this->wcc_option_check_radio_btn( 'prices_archive' ) );
		}

		public function wcc_section_id_prices_category() {
			$this->wcc_settings_fields( 'checkbox', 'wcc-prices-category', 'wcc-switch-input', 'prices_category', $this->wcc_option_check_radio_btn( 'prices_category' ) );
		}

		public function wcc_section_id_prices_google() {
			$this->wcc_settings_fields( 'checkbox', 'wcc-prices-google', 'wcc-switch-input', 'prices_google', $this->wcc_option_check_radio_btn( 'prices_google' ), '', __( 'This change won\'t affect your website immediately. It will affect it when Google robots re-crawl your website again. That can take up to 40 days.', 'wcc' ) );
		}

		public function wcc_section_id_coupon_checkout() {
			$this->wcc_settings_fields( 'checkbox', 'wcc-coupon-checkout', 'wcc-switch-input', 'coupon_checkout', $this->wcc_option_check_radio_btn( 'coupon_checkout' ) );
		}

		public function wcc_section_id_coupon_cart() {
			$this->wcc_settings_fields( 'checkbox', 'wcc-coupon-cart', 'wcc-switch-input', 'coupon_cart', $this->wcc_option_check_radio_btn( 'coupon_cart' ) );
		}

		public function wcc_section_id_description_tab() {
			$this->wcc_settings_fields( 'checkbox', 'wcc-description-tab', 'wcc-switch-input', 'description_tab', $this->wcc_option_check_radio_btn( 'description_tab' ) );
		}

		public function wcc_section_id_review_tab() {
			$this->wcc_settings_fields( 'checkbox', 'wcc-review-tab', 'wcc-switch-input', 'review_tab', $this->wcc_option_check_radio_btn( 'review_tab' ) );
		}

		public function wcc_section_id_additional_info_tab() {
			$this->wcc_settings_fields( 'checkbox', 'wcc-additional-info-tab', 'wcc-switch-input', 'additional_info_tab', $this->wcc_option_check_radio_btn( 'additional_info_tab' ) );
		}

		public function wcc_section_id_checkout_fields() {
			$this->wcc_settings_fields( 'text', 'wcc-checkout-fields', 'wcc-settings-field', 'checkout_fields', esc_attr__( sanitize_text_field( $this->wcc_options_check( 'checkout_fields' ) ) ), 'billing_first_name, billing_city, billing_phone', __( 'Add checkout field slug comma separated.', 'wcc' ) );
		}

		public function wcc_section_id_string_add_to_cart_archive() {
			$this->wcc_settings_fields( 'text', 'wcc-string-add-to-cart-archive', 'wcc-settings-field', 'string_add_to_cart_archive', esc_attr__( sanitize_text_field( $this->wcc_options_check( 'string_add_to_cart_archive' ) ) ), 'Add to Cart' );
		}

		public function wcc_section_id_string_add_to_cart_single() {
			$this->wcc_settings_fields( 'text', 'wcc-string-add-to-cart-single', 'wcc-settings-field', 'string_add_to_cart_single', esc_attr__( sanitize_text_field( $this->wcc_options_check( 'string_add_to_cart_single' ) ) ), 'Add to Cart' );
		}

	}

	new Wcc_Settings();
}