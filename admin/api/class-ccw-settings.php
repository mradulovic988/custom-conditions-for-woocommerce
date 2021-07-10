<?php
/**
 * Main class for all API Settings communication
 *
 * @class Ccw_Settings
 * @package Ccw_Settings
 * @version 1.0.0
 * @author Marko Radulovic
 */

if ( ! class_exists( 'Ccw_Settings' ) ) {
	class Ccw_Settings {
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'ccw_register_submenu_page' ) );
			add_action( 'admin_init', array( $this, 'ccw_register_settings' ) );
			add_action( 'admin_notices', array( $this, 'ccw_show_error_notice' ) );
		}

		public function ccw_show_error_notice() {
			if ( isset( $_GET['settings-updated'] ) ) {
				$message = __( 'You have successfully saved your settings.', 'custom-conditions-for-woocommerce' );
				add_settings_error( 'ccw_settings_fields', 'success', $message, 'success' );
			}
		}

		public function ccw_register_submenu_page() {
			add_submenu_page(
				'woocommerce',
				__( 'Custom Conditions', 'custom-conditions-for-woocommerce' ),
				__( 'Custom Conditions', 'custom-conditions-for-woocommerce' ),
				'manage_options',
				'custom-conditions-for-woocommerce',
				array( $this, 'ccw_register_submenu_page_callback' )
			);
		}

		protected function ccw_settings_fields( string $type, string $id, string $class, string $name, string $value, $placeholder = '', $description = '', $min = '', $max = '', $required = '' ) {
			switch ( $type ) {
				case 'text':
					echo '<input type="text" id="' . $id . '" class="' . $class . '" name="ccw_settings_fields[' . $name . ']" value="' . $value . '" placeholder="' . esc_attr( $placeholder ) . '" ' . $required . '><small class="ccw-field-desc">' . esc_attr( $description ) . '</small>';
					break;
				case 'number':
					echo '<input type="number" id="' . $id . '" class="' . $class . '" name="ccw_settings_fields[' . $name . ']" value="' . $value . '" placeholder="' . esc_attr( $placeholder ) . '" min="' . $min . '" max="' . $max . '"><small class="ccw-field-desc">' . esc_attr( $description ) . '</small>';
					break;
				case 'checkbox':
					echo '<label class="ccw-switch" for="' . $id . '"><input type="checkbox" id="' . $id . '" class="' . $class . '" name="ccw_settings_fields[' . $name . ']" value="1" ' . $value . '><span class="ccw-slider ccw-round"></span></label><small class="ccw-field-desc">' . esc_attr( $description ) . '</small>';
					break;
				case 'url':
					echo '<input type="url" id="' . $id . '" class="' . $class . '" name="ccw_settings_fields[' . $name . ']" value="' . $value . '"placeholder="' . esc_url( $placeholder ) . '" ' . $required . '><small class="ccw-field-desc">' . esc_url( $description ) . '</small>';
					break;
				case 'color':
					echo '<input type="color" id="' . $id . '" class="' . $class . '" name="ccw_settings_fields[' . $name . ']" value="' . $value . '">';
					break;
				case 'textarea':
					echo '<textarea class="' . $class . '" name="ccw_settings_fields[' . $name . ']" placeholder="' . esc_textarea( $placeholder ) . '" id="' . $id . '" rows="7" ' . $required . '>' . esc_textarea( $value ) . '</textarea>';
					break;
			}
		}

		public function ccw_options_check( string $id ): string {
			$options = get_option( 'ccw_settings_fields' );

			return ( ! empty( $options[ $id ] ) ? $options[ $id ] : '' );
		}

		public function ccw_option_check_radio_btn( string $id ): string {
			$options = get_option( 'ccw_settings_fields' );

			return isset( $options[ $id ] ) ? checked( 1, $options[ $id ], false ) : '';
		}

		public function ccw_register_submenu_page_callback() {
			?>
            <div class="wrap">
                <form action="options.php" method="post">

					<?php
					settings_errors( 'ccw_settings_fields' );
					wp_nonce_field( 'ccw_dashboard_save', 'ccw_form_save_name' );
					settings_fields( 'ccw_settings_fields' );
					do_settings_sections( 'ccw_settings_section_one' );
					do_settings_sections( 'ccw_settings_section_product_prices' );
					do_settings_sections( 'ccw_settings_section_coupon' );
					do_settings_sections( 'ccw_settings_section_description_tabs' );
					do_settings_sections( 'ccw_settings_section_checkout_fields' );
					do_settings_sections( 'ccw_settings_section_two' );

					?>
                    <div class="ccw-loading-wrapper">
						<?php
						submit_button(
							__( 'Save changes', 'custom-conditions-for-woocommerce' ),
							'primary',
							'ccw_save_changes_btn',
							true,
							array( 'id' => 'ccw-save-changes-btn' )
						);
						?>
                        <div class="ccw-loader"></div>
                    </div>

                </form>

				<?php
				if ( ! isset( $_POST['ccw_form_save_name'] ) ||
				     ! wp_verify_nonce( $_POST['ccw_form_save_name'], 'ccw_dashboard_save' ) ) {
					return;
				}
				?>
            </div>
			<?php
		}

		public function ccw_register_settings() {

			register_setting( 'ccw_settings_fields', 'ccw_settings_fields', 'ccw_sanitize_callback' );

			// Adding sections
			add_settings_section( 'ccw_section_id', __( 'Add to Cart button', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_settings_section_callback'
			), 'ccw_settings_section_one' );

			add_settings_section( 'ccw_section_id', __( 'Product prices', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_settings_section_callback_product_prices'
			), 'ccw_settings_section_product_prices' );

			add_settings_section( 'ccw_section_id', __( 'Coupon Code', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_settings_section_callback_coupon'
			), 'ccw_settings_section_coupon' );

			add_settings_section( 'ccw_section_id', __( 'Description tabs', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_settings_section_callback_description_tabs'
			), 'ccw_settings_section_description_tabs' );

			add_settings_section( 'ccw_section_id', __( 'Checkout fields', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_settings_section_callback_checkout_fields'
			), 'ccw_settings_section_checkout_fields' );

			add_settings_section( 'ccw_section_id', __( 'Add to Cart button text', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_settings_section_strings_callback'
			), 'ccw_settings_section_two' );

			// Add to cart fields
			add_settings_field( 'ccw_section_id_card_button_archive', __( 'Hide Add to Cart button - Archive (Shop) page', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_card_button_archive'
			), 'ccw_settings_section_one', 'ccw_section_id' );

			add_settings_field( 'ccw_section_id_card_button_single', __( 'Hide Add to Cart button - Single product page', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_card_button_single'
			), 'ccw_settings_section_one', 'ccw_section_id' );

			add_settings_field( 'ccw_section_id_card_button_category', __( 'Hide Add to Cart button - Category product page', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_card_button_category'
			), 'ccw_settings_section_one', 'ccw_section_id' );

			// Product prices fields
			add_settings_field( 'ccw_section_id_prices_all', __( 'Hide All Product prices', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_prices_all'
			), 'ccw_settings_section_product_prices', 'ccw_section_id' );

			add_settings_field( 'ccw_section_id_prices_archive', __( 'Hide Product prices - Archive (Shop) page', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_prices_archive'
			), 'ccw_settings_section_product_prices', 'ccw_section_id' );

			add_settings_field( 'ccw_section_id_prices_category', __( 'Hide Product prices - Category product page', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_prices_category'
			), 'ccw_settings_section_product_prices', 'ccw_section_id' );

			add_settings_field( 'ccw_section_id_prices_google', __( 'Hide Product prices from Google search', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_prices_google'
			), 'ccw_settings_section_product_prices', 'ccw_section_id' );

			// Coupon Code fields
			add_settings_field( 'ccw_section_id_coupon_checkout', __( 'Hide Coupon Code - Checkout page', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_coupon_checkout'
			), 'ccw_settings_section_coupon', 'ccw_section_id' );

			add_settings_field( 'ccw_section_id_coupon_cart', __( 'Hide Coupon Code - Cart page', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_coupon_cart'
			), 'ccw_settings_section_coupon', 'ccw_section_id' );

			// Description tabs
			add_settings_field( 'ccw_section_id_description_tab', __( 'Hide Description Tab', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_description_tab'
			), 'ccw_settings_section_description_tabs', 'ccw_section_id' );

			add_settings_field( 'ccw_section_id_review_tab', __( 'Hide Reviews Tab', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_review_tab'
			), 'ccw_settings_section_description_tabs', 'ccw_section_id' );

			add_settings_field( 'ccw_section_id_additional_info_tab', __( 'Hide Additional Information Tab', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_additional_info_tab'
			), 'ccw_settings_section_description_tabs', 'ccw_section_id' );

			add_settings_field( 'ccw_section_id_checkout_fields', __( 'Hide Specific Checkout field', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_checkout_fields'
			), 'ccw_settings_section_checkout_fields', 'ccw_section_id' );

			add_settings_field( 'ccw_section_id_string_add_to_cart_archive', __( 'Change name for Add to Cart - Archive page', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_string_add_to_cart_archive'
			), 'ccw_settings_section_two', 'ccw_section_id' );

			add_settings_field( 'ccw_section_id_string_add_to_cart_single', __( 'Change name for Add to Cart - Single page', 'custom-conditions-for-woocommerce' ), array(
				$this,
				'ccw_section_id_string_add_to_cart_single'
			), 'ccw_settings_section_two', 'ccw_section_id' );

		}

		public function ccw_settings_section_callback() {
			_e( 'Manage visibility for Add to Cart button.', 'custom-conditions-for-woocommerce' );
			echo '<hr>';
		}

		public function ccw_settings_section_callback_product_prices() {
			_e( 'Manage visibility for Product prices.', 'custom-conditions-for-woocommerce' );
			echo '<hr>';
		}

		public function ccw_settings_section_callback_coupon() {
			_e( 'Manage visibility for Coupon code.', 'custom-conditions-for-woocommerce' );
			echo '<hr>';
		}

		public function ccw_settings_section_callback_description_tabs() {
			_e( 'Manage visibility for Description tabs.', 'custom-conditions-for-woocommerce' );
			echo '<hr>';
		}

		public function ccw_settings_section_callback_checkout_fields() {
			_e( 'Manage visibility for Checkout fields.', 'custom-conditions-for-woocommerce' );
			echo '<br>';
			_e( 'Use checkout fields slugs from the table below.', 'custom-conditions-for-woocommerce' );

			echo '<pre class="ccw-pre-code-table">' . esc_attr( 'First Name  - billing_first_name
Last Name   - billing_last_name
Company     - billing_company
Address 1   - billing_address_1
Address 2   - billing_address_2
City        - billing_city
Postcode    - billing_postcode
Country     - billing_country
State       - billing_state
Phone       - billing_phone
Email       - billing_email' ) . '</pre>';
			echo '<hr>';
		}

		public function ccw_settings_section_strings_callback() {
			// CHANGE DESCRIPTION LATER
			_e( 'Change text for Add to Cart button.', 'custom-conditions-for-woocommerce' );
			echo '<hr>';
		}

		public function ccw_section_id_card_button_archive() {
			$this->ccw_settings_fields( 'checkbox', 'ccw-card-button-archive', 'ccw-switch-input', 'card_button_archive', $this->ccw_option_check_radio_btn( 'card_button_archive' ) );
		}

		public function ccw_section_id_card_button_single() {
			$this->ccw_settings_fields( 'checkbox', 'ccw-card-button-single', 'ccw-switch-input', 'card_button_single', $this->ccw_option_check_radio_btn( 'card_button_single' ) );
		}

		public function ccw_section_id_card_button_category() {
			$this->ccw_settings_fields( 'checkbox', 'ccw-card-button-category', 'ccw-switch-input', 'card_button_category', $this->ccw_option_check_radio_btn( 'card_button_category' ) );
		}

		public function ccw_section_id_prices_all() {
			$this->ccw_settings_fields( 'checkbox', 'ccw-prices-all', 'ccw-switch-input', 'prices_all', $this->ccw_option_check_radio_btn( 'prices_all' ) );
		}

		public function ccw_section_id_prices_archive() {
			$this->ccw_settings_fields( 'checkbox', 'ccw-prices-archive', 'ccw-switch-input', 'prices_archive', $this->ccw_option_check_radio_btn( 'prices_archive' ) );
		}

		public function ccw_section_id_prices_category() {
			$this->ccw_settings_fields( 'checkbox', 'ccw-prices-category', 'ccw-switch-input', 'prices_category', $this->ccw_option_check_radio_btn( 'prices_category' ) );
		}

		public function ccw_section_id_prices_google() {
			$this->ccw_settings_fields( 'checkbox', 'ccw-prices-google', 'ccw-switch-input', 'prices_google', $this->ccw_option_check_radio_btn( 'prices_google' ), '', __( 'This change won\'t affect your website immediately. It will affect it when Google robots re-crawl your website again. That can take up to 40 days.', 'custom-conditions-for-woocommerce' ) );
		}

		public function ccw_section_id_coupon_checkout() {
			$this->ccw_settings_fields( 'checkbox', 'ccw-coupon-checkout', 'ccw-switch-input', 'coupon_checkout', $this->ccw_option_check_radio_btn( 'coupon_checkout' ) );
		}

		public function ccw_section_id_coupon_cart() {
			$this->ccw_settings_fields( 'checkbox', 'ccw-coupon-cart', 'ccw-switch-input', 'coupon_cart', $this->ccw_option_check_radio_btn( 'coupon_cart' ) );
		}

		public function ccw_section_id_description_tab() {
			$this->ccw_settings_fields( 'checkbox', 'ccw-description-tab', 'ccw-switch-input', 'description_tab', $this->ccw_option_check_radio_btn( 'description_tab' ) );
		}

		public function ccw_section_id_review_tab() {
			$this->ccw_settings_fields( 'checkbox', 'ccw-review-tab', 'ccw-switch-input', 'review_tab', $this->ccw_option_check_radio_btn( 'review_tab' ) );
		}

		public function ccw_section_id_additional_info_tab() {
			$this->ccw_settings_fields( 'checkbox', 'ccw-additional-info-tab', 'ccw-switch-input', 'additional_info_tab', $this->ccw_option_check_radio_btn( 'additional_info_tab' ) );
		}

		public function ccw_section_id_checkout_fields() {
			$this->ccw_settings_fields( 'text', 'ccw-checkout-fields', 'ccw-settings-field', 'checkout_fields', esc_attr__( sanitize_text_field( $this->ccw_options_check( 'checkout_fields' ) ) ), 'billing_first_name, billing_city, billing_phone', __( 'Add checkout field slug comma separated.', 'custom-conditions-for-woocommerce' ) );
		}

		public function ccw_section_id_string_add_to_cart_archive() {
			$this->ccw_settings_fields( 'text', 'ccw-string-add-to-cart-archive', 'ccw-settings-field', 'string_add_to_cart_archive', esc_attr__( sanitize_text_field( $this->ccw_options_check( 'string_add_to_cart_archive' ) ) ), __( 'Add to Cart', 'custom-conditions-for-woocommerce' ), );
		}

		public function ccw_section_id_string_add_to_cart_single() {
			$this->ccw_settings_fields( 'text', 'ccw-string-add-to-cart-single', 'ccw-settings-field', 'string_add_to_cart_single', esc_attr__( sanitize_text_field( $this->ccw_options_check( 'string_add_to_cart_single' ) ) ), __( 'Add to Cart', 'custom-conditions-for-woocommerce' ) );
		}

	}

	new Ccw_Settings();
}