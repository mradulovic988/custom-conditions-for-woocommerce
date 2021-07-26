<?php
/**
 * Custom Conditions For WooCommerce
 *
 * @package           Custom_Conditions_For_WooCommerce
 * @author            Marko Radulovic
 * @copyright         2021 Marko Radulovic
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Conditions For WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/custom-conditions-for-woocommerce
 * Description:       If you want quickly to show/hide/rename most of the functionalities in WooCommerce, this is the right plugin.
 * Version:           1.0.3
 * Requires at least: 4.6
 * Requires PHP:      7.4
 * Author:            Marko Radulovic
 * Author URI:        https://mlab-studio.com
 * Text Domain:       custom-conditions-for-woocommerce
 * Domain Path:       /languages
 *
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run plugin settings if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	if ( ! class_exists( 'Custom_Conditions_For_WooCommerce' ) ) {
		class Custom_Conditions_For_WooCommerce {
			private static $instance;

			public function __construct() {
				if ( ! defined( 'CCW_PLUGIN_PATH' ) ) {
					define( 'CCW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
				}

				if ( ! defined( 'CCW_PLUGIN_BASENAME' ) ) {
					define( 'CCW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
				}

				if ( is_admin() ) {
					include CCW_PLUGIN_PATH . '/admin/class-ccw-admin.php';
					$this->ccw_load_plugin_textdomain();

					add_filter( 'plugin_action_links', array( $this, 'ccw_settings_link' ), 10, 2 );
					add_filter( 'plugin_row_meta', array( $this, 'ccw_set_plugin_meta' ), 10, 2 );
				} else {
					include CCW_PLUGIN_PATH . '/public/class-ccw-public.php';
					include CCW_PLUGIN_PATH . '/includes/class-ccw-includes.php';
				}
			}

			public function ccw_load_plugin_textdomain() {
				load_plugin_textdomain(
					'custom-conditions-for-woocommerce',
					false,
					CCW_PLUGIN_BASENAME . dirname( __FILE__ ) . '/languages'
				);
			}

			/**
			 * Add settings link on plugins page
			 *
			 * @param $links
			 * @param $file
			 *
			 * @return array
			 */
			public function ccw_settings_link( $links, $file ): array {
				if ( $file == CCW_PLUGIN_BASENAME && current_user_can( 'manage_options' ) ) {
					array_unshift(
						$links,
						sprintf( '<a href="%s">' . esc_attr__( 'Settings', 'custom-conditions-for-woocommerce' ), 'admin.php?page=custom-conditions-for-woocommerce' ) . '</a>'
					);
				}

				return $links;
			}

			// Settings link for the plugin
			public function ccw_set_plugin_meta( $links, $file ): array {
				$plugin = plugin_basename( __FILE__ );

				if ( $file == $plugin && current_user_can( 'manage_options' ) ) {
					array_push(
						$links,
						sprintf( '<a target="_blank" href="%s">' . esc_attr__( 'Docs & FAQs', 'custom-conditions-for-woocommerce' ) . '</a>', 'https://wordpress.org/support/plugin/custom-conditions-for-woocommerce' )
					);

					array_push(
						$links,
						sprintf( '<a target="_blank" href="%s">' . esc_attr__( 'GitHub', 'custom-conditions-for-woocommerce' ) . '</a>', 'https://github.com/mradulovic988/custom-conditions-for-woocommerce' )
					);
				}

				return $links;
			}

			public static function ccw_instance(): Custom_Conditions_For_WooCommerce {
				if ( null === self::$instance ) {
					self::$instance = new self();
				}

				return self::$instance;
			}
		}

		Custom_Conditions_For_WooCommerce::ccw_instance();
	}
}

// If WooCommerce is not active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	function ccw_check_plugin() {
		add_action( 'admin_notices', 'ccw_admin_notice' );
		deactivate_plugins( plugin_basename( __FILE__ ) );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}

	add_action( 'admin_init', 'ccw_check_plugin' );

	function ccw_admin_notice() { ?>
        <div class="notice notice-error">
            <p><?php _e( 'Custom Conditions for WooCommerce requires WooCommerce to run. Please install and activate WooCommerce.', 'custom-conditions-for-woocommerce' ) ?></p>
        </div>
	<?php }
}