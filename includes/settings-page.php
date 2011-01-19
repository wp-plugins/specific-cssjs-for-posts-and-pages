<?php
/**
 * @package Specific CSS/JS for Posts and Pages
 */

// Admin menu
function ttscj_admin_menu() {
	// Create menu link under settings
	add_options_page(__('Specific CSS/JS options', 'ttscj'), __('Specific CSS/JS', 'ttscj'), 'manage_options', 'ttscj-options', 'ttscj_options_page');
	
	// Call register settings function
	add_action( 'admin_init', 'ttscj_register_settings' );
}

// Options page
function ttscj_options_page() {
	global $ttscj_general_settings, $ttscj_url;
	?>
		<div class="wrap">
		
			<h2><?php _e('Specific CSS/JS for Posts and Pages', 'ttscj'); ?></h2>
						
			<p><?php _e('To use this feature in the bottom of the Write Post and Write Page you have a box to introduce the URL or URLs of the specific CSS and JS files, also you can add any code in header to specific posts or pages.', 'ttscj'); ?></p>

			<img src="<?php echo $ttscj_url.'screenshot-1.png'; ?>" alt="<?php _e('Screenshot', 'ttscj'); ?>" width="500" height="384" style="margin: 0 auto; display: block;" />

			<?php ttscj_generate_settings_page($ttscj_general_settings, 'ttscj_general_settings'); ?>
			
		</div>
	<?php
}

?>