<?php
/*
Plugin Name: Bird Feeder
Plugin URI: http://andrewjaswa.com/bird-feeder
Description: Tweets your published posts to twitter.
Version: 1.2.2
Author: Andrew Jaswa
Author URI: http://andrewjaswa.com
*/
/*
Copyright 2008  Andrew Jaswa  (email : ajaswa@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('admin_menu', '_aj_bf_add_admin',1);
add_action('admin_head', '_aj_bf_add_styles',13);

function _aj_birdfeeder($post_ID){
	if($_POST['post_type'] != "page"){
		$options = get_option("aj_bf_options");

		if( isset( $options->username, $options->password ) && $options->username != '' && $options->password != '' ){
			$url = "http://twitter.com/statuses/update.json?source=birdfeeder";
			$postTitle = get_post($post_ID);
			$title = $postTitle->post_title;
			if ( isset($options->url) && $options->url !=''){
				$postUrl = get_bloginfo('url');
			}else {
				$postUrl = get_permalink($post_ID);
			}

			if ( isset($options->preview) && $options->preview !=''){
				$shortnameUrl = "http://shortna.me/v/";
			}else {
				$shortnameUrl = "http://shortna.me/";
			}

			if (function_exists('curl_init') && $postUrl) {
				$encodedUrl = urlencode($postUrl);
				$getUrl = "http://shortna.me/api/birdfeeder-".$options->username."/shorten?hashonly=1&url=".$encodedUrl;
				$session = curl_init($getUrl);
				curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($session, CURLOPT_FAILONERROR, true);
				curl_setopt($session, CURLOPT_GET, true);
				curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 20);
				curl_setopt($session, CURLOPT_TIMEOUT, 20);
				$shortnameHash = curl_exec($session);
				curl_close($session);

//				if(stristr($shortnameHash, 'ERROR') === FALSE || (isset($shortnameHash) && $shortnameHash != '')) {

				if ($shortnameHash != '' && stripos($shortnameHash, 'ERROR') === FALSE) {
					$shortUrl = $shortnameUrl . $shortnameHash;
				}else {
					$shortUrl = $postUrl;
				}
			}

			$twitterLen = 120;
			$messageLen = strlen($options->message);
			$urlLen = strlen($shortUrl);
			$titleLen = strlen($title);
			$charLen = $urlLen + $messageLen + $titleLen;

			if ($charLen > $twitterLen){
				$subtract = $titleLen - ($charLen - $twitterLen);
				$shortTitle = substr($title, 0, $subtract);
				$title = $shortTitle . "...";
			}

			$curlPost = "status=". $options->message . " " . $title .	 " " . $shortUrl;
			if (function_exists('curl_init') && $url) {
				$up = $options->username .":". $options->password;
				$session = curl_init($url);
				curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($session, CURLOPT_FAILONERROR, true);
				curl_setopt($session, CURLOPT_POST, true);
				curl_setopt($session, CURLOPT_USERPWD, $up);
				curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt($session, CURLOPT_TIMEOUT, 10);
				curl_setopt($session, CURLOPT_POSTFIELDS, $curlPost); 
				$stream = curl_exec($session);
				curl_close($session);
			}
		}
	}
}

function _aj_bf_add_admin() {
	if (function_exists('add_options_page')) {
		add_options_page('Bird Feeder', 'Bird Feeder', 8, 'bird-feeder', '_aj_bf_admin_page');
	}
}
function _aj_bf_add_styles() {
	if ($_GET['page'] == 'bird-feeder') {
		$stylePath = get_settings('siteurl');
		echo '<link rel="stylesheet" type="text/css" href="' . $stylePath . '/wp-content/plugins/bird-feeder/bird-feeder.css" />';
	}
}

function _aj_bf_admin_page() {
?>
<div id="bird-feeder" class="wrap"><h2>Bird Feeder</h2>
<?php
	if (!function_exists('curl_init')) {
?>
			<div class="error">
				<p>This plugin requires that cURL be installed on your server and available via PHP. One or both of these appear to be lacking.  Sorry!</p>
			</div>
<?php
	} else {
		if (isset($_POST['update'])) {
			if ($_POST['user']) $options->username = $_POST['user'];
			if ($_POST['password']) $options->password = $_POST['password'];
			if ($_POST['message']) $options->message = $_POST['message'];
			if ($_POST['url']) $options->url = $_POST['url'];
			if ($_POST['preview']) $options->preview = $_POST['preview'];
			update_option("aj_bf_options",$options);
			echo '<p id="message" class="updated">Options updated.</p>';
			$options = get_option("aj_bf_options");
			
		}
		$options = get_option("aj_bf_options");
?>

<form method="post">
	<fieldset class="options">
		<legend><?php _e('Options') ?></legend>
		<label for="user"><?php _e('Your Twitter user'); ?></label>
		<input type="text" class="text" name="user" id="user" value="<?php echo $options->username; ?>" /> <?php if ($options->username) {echo '<small>(<a href="http://twitter.com/' . $options->username . '">twitter.com/' . $options->username . '</a>)</small>';} ?>

		<label for="password"><?php _e('Your Twitter password'); ?></label>
		<input type="password" class="text" name="password" id="password" value="<?php echo $options->password; ?>" />

		<label for="message"><?php _e('Message'); ?></label>
		<textarea id="message" class="text" name="message"><?php echo $options->message; ?></textarea>

		<label for="url"><?php _e('Use blog URL (rather than post URL)'); ?></label>
		<input type="checkbox" class="checkbox" id="url" name="url" value="url" <?php if ( isset($options->url) && $options->url !=''){?>checked="checked"<?php }?> />

		<label for="preview"><?php _e('Use preview url'); ?></label>
		<input type="checkbox" class="checkbox" id="preview" name="preview" value="preview" <?php if ( isset($options->preview) && $options->preview !=''){?>checked="checked"<?php }?> />

		<p class="submit">
			<input type="submit" name="update" id="update" value="<?php _e('Update') ?>" />
		</p>
	</fieldset>

</form>
<p>Your tweet will look something like this:</p>
<p>[your_message] [title_of_blog_post] [short_url]</p>
</div>

<?php
	}

}
add_action('new_to_publish', '_aj_birdfeeder');
add_action('draft_to_publish', '_aj_birdfeeder');
add_action('pending_to_publish', '_aj_birdfeeder');
// add_action('publish_post', '_aj_birdfeeder');
add_action('future_to_publish', '_aj_birdfeeder');
?>