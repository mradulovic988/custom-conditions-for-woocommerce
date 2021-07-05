<?php
/**
 * WooCommerce Conditions
 *
 * @package           WooCommerceConditions
 * @author            Marko Radulovic
 * @copyright         2021 Marko Radulovic
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Conditions
 * Plugin URI:        https://wordpress.org/plugins/woocommerce-conditions
 * Description:       Description of the plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Marko Radulovic
 * Author URI:        https://mlab-studio.com
 * Text Domain:       wcc
 * Domain Path:       /languages
 *
 * Woo:               12345:342928dfsfhsf8429842374wdf4234sfd
 * WC requires at least: 2.2
 * WC tested up to:   2.3
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
	if ( ! class_exists( 'WooCommerce_Conditions' ) ) {
		class WooCommerce_Conditions {
			public function __construct() {
				if ( ! defined( 'WCC_PLUGIN_PATH' ) ) {
					define( 'WCC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
				}

				if ( ! defined( 'WCC_PLUGIN_BASENAME' ) ) {
					define( 'WCC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
				}

				if ( is_admin() ) {
					include WCC_PLUGIN_PATH . '/admin/class-wcc-admin.php';
					$this->wcc_load_plugin_textdomain();

					add_filter( 'plugin_action_links', array( $this, 'wcc_settings_link' ), 10, 2 );
				} else {
					include WCC_PLUGIN_PATH . '/public/class-wcc-public.php';
					include WCC_PLUGIN_PATH . '/includes/class-wcc-includes.php';
				}

			}

			public function wcc_load_plugin_textdomain() {
				load_plugin_textdomain(
					'wcc',
					false,
					WCC_PLUGIN_BASENAME . dirname( __FILE__ ) . '/languages'
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
			public function wcc_settings_link( $links, $file ): array {
				if ( $file == WCC_PLUGIN_BASENAME && current_user_can( 'manage_options' ) ) {
					array_unshift(
						$links,
						sprintf( '<a href="%s">' . __( 'Settings', 'wcc' ), 'admin.php?page=woocommerce-conditions' ) . '</a>'
					);
				}

				return $links;
			}
		}

		new WooCommerce_Conditions();
	}
}

// If WooCommerce is not active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	function wcc_check_plugin() {
		add_action( 'admin_notices', 'wcc_admin_notice' );
		deactivate_plugins( plugin_basename( __FILE__ ) );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
	add_action( 'admin_init', 'wcc_check_plugin' );

	function wcc_admin_notice() { ?>
        <div class="notice notice-error">
            <p><?php _e( 'WooCommerce Conditions requires WooCommerce to run. Please install and activate WooCommerce.', 'wcc' ) ?></p>
        </div>
	<?php }
}