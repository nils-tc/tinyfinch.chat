<?php

/**
 * Plugin Name: Tiny Finch
 * Plugin URI: http://tinyfinch.chat/
 * Description: Add the Tiny Finch widget to your website, receive and reply to messages on Slack.
 * Version: 1.0
 * Requires at least: 4.1
 * Requires PHP: 7.2
 * Author: Prune
 * Author URI: http://prune.sh/
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tiny-finch
 */

if (! defined('ABSPATH') ) {
	exit;
}

/**
 * Function to add the custom Tiny Finch script tag in the DOM
 * 
 * @return void
 */
function tinyfinch_enqueue_custom_script() {
	$tinyfinch_data_t = get_option('tinyfinch_data_t');
	if (!$tinyfinch_data_t) {
		return;
	}

	// Enqueue the script
	wp_enqueue_script(
		'tinyfinch-script', 
		'https://tinyfinch.chat/client.js', 
		array(), 
		'1.0.0', 
		array('strategy' => 'defer',)
	);
	
	// Add the custom attributes using the proper WordPress way
	add_filter('script_loader_tag', 'tinyfinch_add_attributes', 10, 2);
}

/**
 * Hook to add the data parameters to Tiny Finch the script tag
 * 
 * @param string $tag	 original script tag
 * @param string $handle name of the script tag
 * 
 * @return string		 updated script tag
 */
function tinyfinch_add_attributes($tag, $handle) {
	if ('tinyfinch-script' === $handle) {
		$tinyfinch_data_t = esc_attr(get_option('tinyfinch_data_t'));
		$tinyfinch_data_profile = esc_attr(get_option('tinyfinch_data_profile'));
		// Modify the existing enqueued script tag instead of creating a new one
		$tag = str_replace('<script ', '<script id="7a4696e" data-t="' . $tinyfinch_data_t . '" data-profile="' . $tinyfinch_data_profile . '" ', $tag);
	}
	return $tag;
}
add_action('wp_enqueue_scripts', 'tinyfinch_enqueue_custom_script');

/**
 * Function to add the custom Tiny Finch settings menu
 * 
 * @return void
 */
function tinyfinch_add_menu() {
	add_options_page(
		esc_html__('Tiny Finch Settings', 'tiny-finch'),
		esc_html__('Tiny Finch Settings', 'tiny-finch'),
		'manage_options',
		'tinyfinch-settings',
		'tinyfinch_display_settings_page'
	);
}

/**
 * Function to return the Tiny Finch admin settings page content
 * 
 * @return string content of the settings page
 */
function tinyfinch_display_settings_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e('Tiny Finch Settings', 'tiny-finch'); ?></h1>
		<p><?php esc_html_e('Tiny Finch is a subscription based live chat service.', 'tiny-finch'); ?><br/><?php esc_html_e('Your website visitors write to you in the chat widget and you answer them from your Slack workspace.', 'tiny-finch'); ?><br/><?php esc_html_e('To start, you first need to sign-up and activate your subscription on', 'tiny-finch'); ?> <a href="https://tinyfinch.chat" target="_blank" rel="noopener"><?php esc_html_e('the Tiny Finch website', 'tiny-finch'); ?></a>.</p>
		<p><?php esc_html_e('To customize your Tiny Finch widget, please go to', 'tiny-finch'); ?> <a href="https://tinyfinch.chat/dashboard" target="_blank" rel="noopener"><?php esc_html_e('the Tiny Finch dashboard', 'tiny-finch'); ?></a>.<br/><?php esc_html_e('You will find your "Team ID" (go to Team > Basic info) and "Profile Name" (go to Widget) to report here.', 'tiny-finch'); ?></p>
		<form method="post" action="options.php">
			<?php
			settings_fields('tinyfinch-options-group');
			do_settings_sections('tinyfinch-settings');
			?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e('Your Tiny Finch Team ID:', 'tiny-finch'); ?></th>
					<td>
						<input type="text" name="tinyfinch_data_t" value="<?php echo esc_attr(get_option('tinyfinch_data_t')); ?>" />
						<p class="description"><?php esc_html_e('Your Tiny Finch Team ID, you can find it on the team page of your Tiny Finch dashboard', 'tiny-finch'); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e('Your Tiny Finch Profile name:', 'tiny-finch'); ?></th>
					<td>
						<input type="text" name="tinyfinch_data_profile" value="<?php echo esc_attr(get_option('tinyfinch_data_profile')); ?>" />
						<p class="description"><?php esc_html_e('Your Tiny Finch Profile name, you can find it on the widget page of your Tiny Finch dashboard', 'tiny-finch'); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
add_action('admin_menu', 'tinyfinch_add_menu');

/**
 * Function to register the two custom Tiny Finch settings
 * 
 * @return void
 */
function tinyfinch_register_settings() {
	register_setting(
		'tinyfinch-options-group',
		'tinyfinch_data_t',
		array(
			'type' => 'string',
			'label' => esc_attr__('Team ID', 'tiny-finch'),
			'description' => esc_attr__('Your Tiny Finch Team ID, you can find it on the team page of your Tiny Finch dashboard.', 'tiny-finch'),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	register_setting(
		'tinyfinch-options-group',
		'tinyfinch_data_profile',
		array(
			'type' => 'string',
			'label' => esc_attr__('Profile Name', 'tiny-finch'),
			'description' => esc_attr__('Your Tiny Finch Profile name, you can find it on the widget page of your Tiny Finch dashboard.', 'tiny-finch'),
			'sanitize_callback' => 'sanitize_text_field',
			'default' => 'default',
		)
	);
}
add_action('admin_init', 'tinyfinch_register_settings');