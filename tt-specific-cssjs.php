<?php
/**
 * @package Specific CSS/JS for Posts and Pages
 */
/*
Plugin Name: Specific CSS/JS for Posts and Pages
Plugin URI: http://techtastico.com/plugins/
Description: Add CSS or JavaScript files to a specific page or post, even you can inser &lt;style&gt; or &lt;script&gt; blocks in header, all this from Write/Edit Post/Page panel
Version: 1.0
Author: Carlos Leopoldo Magaña Zavala
Author URI: http://techtastico.com
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/**
* Variables and constants
*/
$ttscj_url = WP_PLUGIN_URL.'/'.str_replace(basename(__FILE__), '', plugin_basename(__FILE__)); // URL to plugin folder, includes / in the end
$ttscj_path = WP_PLUGIN_DIR.'/'.str_replace(basename(__FILE__), '', plugin_basename(__FILE__)); // PATH to plugin folder, includes / in the end


/**
* Load translation
*/
load_plugin_textdomain('ttscj', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');


/**
* Requires
*/
require_once( $ttscj_path . 'includes/settings.php' );
if ( is_admin() ) {
	require_once( $ttscj_path . 'includes/settings-page.php' );
}
require_once( $ttscj_path . 'includes/install.php' );


/**
* Actions
*/
add_action( 'admin_menu', 'ttscj_admin_menu' );
register_activation_hook( __FILE__, 'ttscj_activate_plugin'); // Actions to do when activating the plugin
//register_deactivation_hook( __FILE__, 'ttscj_uninstall_plugin'); // Actions to do when deactivating the plugin
add_action( 'add_meta_boxes', 'ttscj_add_meta_box' );
add_action( 'save_post', 'ttscj_save_postdata' );
add_action( 'wp_head', 'ttscj_add_css_js', 12);

/**
* Plugin code here
*/

// Add the code to header in post or pages
function ttscj_add_css_js() {
	global $post;
	
	if( ($post->post_type == 'post' && get_option('ttscj_enable_on_posts') != '') || ($post->post_type == 'page' && get_option('ttscj_enable_on_pages') != '') ) {
		$ttscj_css_url = get_post_meta($post->ID, '_ttscj_css_url', true);
		$ttscj_css_url = str_replace('  ', ' ', $ttscj_css_url);
		$ttscj_css_url = trim($ttscj_css_url);
	
		$ttscj_js_url = get_post_meta($post->ID, '_ttscj_js_url', true);
		$ttscj_js_url = str_replace('  ', ' ', $ttscj_js_url);
		$ttscj_js_url = trim($ttscj_js_url);
	
		$ttscj_header_code = get_post_meta($post->ID, '_ttscj_header_code', true);
		$ttscj_header_code = trim($ttscj_header_code);
		
		$css_urls = explode(' ', $ttscj_css_url);
		
		foreach($css_urls as $css_url) {
			if( $css_url != '' )
				echo '<link rel="stylesheet" type="text/css" href="'.$css_url.'" media="all" />'."\n";
		}
		
		$js_urls = explode(' ', $ttscj_js_url);
		foreach($js_urls as $js_url) {
			if( $js_url != '' )
				echo '<script type="text/javascript" src="'.$js_url.'"></script>'."\n";
		}
		
		if($ttscj_header_code != '') {
			echo $ttscj_header_code."\n";
		}
	}
}


// Add meta box as a custom write panel
function ttscj_add_meta_box() {
	$post_types = get_post_types('','names'); 
	foreach ( $post_types as $post_type ) { // get all post types
		if( (get_option('ttscj_enable_on_posts') != '' && $post_type == 'post') || (get_option('ttscj_enable_on_pages') != '' &&  $post_type == 'page') ) {
			add_meta_box('ttscj', __('Specific CSS or Javascript', 'ttscj'), 'ttscj_meta_box', $post_type, 'advanced', 'default');
		}
	}
}


// Meta box content
function ttscj_meta_box() {

	$post_id = 	!empty($_GET['post']) ? $_GET['post'] : 0;
	
	// Use nonce for verification
	wp_nonce_field( plugin_basename(__FILE__), 'ttscj' );
	
	// The actual fields for data entry
	echo '<p>'.__('Here you can specify CSS or JavaScript files to be used only in this entry, in addition you can write code that will be added into header of the page of this entry.', 'ttscj').'</p>';
	
	echo '<p><label for="ttscj_css_url">' . __("URL of the custom CSS file", 'ttscj' ) . '</label> ';
	echo '<input id="ttscj_css_url" class="code" type="text" value="'.get_post_meta($post_id, '_ttscj_css_url', true).'" name="_ttscj_css_url" style="width:99%;" /><br />';
	echo __('(Separate multiple URLs with spaces)', 'ttscj').'</p>';

	echo '<p><label for="ttscj_js_url">' . __("URL of the custom Javascript file", 'ttscj' ) . '</label> ';
	echo '<input id="ttscj_js_url" class="code" type="text" value="'.get_post_meta($post_id, '_ttscj_js_url', true).'" name="_ttscj_js_url" style="width:99%;" /><br />';
	echo __('(Separate multiple URLs with spaces)', 'ttscj').'</p>';
	
	echo '<p><label for="ttscj_header_code">' . __("Write here custom CSS or Javascript for this entry, it will be added within tags &lt;header&gt;", 'ttscj' ) . '</label> ';
	echo '<textarea id="ttscj_header_code" class="attachmentlinks" name="_ttscj_header_code" cols="40" rows="1" style="height:180px;">'.get_post_meta($post_id, '_ttscj_header_code', true).'</textarea>';
	echo __('(Do not forget to include opening and closign tags for CSS &lt;style&gt; or Javascript &lt;script&gt;)', 'ttscj').'</p>';

}


// When post is saved, saves our meta data
function ttscj_save_postdata( $post_id ) {
	
	// verify this came from the our screen and with proper authorization,
	if ( empty($_POST['ttscj']) || !wp_verify_nonce( $_POST['ttscj'], plugin_basename(__FILE__) )) {
		return;
	} else {
		
		// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return $post_id;
		} else {
			// Check permissions
			if ( $_POST['post_type'] == 'page' ) {
				if ( !current_user_can( 'edit_page', $post_id ) )
				  return $post_id;
			} else {
				if ( !current_user_can( 'edit_post', $post_id ) )
				  return $post_id;
			}
			// OK, we're authenticated: we need to find and save the data
			$ttscj_css_url = $_POST['_ttscj_css_url'];
			$ttscj_js_url = $_POST['_ttscj_js_url'];
			$ttscj_header_code = $_POST['_ttscj_header_code'];
			
			// Add the data as a post meta
			ttscj_update_delete_post_meta($post_id, '_ttscj_css_url', $ttscj_css_url);
			ttscj_update_delete_post_meta($post_id, '_ttscj_js_url', $ttscj_js_url);
			ttscj_update_delete_post_meta($post_id, '_ttscj_header_code', $ttscj_header_code);
			
			return;
		}
	}
}


// Updates or delete a post meta
function ttscj_update_delete_post_meta($post_id, $key, $data) {

	$post_meta = get_post_meta($post_id, $key, true);
	
	if( $data != '' && $post_meta != $data) {
		update_post_meta($post_id, $key, $data);
	} elseif ( $post_meta != '' && $data == '' ) {
		delete_post_meta($post_id, $key);
	}
}

?>