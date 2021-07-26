<?php
/**
 * Main class for all public side communication
 *
 * @class Ccw_Public
 * @package Ccw_Public
 * @version 1.0.0
 * @author Marko Radulovic
 */

if ( ! class_exists( 'Ccw_Public' ) ) {
	class Ccw_Public {
		public function __construct() {
			include CCW_PLUGIN_PATH . '/public/class-ccw-conditions.php';
		}

		public function ccw_enqueue_public_styles() {
			wp_enqueue_style( 'ccw_public_css', plugins_url( '/assets/css/ccw_public_style.css', __FILE__ ) );
		}
	}

	new Ccw_Public();
}