<?php

if ( ! class_exists( 'Wcc_Admin' ) ) {
	class Wcc_Admin {
		public function __construct() {
			include WCC_PLUGIN_PATH . '/admin/api/class-wcc-settings.php';
		}
	}

	new Wcc_Admin();
}