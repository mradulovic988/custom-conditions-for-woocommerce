<?php

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
				add_settings_error( 'wcc_settings_fields', 'sucess', $message, 'success' );
			}
		}

		public function wcc_options_check( string $id ): string {
			$options = get_option( 'wcc_settings_fields' );

			return ( ! empty( $options[ $id ] ) ? $options[ $id ] : '' );
		}

		public function wcc_register_submenu_page() {
			add_menu_page(
				__( 'WooCommerce Conditions', 'wcc' ),
				__( 'WooCommerce Conditions', 'wcc' ),
				'manage_options',
				'woocommerce-conditions',
				array( $this, 'wcc_register_submenu_page_callback' ),
				'dashicons-schedule'
			);

			add_submenu_page(
				'woocommerce-conditions',
				__( 'Visibility', 'wcc' ),
				__( 'Visibility', 'wcc' ),
				'manage_options',
				'woocommerce-conditions',
				array( $this, 'wcc_register_submenu_page_callback' )
			);

			add_submenu_page(
				'woocommerce-conditions',
				__( 'Strings', 'wcc' ),
				__( 'Strings', 'wcc' ),
				'manage_options',
				'woocommerce-conditions-strings',
				array( $this, 'wcc_register_submenu_page_string_callback' )
			);

			add_submenu_page(
				'woocommerce-conditions',
				__( 'Documentation', 'wcc' ),
				__( 'Documentation', 'wcc' ),
				'manage_options',
				'woocommerce-conditions-documentation',
				array( $this, 'wcc_register_submenu_page_documentation_callback' )
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

		public function wcc_option_check_radio_btn( string $id ): string {
			$options = get_option( 'wcc_settings_fields' );

			return isset( $options[ $id ] ) ? checked( 1, $options[ $id ], false ) : '';
		}

		/**
		 * Admin pages nav menu
		 *
		 * @param $active_tab
		 * @param $is_active
		 * @param $is_next
		 */
		protected function wcc_is_active( $active_tab, $is_active, $is_next ) {
			?>
            <h2 class="nav-tab-wrapper">
                <a href="?page=woocommerce-conditions" class="nav-tab <?php if ( $active_tab == 'woocommerce-conditions' ) {
					echo 'nav-tab-active';
				} ?> "><?php _e( 'Visibility', 'wcc' ); ?></a>

                <a href="?page=woocommerce-conditions-strings" class="nav-tab <?php if ( $active_tab == 'woocommerce-conditions-strings' ) {
					echo 'nav-tab-active';
				} ?> "><?php _e( 'Strings', 'wcc' ); ?></a>

                <a href="?page=woocommerce-conditions-documentation" class="nav-tab <?php if ( $active_tab == 'woocommerce-conditions-documentation' ) {
					echo 'nav-tab-active';
				} ?> "><?php _e( 'Documentation', 'wcc' ); ?></a>
            </h2>
			<?php

			$active_tab = $is_active;

			if ( isset( $_GET["tab"] ) ) {

				if ( $_GET["tab"] == $is_active ) {
					$active_tab = $is_active;
				} else {
					$active_tab = $is_next;
				}
			}
		}

		public function wcc_register_submenu_page_callback() {
			?>
            <div id="agy-wrap" class="wrap">
				<?php $this->wcc_is_active( 'woocommerce-conditions', 'woocommerce-conditions', 'woocommerce-conditions-strings' ); ?>
                <form action="options.php" method="post">

					<?php
					settings_errors( 'wcc_settings_fields' );
					wp_nonce_field( 'wcc_dashboard_save', 'wcc_form_save_name' );
					settings_fields( 'wcc_settings_fields' );
					do_settings_sections( 'wcc_settings_section_one' );

					submit_button(
						__( 'Save Changes', 'wcc' ),
						'',
						'wcc_save_changes_btn',
						true,
						array( 'id' => 'wcc-save-changes-btn' )
					);
					?>

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

		public function wcc_register_submenu_page_string_callback() {
			?>
            <div id="agy-wrap" class="wrap">
				<?php $this->wcc_is_active( 'woocommerce-conditions-strings', 'woocommerce-conditions-strings', 'woocommerce-conditions-documentation' ); ?>
                <form action="options.php" method="post">

					<?php
					settings_errors( 'wcc_settings_fields' );
//					wp_nonce_field( 'wcc_dashboard_save', 'wcc_form_save_name' );
					settings_fields( 'wcc_settings_fields' );
					do_settings_sections( 'wcc_settings_section_two' );

					submit_button(
						__( 'Save Changes', 'wcc' ),
						'',
						'wcc_save_changes_btn',
						true,
						array( 'id' => 'wcc-save-changes-btn' )
					);
					?>

                </form>

				<?php
//				if ( ! isset( $_POST['wcc_form_save_name'] ) ||
//				     ! wp_verify_nonce( $_POST['wcc_form_save_name'], 'wcc_dashboard_save' ) ) {
//					return;
//				}
				?>
            </div>
			<?php
		}

		public function wcc_register_submenu_page_documentation_callback() {
			?>
            <div id="agy-wrap" class="wrap">
				<?php $this->wcc_is_active( 'woocommerce-conditions-documentation', 'woocommerce-conditions-documentation', '' ); ?>
                <form action="options.php" method="post">

					<?php
					settings_errors( 'wcc_settings_fields' );
//					wp_nonce_field( 'wcc_dashboard_save', 'wcc_form_save_name' );
					settings_fields( 'wcc_settings_fields' );
					do_settings_sections( 'wcc_settings_section_three' );

					submit_button(
						__( 'Save Changes', 'wcc' ),
						'',
						'wcc_save_changes_btn',
						true,
						array( 'id' => 'wcc-save-changes-btn' )
					);
					?>

                </form>

				<?php
//				if ( ! isset( $_POST['wcc_form_save_name'] ) ||
//				     ! wp_verify_nonce( $_POST['wcc_form_save_name'], 'wcc_dashboard_save' ) ) {
//					return;
//				}
				?>
            </div>
			<?php
		}

		public function wcc_register_settings() {

			register_setting( 'wcc_settings_fields', 'wcc_settings_fields', 'wcc_sanitize_callback' );

			// Adding sections
			add_settings_section( 'wcc_section_id', __( 'Visibility', 'wcc' ), array(
				$this,
				'wcc_settings_section_callback'
			), 'wcc_settings_section_one' );

			add_settings_section( 'wcc_section_id', __( 'Strings', 'wcc' ), array(
				$this,
				'wcc_settings_section_strings_callback'
			), 'wcc_settings_section_two' );

			add_settings_section( 'wcc_section_id', __( 'Documentation', 'wcc' ), array(
				$this,
				'wcc_settings_section_documentation_callback'
			), 'wcc_settings_section_three' );

			// Visibility page fields
			add_settings_field( 'wcc_section_id_card_button_archive', __( 'Hide Add to Card button - Archive page', 'wcc' ), array(
				$this,
				'wcc_section_id_card_button_archive'
			), 'wcc_settings_section_one', 'wcc_section_id' );

			add_settings_field( 'wcc_section_id_card_button_single', __( 'Hide Add to Card button - Single product page', 'wcc' ), array(
				$this,
				'wcc_section_id_card_button_single'
			), 'wcc_settings_section_one', 'wcc_section_id' );
		}

		public function wcc_settings_section_callback() {
			// CHANGE DESCRIPTION LATER
			_e( 'Add or remove specific functionality from WooCommerce.', 'wcc' );
		}

		public function wcc_settings_section_strings_callback() {
			// CHANGE DESCRIPTION LATER
			_e( 'Strings.', 'wcc' );
		}

		public function wcc_settings_section_documentation_callback() {
			// CHANGE DESCRIPTION LATER
			_e( 'Documentation.', 'wcc' );
		}

		public function wcc_section_id_card_button_archive() {
			$this->wcc_settings_fields( 'checkbox', 'wcc-card-button-archive', 'wcc-switch-input', 'card_button_archive', $this->wcc_option_check_radio_btn( 'card_button_archive' ) );
		}

		public function wcc_section_id_card_button_single() {
			$this->wcc_settings_fields( 'checkbox', 'wcc-card-button-single', 'wcc-switch-input', 'card_button_single', $this->wcc_option_check_radio_btn( 'card_button_single' ) );
		}
	}

	new Wcc_Settings();
}