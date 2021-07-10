<?php
/**
 * Main class for all admin side communication
 *
 * @class Ccw_Admin
 * @package Ccw_Admin
 * @version 1.0.0
 * @author Marko Radulovic
 */

if ( ! class_exists( 'Ccw_Admin' ) ) {
	class Ccw_Admin {
		public function __construct() {
			include CCW_PLUGIN_PATH . '/admin/api/class-ccw-settings.php';

			if ( is_admin() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'ccw_enqueue_admin_styles' ) );
			}
		}

		public function ccw_enqueue_admin_styles() {
			wp_enqueue_style( 'ccw_admin_css', plugins_url( '/assets/css/ccw_admin_style.css', __FILE__ ) );
			wp_enqueue_script( 'ccw_admin_js', plugins_url( '/assets/js/ccw_admin_script.js', __FILE__ ), array(), '1.0.0', true );
		}
	}

	new Ccw_Admin();
}