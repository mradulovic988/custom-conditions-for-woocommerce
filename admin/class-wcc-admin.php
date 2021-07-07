<?php
/**
 * Main class for all admin side communication
 *
 * @class Wcc_Admin
 * @package Wcc_Admin
 * @version 1.0.0
 * @author Marko Radulovic
 */

if ( ! class_exists( 'Wcc_Admin' ) ) {
	class Wcc_Admin {
		public function __construct() {
			include WCC_PLUGIN_PATH . '/admin/api/class-wcc-settings.php';

			if ( is_admin() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'wcc_enqueue_admin_styles' ) );
			}
		}

		public function wcc_enqueue_admin_styles() {
			wp_enqueue_style( 'wcc_admin_css', plugins_url( '/assets/css/wcc_admin_style.css', __FILE__ ) );
			wp_enqueue_script( 'wcc_admin_js', plugins_url( '/assets/js/wcc_admin_script.js', __FILE__ ), array(), '1.0.0', true );
		}
	}

	new Wcc_Admin();
}