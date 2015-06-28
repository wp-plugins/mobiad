<?php
/*
Plugin Name: MobiAd.uk
Plugin URI: http://mobiad.uk
Version: 0.0.1
Author: MobiAd.uk
Author URI: http://mobiad.uk
Description: This plugin allows WordPress users to monetise their mobile web traffic.
License: GPL2 or later
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

if ( ! defined( 'ABSPATH' ) ) die();


// Hook for adding admin menus
// admin actions
if ( is_admin() ){ 

		// Hook for adding admin menu
		add_action( 'admin_menu', 'mobiaduk_admin_menu' );

		// Display the 'Settings' link in the plugin row on the installed plugins list page
		add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'mobiaduk_admin_plugin_actions', -10);

} 
else 
{
	add_action('wp_footer', 'mobiaduk_insert_adtag');
	remove_filter( 'wp_footer', 'strip_tags' );
}


/*
*	action function for above hook
*/
function mobiaduk_admin_menu()
{
	// Add a new submenu under Settings:
	add_options_page(__('MobiAd Settings','mobiaduk-menu'), __('MobiAd Settings','mobiaduk-menu'), 'manage_options', 'mobiaduk', 'mobiaduk_settings_page');

}

/*
*	fc_settings_page() displays the page content for 
* the Header and Footer Commander submenu
*/
function mobiaduk_settings_page() 
{

	if (!current_user_can('manage_options'))
	{
	  wp_die( __('You do not have sufficient permissions to access this page.') );
	}
		
	$mobiad_js_adtag = get_option('mobiad_js_adtag');
	

	if( isset($_POST['submit']) )
	{
     
		if(is_numeric($_POST['ma-ad-code']) AND is_numeric($_POST['testad']) AND is_numeric($_POST['testad']) )	
		{				
				update_option('mobiad_js_adtag', sanitize_text_field($_POST['ma-ad-code'])); 
				update_option('mobiad_test', sanitize_text_field($_POST['testad'])); 

				echo '<div class="updated"><p><strong>';
					 _e('settings saved.', 'fc-menu' );
				echo '</strong></p></div>';
		}
		else
		{
				echo '<div class="updated"><p><strong>';
				_e('Validation error. Settings not saved.', 'fc-menu' );
				echo '</strong></p></div>';
		}
}


	// Now display the settings editing screen
	echo '<div class="wrap">';    
	// icon for settings
	 echo '<div id="icon-plugins" class="icon32"></div>';
	// header
	echo "<h2>" . __( 'MobiAd Settings', 'fc-menu' ) . "</h2>";    


	$settingsForm = file_get_contents( plugin_dir_path( __FILE__ ) . 'form.html');
	
	$settingsForm = str_replace(
		'{ma-ad-code-js}',
		get_option('mobiad_js_adtag'), 
		$settingsForm
	);	

	$test_state = get_option('mobiad_test');
	$options = '<option value="0" '.(($test_state == "0" OR empty($test_state)) ? 'selected' : '').'>No</option>'
					 . '<option value="1" '.(($test_state == "1") ? 'selected' : '').'>Yes</option>';

	$settingsForm = str_replace(
		'{ma-test}',
		$options, 
		$settingsForm
	);	
	
	print $settingsForm;
}


// Build array of links for rendering in installed plugins list
function mobiaduk_admin_plugin_actions($links) 
{
	$mobiaduk_plugin_links = array(
		 '<a href="options-general.php?page=mobiaduk">'.__('Settings').'</a>',
	);
	return array_merge( $mobiaduk_plugin_links, $links );
}

function mobiaduk_insert_adtag()
{
	$siteid = get_option('mobiad_js_adtag');	
	$test = get_option('mobiad_test');

	$string = '<script>(function(i,t){ '
		. 'var s = document.createElement("script"), el = document.getElementsByTagName("script")[0]; '
		. 's.async = true; s.src = "http://delivery.mobiad.uk/majsload/'.$siteid.'/'.$test.'.js?r=" + Math.floor(Math.random() * 100000000); '
		. 'el.parentNode.insertBefore(s, el); })(); '
		. '</script>';
	
	print $string;
}



?>
