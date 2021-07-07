<?php
/**
 * Main class for all public side communication
 *
 * @class Wcc_Public
 * @package Wcc_Public
 * @version 1.0.0
 * @author Marko Radulovic
 */

if ( ! class_exists( 'Wcc_Public' ) ) {
	class Wcc_Public {
		public function __construct() {
			include WCC_PLUGIN_PATH . '/public/class-wcc-conditions.php';
		}
	}

	new Wcc_Public();
}