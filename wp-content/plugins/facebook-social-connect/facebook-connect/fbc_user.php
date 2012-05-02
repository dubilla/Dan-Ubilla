<?php

/**
 * @author Anuj Chauhan<anuj@arcgate.com>
 *
 *
 */

class FbUser {

	private $fb_appid;
	private $fb_appkey;
	private $fb_appsecret;

	function __construct() {
		$this->fb_appid = FB_APPID;
		$this->fb_appkey = FB_APP_KEY;
		$this->fb_appsecret = FB_APP_SECRET;
	}

/**
 * Function to get user detail from facebook
 *
 * @uses Facebook Graph Api
 *
 * @param $access_token string Access token of FB User
 *
 * @return mixed user object or false
 */
	public function fbc_get_loggedin_user($access_token) {
		if($access_token) :
			$user = json_decode(file_get_contents('https://graph.facebook.com/me?access_token='.$access_token));
			//pr($user);
			return $user;
		endif;
		return False;
	}

/**
 * Function to create WP user
 * checks if user is already a member for Wp and facebook
 * If not then create a user otherwise return
 * @method fbc_create_wp_user()
 * @param $fb_user object  FB User
 *
 * @return boolean TRUE
 */
	public function fbc_set_user_info($fb_user) {
		global $wpdb;
		if(!empty($fb_user)) {
			$query = $wpdb->prepare("Select ID, user_login, user_email from wp_users where user_email = '%s'", $fb_user->email);
			$wp_user = $wpdb->get_results( $query );
			if(empty($wp_user) && !is_user_logged_in() ) {
					$this->fbc_create_wp_user($fb_user);
			} else {
				if(is_user_logged_in()):
					$user = wp_get_current_user();
					$user_id = $user->ID;
				else:
					$user_id = 	$wp_user[0]->ID;
					$userObj = wp_signon(
						array(
							'user_login' => $wp_user[0]->user_login,
							'user_password' => $wp_user[0]->user_login,
						)
					);
				endif;
				if($user_id) {
					update_usermeta($user_id, 'wp_facebook_id' , $fb_user->id );
					update_usermeta($user_id, 'wp_facebook_link' , $fb_user->link );
					update_usermeta($user_id, 'wp_facebook_access_token' , $fb_user->access_token );
				}
				
			}
		}
		return TRUE;
	}

/**
 * Function to create WP User and logged in to site
 *
 * @uses Facebook Graph Api
 *
 * @param $access_token string Access token of FB User
 *
 * @return mixed user object or false
 */
	private function fbc_create_wp_user($fb_user) {
		global $wpdb;

		$email = sanitize_email( $fb_user->email );
		$user = get_user_by_email($email);
		if(!$user) {
					$password = "fb_". $fb_user->id;
					$username = "fb_". $fb_user->id;

			// Generate an activation key
			$key = substr( md5( time() . rand() . $user_email ), 0, 16 );

			// Prepare the metadata
			$meta = serialize( array( 'first_name' => $fb_user->first_name, 'last_name' => $fb_user->last_name ) );


			$result = $wpdb->insert( $wpdb->signups, array(
				'domain' => '',
				'path' => '',
				'title' => '',
				'user_login' => $username,
				'user_email' => $email,
				'registered' => current_time( 'mysql', true ),
				'activation_key' => $key,
				'meta' => $meta,
			) );
			if($result) {
				$wp_userid = $this->fbc_activate_user($username, $password);
				if( !$wp_userid )
					throw new Exception('Could not create user.');

				update_usermeta($wp_userid, 'wp_facebook_id' , $fb_user->id );
				update_usermeta($wp_userid, 'wp_facebook_link' , $fb_user->link );
				update_usermeta($wp_userid, 'wp_facebook_access_token' , $fb_user->access_token );
				update_usermeta($wp_userid, 'first_name', $fb_user->first_name);
				update_usermeta($wp_userid, 'last_name', $fb_user->last_name);
				// Create the user
				$userObj = wp_signon(
					array(
						'user_login' => $username,
						'user_password' => $password,
					)
				);

				if ( !$userObj )
					throw new Exception('Could not create user.');

				$userObj->set_role('subscriber');
				//wp_new_user_notification($user_id, $user_pass);
			}
		}

		return TRUE;
	}

/**
 * Function to get Facebook user cookie information
 *
 * @global $args Array
 *
 * @return mixed user object or false
 */

	public function fbc_get_user_cookie() {
		$args = array();
		$fb_cookie = $_COOKIE['fbs_' . $this->fb_appid];
		if(isset($fb_cookie)):
			parse_str(trim($fb_cookie, '\\"'), $args);
			ksort($args);
			$payload = '';
			foreach ($args as $key => $value) {
					if ($key != 'sig') {
						$payload .= $key . '=' . $value;
					}
			}
			if (md5($payload . $this->fb_appsecret) != $args['sig']) {
					return null;
			}
		endif;
		return $args;
	}

/**
 * Function to activate wp user
 *
 * @global $wpdb object database query object
 *
 * @return int user id
 */
	private function fbc_activate_user($username, $password) {
		global $wpdb;
		$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE user_login = %s limit 1", $username) );
		$meta = unserialize($signup->meta);
		$user_login = $wpdb->escape($signup->user_login);
		$user_email = $wpdb->escape($signup->user_email);
		$password = $password;
		$userdata = compact('user_login', 'user_email', 'user_pass');
		return $this->nytn_create_user($user_login, $password, $user_email);
	}

