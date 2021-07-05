<?php

if ( ! class_exists('Wcc_Includes')) {
	class Wcc_Includes {

		public function __construct() {
			include WCC_PLUGIN_PATH . '/includes/class-wcc-conditions.php';
		}
	}

	new Wcc_Includes();
}