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
	}

	new Ccw_Public();
}