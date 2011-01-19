<?php
/**
 * @package Specific CSS/JS for Posts and Pages
 */

// Array with setting for the plugin
$ttscj_general_settings = array (
	array(	"type" => "open"),

	array(	"name" => __('General options', 'ttscj'),
			"type" => "title"),

	array(	"name" => __('Specific CSS/JS on posts', 'ttscj'),
			"desc" => __('Enable specific CSS/JS on posts', 'ttscj'),
			"id" => "ttscj_enable_on_posts",
			"default" => true,
			"type" => "checkbox"),

	array(	"name" => __('Specific CSS/JS on pages', 'ttscj'),
			"desc" => __('Enable specific CSS/JS on pages', 'ttscj'),
			"id" => "ttscj_enable_on_pages",
			"default" => true,
			"type" => "checkbox"),

	array(	"type" => "close")

);


// Combine all the settings pages
global $ttscj_settings_groups;
$ttscj_settings_groups = array( 'ttscj_general_settings' => $ttscj_general_settings );

// Register settings
function ttscj_register_settings() {
	global $ttscj_settings_groups;

	foreach ( $ttscj_settings_groups as  $group => $settings_group ) {
		foreach ( $settings_group as $settings ) {
			if ( $settings['type'] == 'text' || $settings['type'] == 'textarea' || $settings['type'] == 'select' || $settings['type'] == 'checkbox' ) {
				register_setting( $group, $settings['id'] );
			}
		}
	}
}

// Function to generate settings form
function ttscj_generate_settings_page($settings = array(), $group) {
	?>
		<form method="post" action="options.php">
		
		<?php settings_fields( $group ); ?>
		
		<?php foreach ($settings as $value) { 
		
		switch ( $value['type'] ) {
		
			case "open":
			?>
				<table class="form-table">
			
			<?php break;
			
			case "close":
			?>
				</table>
			
			<?php break;
			
			case "title":
			?>
			
				<tr valign="top">
					<th scope="row" colspan="2">
						<h3><?php echo $value['name']; ?></h3>
					</th>
				</tr>
	
			<?php break;
	
			case 'text':
			?>
			
				<tr valign="top">
					<th scope="row"><?php echo $value['name']; ?></th>
					<td>
						<label>
							<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php echo stripslashes( get_option( $value['id'] ) ); ?>" class="regular-text" />
							<span class="description"><?php echo $value['desc']; ?></span>
						</label>
					</td>
				</tr>
				
			<?php 
			break;
			
			case 'textarea':
			?>
			
				<tr valign="top">
					<th scope="row"><?php echo $value['name']; ?></th>
					<td>
						<span class="description"><?php echo $value['desc']; ?></span><br />
						<textarea name="<?php echo $value['id']; ?>" cols="50" rows="6" class="large-text code"><?php echo stripslashes (get_option( $value['id'] ) ); ?></textarea>
						</td>
				</tr>
				
			<?php 
			break;
			
			case 'select':
			?>
	
				<tr valign="top">
					<th scope="row"><?php echo $value['name']; ?></th>
					<td>
						<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
						<?php foreach ($value['options'] as $option) { ?>
							<option<?php if( get_option( $value['id'] ) == $option[1] ) { echo ' selected="selected"'; } ?> value="<?php echo $option[1]; ?>"><?php echo $option[0]; ?></option>
						<?php } ?>
						</select>
						<br /><span class="description"><?php echo $value['desc']; ?></span>
					</td>
				</tr>
	
			<?php
			break;
				
			case "checkbox":
			?>
	
				<tr valign="top">
					<th scope="row"><?php echo $value['name']; ?></th>
					<td>
						<label><?php if( get_option($value['id']) ) { $checked = "checked=\"checked\""; } else { $checked = ""; } ?>
						<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
						<span class="description"><?php echo $value['desc']; ?></span>
						</label>
					</td>
				</tr>
				
			<?php 		break;
		
			} 
		}
		?>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save settings', 'ttscj'); ?>" />
			</p>
			
		</form>
	<?php
}

?>