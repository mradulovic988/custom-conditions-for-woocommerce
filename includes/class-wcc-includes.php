<?php
/**
 * Main class for all communication between front-end and with the back-end
 *
 * @class Wcc_Includes
 * @package Wcc_Includes
 * @version 1.0.0
 * @author Marko Radulovic
 */

if ( ! class_exists('Wcc_Includes')) {
	class Wcc_Includes {

		public function __construct() {
			include WCC_PLUGIN_PATH . '/includes/class-wcc-conditions.php';
		}
	}

	new Wcc_Includes();
}