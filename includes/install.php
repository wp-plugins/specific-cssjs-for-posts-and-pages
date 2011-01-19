<?php
/**
 * @package Specific CSS/JS for Posts and Pages
 */

// Actions to do when activating the plugin
function ttscj_activate_plugin() {
	global $ttscj_settings_groups;

	// Add some options with default values
	foreach ( $ttscj_settings_groups as $settings_group ) {
		foreach ( $settings_group as $settings ) {
			if ( ( $settings['type'] == 'text' || $settings['type'] == 'textarea' || $settings['type'] == 'select' || $settings['type'] == 'checkbox' ) && isset( $settings['default'] ) ) {
				add_option( $settings['id'], $settings['default'] );
			}
		}
	}
}

// Actions to do when uninstalling the plugin
function ttscj_uninstall_plugin() {
	global $ttscj_settings_groups;

	// Delete all the options
	foreach ( $ttscj_settings_groups as $settings_group ) {
		foreach ( $settings_group as $settings ) {
			if ( $settings['type'] == 'text' || $settings['type'] == 'textarea' || $settings['type'] == 'select' || $settings['type'] == 'checkbox' ) {
				delete_option( $settings['id'] );
			}
		}
	}
}

?>