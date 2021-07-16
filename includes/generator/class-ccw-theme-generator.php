<?php
/**
 * Theme Generator
 *
 * @class Ccw_Theme_Generator
 * @package Ccw_Theme_Generator
 * @version 1.0.0
 * @author Marko Radulovic
 * @since 1.0.3
 */

if ( ! class_exists( 'Ccw_Theme_Generator' ) ) {
	class Ccw_Theme_Generator {
		public WP_Theme $theme;
		public array $text_domains = array(
			'betheme' => 'betheme_hook_to_be_removed',
			'astra'   => 'astra_hook_to_be_removed',
			'divi'    => 'divi_hook_to_be_removed',
			'enfold'  => 'enfold_hook_to_be_removed',
		);

		public function __construct() {
			$this->theme = wp_get_theme();
			$this->ccw_theme_generator();
		}

		public function ccw_theme_generator() {
			foreach ( $this->text_domains as $key => $value ) {
				if ( $this->theme->get( 'TextDomain' ) === $key ) {
					// Do something if the generator catch specific theme

				}
			}
		}
	}

	new Ccw_Theme_Generator();
}