	private function nytn_create_user( $user_name, $password, $email) {
	$user_name = preg_replace( "/\s+/", '', sanitize_user( $user_name, true ) );
	if ( $this->username_exists($user_name) )
		return false;

	// Check if the email address has been used already.
	if ( $this->email_exists($email) )
		return false;

	$user_id = $this->wp_create_user( $user_name, $password, $email );
	$user = new WP_User($user_id);

	// Newly created users have no roles or caps until they are added to a blog.
	update_user_option($user_id, 'capabilities', '');
	update_user_option($user_id, 'user_level', '');

	do_action( 'wpmu_new_user', $user_id );

	return $user_id;
	}

	function username_exists( $username ) {
		if ( $user = get_userdatabylogin( $username ) ) {
			return $user->ID;
		} else {
			return null;
		}
	}

	function email_exists( $email ) {
		if ( $user = get_user_by_email($email) )
			return $user->ID;

		return false;
	}

	function wp_create_user($username, $password, $email = '') {
		$user_login = esc_sql( $username );
		$user_email = esc_sql( $email    );
		$user_pass = $password;

		$userdata = compact('user_login', 'user_email', 'user_pass');
		return $this->wp_insert_user($userdata);
	}

	function wp_insert_user($userdata) {
		global $wpdb;
		extract($userdata, EXTR_SKIP);

		// Are we updating or creating?
		if ( !empty($ID) ) {
			$ID = (int) $ID;
			$update = true;
			$old_user_data = get_userdata($ID);
		} else {
			$update = false;
			// Hash the password
			$user_pass = wp_hash_password($user_pass);
		}

		$user_login = sanitize_user($user_login, true);
		$user_login = apply_filters('pre_user_login', $user_login);

		if ( empty($user_nicename) )
			$user_nicename = sanitize_title( $user_login );
		$user_nicename = apply_filters('pre_user_nicename', $user_nicename);

		if ( empty($user_url) )
			$user_url = '';
		$user_url = apply_filters('pre_user_url', $user_url);

		if ( empty($user_email) )
			$user_email = '';
		$user_email = apply_filters('pre_user_email', $user_email);

		if ( empty($display_name) )
			$display_name = $user_login;
		$display_name = apply_filters('pre_user_display_name', $display_name);

		if ( empty($nickname) )
			$nickname = $user_login;
		$nickname = apply_filters('pre_user_nickname', $nickname);

		if ( empty($first_name) )
			$first_name = '';
		$first_name = apply_filters('pre_user_first_name', $first_name);

		if ( empty($last_name) )
			$last_name = '';
		$last_name = apply_filters('pre_user_last_name', $last_name);

		if ( empty($description) )
			$description = '';
		$description = apply_filters('pre_user_description', $description);

		if ( empty($rich_editing) )
			$rich_editing = 'true';

		if ( empty($comment_shortcuts) )
			$comment_shortcuts = 'false';

		if ( empty($admin_color) )
			$admin_color = 'fresh';
		$admin_color = preg_replace('|[^a-z0-9 _.\-@]|i', '', $admin_color);

		if ( empty($use_ssl) )
			$use_ssl = 0;

		if ( empty($user_registered) )
			$user_registered = gmdate('Y-m-d H:i:s');

		$user_nicename_check = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_nicename = %s AND user_login != %s LIMIT 1" , $user_nicename, $user_login));

		if ( $user_nicename_check ) {
			$suffix = 2;
			while ($user_nicename_check) {
				$alt_user_nicename = $user_nicename . "-$suffix";
				$user_nicename_check = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_nicename = %s AND user_login != %s LIMIT 1" , $alt_user_nicename, $user_login));
				$suffix++;
			}
			$user_nicename = $alt_user_nicename;
		}

		$data = compact( 'user_pass', 'user_email', 'user_url', 'user_nicename', 'display_name', 'user_registered' );
		$data = stripslashes_deep( $data );

		if ( $update ) {
			$wpdb->update( $wpdb->users, $data, compact( 'ID' ) );
			$user_id = (int) $ID;
		} else {
			$wpdb->insert( $wpdb->users, $data + compact( 'user_login' ) );
			$user_id = (int) $wpdb->insert_id;
		}

		update_usermeta( $user_id, 'first_name', $first_name);
		update_usermeta( $user_id, 'last_name', $last_name);
		update_usermeta( $user_id, 'nickname', $nickname );
		update_usermeta( $user_id, 'description', $description );
		update_usermeta( $user_id, 'rich_editing', $rich_editing);
		update_usermeta( $user_id, 'comment_shortcuts', $comment_shortcuts);
		update_usermeta( $user_id, 'admin_color', $admin_color);
		update_usermeta( $user_id, 'use_ssl', $use_ssl);



		if ( isset($role) ) {
			$user = new WP_User($user_id);
			$user->set_role($role);
		} elseif ( !$update ) {
			$user = new WP_User($user_id);
			$user->set_role(get_option('default_role'));
		}

		wp_cache_delete($user_id, 'users');
		wp_cache_delete($user_login, 'userlogins');

		if ( $update )
			do_action('profile_update', $user_id, $old_user_data);
		else
			do_action('user_register', $user_id);

		return $user_id;
	}

}



// create object
$fbcuser = new FbUser();
?>