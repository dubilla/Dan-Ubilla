<?php
/*
 Plugin Name: Facebook Social Connect Plugin
 Plugin URI: http://arcgate.com/facebook-connect
 Description: Allows the use of Facebook Connect for account registration, authentication, and commenting.
 Author: Arcgate
 Author URI: http://arcgate.com/
 Version: 1.1
 License: GPL (http://www.fsf.org/licensing/licenses/info/GPLv2.html)
 */

 if ( !defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');

$fbPluginUrl = plugins_url ( plugin_basename ( dirname ( __FILE__ ) ) );
define('FB_PLUGIN_URL', $fbPluginUrl);

require_once('config/fb_config.php');
require_once('fbc_user.php');
require_once('fb_fbml_tags.php');
require_once('fbcAdminControls.php');

/**
 * Add Css file in head section
 *
 */
function fbc_styles() {
	wp_enqueue_style('facebook-connect-css', FB_PLUGIN_URL . '/css/fbc.css', false, '1.0', 'screen');
}
//add_action('wp_head', 'fbc_styles', 1);

/**
 * Add javascript file in head section
 *
 */

function fbc_scripts() {
	global $faqPluginUrl;
	wp_enqueue_script('facebook-connect-js', FB_PLUGIN_URL . '/js/fbc.js', array('jquery'), '1.0', true);
	if ( ! did_action ( 'wp_print_styles' ) )
		wp_print_styles ();
}
//add_action('wp_head', 'fbc_scripts', 8);



/**
 * Runs when PLugin is activate
 * Use to install custom tables and options
 */
function fbc_activate() {

}
register_activation_hook(__FILE__, 'fbc_activate');

/**
 * Runs when PLugin is uninstall
 * Use to uninstall custom tables and options
 */
function fbc_deactivate() {

}
register_deactivation_hook(__FILE__, 'fbc_deactivate');


/**
 * function to display configration settings for facebook connect api
 *
 */
function fbc_admin_menu() {

	add_menu_page("Arcgate Facebook Social Plugins", "Arcgate FBC", 1, 'facebook-connect/fbcAdminControls.php', array( 'FbcAdminControls', 'fbc_general_options'), plugins_url('fbconnect/images/facebook.png'));
	add_submenu_page('facebook-connect/fbcAdminControls.php', __('FBC options', 'facebook-connect'), __('FBC options', 'facebook-connect'), 1, 'facebook-connect/fbcAdminControls.php',array( 'FbcAdminControls', 'fbc_general_options'));

}

/**
 * function to register widget from admin panel
 * User can enable Recommendation, Activity, login widgets from admin panel
 * Set widgets to primary, secondary sidebars
 *
 */
function fbc_register_sidebar_widget() {
    register_sidebar_widget('Facebook Recommend', array('FbmlTags', 'fbc_recommendation_widget'));
		register_sidebar_widget('Facebook Activity', array('FbmlTags', 'fbc_activity_widget'));
		register_sidebar_widget('Facebook Login', array('FbmlTags', 'fbc_login_widget'));
}

/**
 * Login functionality Button
 * @param $echo boolean whether to return or display login button
 * @global $fbml_tags FbmlTags class object
 * @return mixed Facebook login button
 *
 */
function fbc_the_login_button($echo = TRUE) {
	global $fbml_tags;
	if($echo) :
		echo $fbml_tags->fbc_get_login_button();
	else :
		return $fbml_tags->fbc_get_login_button();
	endif;
}

/**
 * Login functionality Button
 * @param $echo boolean whether to return or display login button
 * @global $fbml_tags FbmlTags class object
 * @return mixed Facebook login button
 *
 */
function fbc_the_like_button($href, $echo = TRUE) {
	global $fbml_tags;
	if($echo) :
		echo $fbml_tags->fbc_get_like_button($href);
	else :
		return $fbml_tags->fbc_get_like_button($href);
	endif;
}

/**
 * Recommendation widget functionality
 * @param $echo boolean whether to return or display Recommendation
 * @global $fbml_tags FbmlTags class object
 * @return mixed Facebook recommendtion widget
 *
 */
function fbc_the_recommendation_block($echo = TRUE) {
	global $fbml_tags;
	if($echo) :
		echo $fbml_tags->fbc_get_recommendation_block();
	else :
		return $fbml_tags->fbc_get_recommendation_block();
	endif;
}

/**
 * Facebook Users Activity widget functionality
 * @param $echo boolean whether to return or display Activity block
 * @global $fbml_tags FbmlTags class object
 * @return mixed Facebook Users Activity
 *
 */
function fbc_the_activity_block($echo = TRUE) {
	global $fbml_tags;
	if($echo) :
		echo $fbml_tags->fbc_get_activity_block();
	else :
		return $fbml_tags->fbc_get_activity_block();
	endif;
}



/**
 * Facebook Users Comments widget functionality
 * @param $echo boolean whether to return or display comments section
 * @global $fbml_tags FbmlTags class object
 * @return mixed Facebook Users comments block
 *
 */
function fbc_the_comments_block($echo = TRUE) {
	global $fbml_tags;
	if($echo) :
		echo $fbml_tags->fbc_get_comments_block();
	else :
		return $fbml_tags->fbc_get_comments_block();
	endif;
}

/**
 * Facebook Users livestream widget functionality
 * @param $echo boolean whether to return or display livestream block
 * @global $fbml_tags FbmlTags class object
 * @return mixed Facebook Users livestream block
 *
 */
function fbc_the_live_stream_block($echo = TRUE) {
	global $fbml_tags;
	if($echo) :
		echo $fbml_tags->fbc_get_live_stream_block();
	else :
		return $fbml_tags->fbc_get_live_stream_block();
	endif;
}


/**
 * Return user Facebook Cookie Information
 *
 * @return array Facebook Users Cookie
 *
 */
function fbc_get_user_cookie() {
	$fb_cookie = '';
	if( isset($_COOKIE['fbs_'.FB_APPID]) && $_COOKIE['fbs_'.FB_APPID] !=''){
		global $fbcuser;
		$fb_cookie = $fbcuser->fbc_get_user_cookie();
	}
	return $fb_cookie;
}


/**
 * Pick all info of user from facebook and then create a wp auth user
 *
 * @method FbUser::fbc_get_loggedin_user()
 * @global $fbcuser object FbUser class object
 */
function fbc_set_user_information() {
	global $fbcuser;
	if($_GET['fb_login'] == 'yes'){
		$fb_cookie = (array) fbc_get_user_cookie() ;
		if(!is_user_logged_in()) {
			if( isset($fb_cookie['access_token']) ) :
				$user = new stdClass();
				$user = $fbcuser->fbc_get_loggedin_user($fb_cookie['access_token']);
				if($user) :
					$fbcuser->fbc_set_user_info($user);
				endif;
			endif;
		} else {
			$user= wp_get_current_user();
			update_usermeta($user->ID, 'wp_facebook_id' , $fb_cookie['uid']);
			update_usermeta($user->ID, 'wp_facebook_link' , $fb_user->link );
			update_usermeta($user->ID, 'wp_facebook_access_token' , $fb_cookie['access_token'] );
		}
	?>
		<script>window.location.href="<?php echo get_bloginfo('url');?>"</script>
	<?

	}
	if($_GET['fb_logout'] == 'yes') {
		wp_logout();
	?>
		<script>window.location.href="<?php echo get_bloginfo('url');?>"</script>
	<?
	}

}


add_action("plugins_loaded", "fbc_register_sidebar_widget");
add_action("init", "fbc_set_user_information");
add_action('admin_menu', 'fbc_admin_menu');
add_action('wp_footer', array('FbmlTags','fbc_fbml_js'));
add_action('the_content', array('FbmlTags','fbc_the_fshare_content') );
add_filter('language_attributes', array('FbmlTags', 'fbc_set_xfbml_tag'));
add_filter('loginout', array('FbmlTags', 'fbc_logout'));
//remove_filter('loginout', array('FbmlTags', 'fbc_get_login_button'));
