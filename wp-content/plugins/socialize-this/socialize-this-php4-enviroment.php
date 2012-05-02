<?php
# This is the PHP 4 enviroment for Socialize This. PHP4 isn't as good a PHP5 but I still have to support it :(
$st_url = '';
$st_imgs = '';
class socialize_this {
	
	var $st_url;
	var $st_imgs;
	
	// __construct() - starts adding the admin menus
	function __construct(){
		global $st_imgs, $st_url;
		
		if(get_option('st_current_version') != ST_VERSION){
			$this->st_uninstall(TRUE);
			$this->st_install(TRUE);
		}
		
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('publish_post', array($this, 'admin_submit_post'), '10', 1);
		if(get_option('st_include_in_posts') === 'yes'){
			add_filter('comments_template', array($this, 'show_social_widgets'));
		}
		if(get_option('st_extend_php_limits') == TRUE){
			ini_set('memory_limit','128M');
			ini_set('max_execution_time','60');	
		}
		add_shortcode('socializethis', array($this, 'show_social_widgets'));
		$st_url = WP_PLUGIN_URL.'/socialize-this';
		
		$st_imgs = unserialize(get_option('st_imgs'));
	}
	
	// admin_menu() - add's the admin menus
	function admin_menu(){
		global $st_imgs, $st_url;
		$plugin_page = add_submenu_page('options-general.php', 'Socialize This', 'Socialize This', 10, ST_FILE, array($this, 'socialize_this_settings'));
		add_action( 'admin_head-'. $plugin_page, array($this, 'st_admin_jquery_css'), 10, ST_FILE, 'socialize-this');
	}
	
	function extra_plugin_links($links, $file) {
		if($file == ST_FILE){
			$links[] = '<a href="options-general.php?page=socialize-this/socialize-this.php">' . __('Settings', 'st_plugin') . '</a>';
			$links[] = '<a href="options-general.php?page=socialize-this/socialize-this.php&module=social_widgets">' . __('Social Widgets', 'st_plugin') . '</a>';
			$links[] = '<a href="options-general.php?page=socialize-this/socialize-this.php&module=templates">' . __('Templates', 'st_plugin') . '</a>';
		}
		return $links;
	}
	
	function st_admin_jquery_css(){
		global $st_imgs, $st_url;
		echo '<link rel="stylesheet" type="text/css" href="'.$st_url.'/wp-admin.css'.'" />';
		if($_GET['module'] == 'social_widgets'){
			echo '<script type="text/javascript" src="'.$st_url.'/jquery-1.3.2.min.js"></script><script type="text/javascript" src="'.$st_url.'/jquery-ui-1.7.2.custom.min.js"></script>';?>
<script type="text/javascript">
//<![CDATA[ 
function click_unclick(name){
	widget_image = document.getElementById('widget_image_'+name);
	widget_tickbox = document.getElementById('widget_'+name);
	if(widget_tickbox.checked == true){
		widget_image.className = 'ticked';
	} else {
		widget_image.className = 'unticked';
	}
	return true;
}
$(document).ready(function(){ 
	$("#widgetOrder ul").sortable({ opacity: 0.6, cursor: 'move', update: function() {
		$("#widget_order_form").val($(this).sortable('toArray'));
	}});
});
//]]> 
</script>
            <?php
		}
	}
	
	// st_adm_header() - echos the Header HTML
	function st_adm_header(){
		global $st_imgs, $st_url;
		?>
<div id="icon-options-general" class="icon32"><br />
</div>
<h2>
  <?php _e('Socialize This', 'st_plugin'); ?>
  (1.6.2)
</h2>
<div id="st_top_navagation"><a href="?page=socialize-this/socialize-this.php"><?php _e('Overview', 'st_plugin'); ?></a> | <a href="?page=socialize-this/socialize-this.php&module=social_widgets"><?php _e('Social Widgets', 'st_plugin'); ?></a> | <a href="?page=socialize-this/socialize-this.php&module=templates"><?php _e('Templates', 'st_plugin'); ?></a> | <a href="?page=socialize-this/socialize-this.php&module=advanced_functions"><?php _e('Advanced Functions', 'st_plugin'); ?></a> | <a href="?page=socialize-this/socialize-this.php&module=credits"><?php _e('Credits', 'st_plugin'); ?></a></div>
<?php
	}
	function st_adm_footer(){
		global $st_imgs, $st_url;
		// I may add a footer in the future. 
	}
	
	function st_adm_donate(){
		global $st_imgs, $st_url;
		if(get_option('st_hide_donate') != TRUE){
			echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post"  target="_blank">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="10834919">
<p class="submit"><input type="submit" name="submit" value="Support Socialize This" class="button-primary"></p>
</form>
';
		}
	}
	
	// st_adm_notice() - If their is a notice...display it!
	function st_adm_notice($note=''){
		global $st_imgs, $st_url;
		if($note != ''){
			echo '<div class="message">'.$note.'</div>';
		}
	}
	
	function st_adm_general(){
		global $st_imgs, $st_url;
		$note = NULL;
		if($_POST['Submit'] === 'Save Settings'){
			if($_POST['include_in_posts'] === 'yes'){
				update_option('st_include_in_posts', 'yes');
			} else {
				update_option('st_include_in_posts', 'no');
			}
			

			update_option('st_max_widgets_until_br', $_POST['max_widgets_until_br']);
			update_option('st_noti_twitter', $_POST['st_noti_twitter']);
			update_option('st_noti_twitter_user', $_POST['st_noti_twitter_user']);
			update_option('st_noti_twitter_pass', stripslashes($_POST['st_noti_twitter_pass']));
			update_option('st_url_shortening_service', $_POST['st_url_shortening_service']);
			
			$note .= '<p>Settings Updated</p>';
		}
		$this->st_adm_notice($note);
		?>
    <h4>
    <?php _e('General Settings', 'st_plugin');?>
    
  </h4>
  <?php 
  if(!function_exists('curl_init')) { ?>
  <div class="message error"><?php _e('Note: Your server configuration does not allow cURL, people enable it to enable twitter updates.', 'st_plugin'); ?></div>
  <?php } ?>
  <form action="" method="post" enctype="application/x-www-form-urlencoded">
    <table width="90%" border="0" class="widefat post fixed">
    <thead>
    <tr>
    <th width="40%" class="manage-column"><?php _e('Setting Description', 'st_plugin'); ?></th>
    <th width="60%" class="manage-column">&nbsp;</th>
    </tr>
    </thead>
      <tr>
        <td width="40%"><?php _e('Include Soical widgets before the comments template (normally after posts)', 'st_plugin'); ?>
          <br>
          <em>(
          <?php _e('Tick for yes', 'st_plugin'); ?>
          )</em></td>
        <td width="60%"><label>
            <input type="checkbox" name="include_in_posts" id="checkbox" value="yes" <?php if(get_option('st_include_in_posts') === 'yes'){?> checked="checked" <?php } ?>>
            <em>
            <?php _e('You can manually include the social widgets using the following code:', 'st_plugin'); ?>
          </em></label><br />
<em>
          &lt;?php show_social_widgets(); ?&gt;</em></td>
      </tr>
      <tr>
        <td width="40%"><?php _e('Amount of widgets until a', 'st_plugin'); ?>
          &lt;br /&gt;<br />
          <em>
          <?php _e('For changing values for each line, sperate each value with a comma.', 'st_plugin'); ?>
          </em></td>
        <td width="60%"><label>
            <input type="text" name="max_widgets_until_br" value="<?php echo get_option('st_max_widgets_until_br'); ?>" />
          </label></td>
      </tr>
      <tr>
        <td width="40%"><?php _e('URL Shortening Service', 'st_plugin'); ?></td>
        <td width="60%"><label>
            <select name="st_url_shortening_service">
            <option value="tinyurl"<?php if(get_option('st_url_shortening_service') == 'tinyurl'){echo ' selected="selected"';}?>>TinyURL</option>
            <option value="urly"<?php if(get_option('st_url_shortening_service') == 'urly'){echo ' selected="selected"';}?>>ur.ly</option>
            <option value="isgd"<?php if(get_option('st_url_shortening_service') == 'isgd'){echo ' selected="selected"';}?>>is.gd</option>
            <option value="trim"<?php if(get_option('st_url_shortening_service') == 'trim' || get_option('st_url_shortening_service') == ''){echo ' selected="selected"';}?>>tr.im</option>
            <option value="klam"<?php if(get_option('st_url_shortening_service') == 'klam'){echo ' selected="selected"';}?>>kl.am</option>
            <option value="unu"<?php if(get_option('st_url_shortening_service') == 'unu'){echo ' selected="selected"';}?>>u.nu</option>
            </select>
          </label></td>
      </tr>
      <tr>
        <td width="40%"><?php _e('Notify Twitter when post is Published.', 'st_plugin'); ?>
          <br />
          <em>
          <?php _e('Your twitter username and password are required if this featured in enabled. This also requires cURL.', 'st_plugin'); ?>
          </em></td>
        <td width="60%"><label>
            <input type="checkbox" name="st_noti_twitter" id="checkbox" value="yes" <?php if(get_option('st_noti_twitter') === 'yes'){?> checked="checked" <?php } ?>>
          </label>
          <br />
          <label>
            <?php _e('Username:', 'st_plugin'); ?>
            <input type="text" name="st_noti_twitter_user" value="<?php echo get_option('st_noti_twitter_user'); ?>" />
          </label>
          <br />
          <label>
            <?php _e('Password:', 'st_plugin'); ?>
            <input type="password" name="st_noti_twitter_pass" value="<?php echo get_option('st_noti_twitter_pass'); ?>" />
          </label></td>
      </tr>
    </table>
    <p class="submit">
      <input type="submit" name="Submit" class="button-primary" value="Save Settings" />
    </p>
  </form>
    <?php 
	$this->st_adm_donate();
	}
	
	function st_get_widget_sets(){
		global $st_imgs, $st_url;
		# taken from http://www.laughing-buddha.net/jon/php/dirlist/
		$handler = opendir(ST_FOLER); // Open the folder and look for widget sets.
		while ($file = readdir($handler)) {
			// if $file isn't this directory or its parent, 
			// add it to the results array
			if ($file != '.' && $file != '..'){
				if(preg_match('#.stset.xml#is', $file)){
					$results[] = $file;
				}
			}
		}
		
		// tidy up: close the handler
		closedir($handler);
		unset($handler, $file);
		
		return $results;
	}
	
	function st_adm_social_widgets(){
		global $st_imgs, $st_url;
		$note = NULL;
		if($_POST['Submit'] === 'Save Widgets'){
			// Update the order.
			$widget_order = explode(',', $_POST['widget_order']);
			//var_dump($widget_order);
			update_option('st_widget_order', serialize($widget_order));
			
			foreach($st_imgs as $key => $image){
				//echo $key;
				if($_POST['widget_'.$image['name']] === $key){
					update_option('st_'.$key, 'use');
				} else {
					update_option('st_'.$key, 'dontuse');
				}
			}
			$note .= '<p>widgets Updated</p>';
		}
		
		
		$this->st_adm_notice($note);
		?>
  <form action="" method="post" enctype="application/x-www-form-urlencoded">
    <h4>
      <?php _e('Social Widgets (Click to enable/disable)', 'st_plugin'); ?>
    </h4>
    <div id="widgetOrder">
      <ul>
        <?php 
	  //asort($st_imgs);
	  
	  $widget_order = unserialize(get_option('st_widget_order'));
	 // var_dump($widget_order);
	  foreach($widget_order as $key){
	  
	  $image = $st_imgs[$key];
  ?>
        <li id="<?php echo $key; ?>" class="widget_box">
          <label for="widget_<?php echo $image['name']; ?>" onClick="click_unclick('<?php echo $image['name']; ?>')"> <img src="<?php echo $st_url.'/img/'.$image['file']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" id="widget_image_<?php echo $image['name']; ?>" title="<?php echo $image['title']; ?>" class="<?php if(get_option('st_'.$key) !== 'use'){ echo 'un'; } ?>ticked" />
            <!--<?php echo $image['title']; ?>-->
            <br />
            <input type="checkbox" <?php if(get_option('st_'.$key) === 'use'){ echo 'checked'; } ?> name="widget_<?php echo $image['name']; ?>" id="widget_<?php echo $image['name']; ?>" value="<?php echo $key; ?>">
          </label>
        </li>
        <?php 
		 	 if($widget_order_commas == true){
		  		$widget_order_commas .= ','.$key;
		 	 } else {
				$widget_order_commas = $key;  
		 	 }
		  } ?>
      </ul>
    </div>
    <p class="submit">
      <input type="hidden" name="widget_order" value="<?php echo $widget_order_commas; ?>" id="widget_order_form" />
      <input type="submit" name="Submit" class="button-primary" value="Save Widgets" />
    </p>
  </form>
	<?php }
	
	function st_adm_templates(){
		global $st_imgs, $st_url;
		$note = NULL;
		if($_POST['Submit'] === 'Save Templates'){
			foreach($st_imgs as $key => $image){
				update_option('st_template_'.$image['template'], stripslashes($_POST['st_template_'.$image['template']]));
			}
			update_option('st_template_twitter_noti', $_POST['st_template_twitter_noti']);
			$note .= '<p>Templated Updated</p>';
		}
		$this->st_adm_notice($note);
		?>
          <form action="" method="post" enctype="application/x-www-form-urlencoded">
    <h3>
      <?php _e('Templates', 'st_plugin'); ?>
    </h3>
    <p>
      <?php _e('This section allows you to edit the templates related to the widgets.', 'st_plugin'); ?>
    </p>
    <table border="0" class="widefat post fixed">
    <thead>
    <tr>
    <th width="25%" class="manage-column"><?php _e('Website', 'st_plugin'); ?></th>
    <th width="75%" class="manage-column"><?php _e('Code', 'st_plugin'); ?></th>
    </tr>
    </thead>
      <tr>
        <td width="25%" valign="top"><a href="http://www.designbump.com/">Design Bump</a></td>
        <td width="75%"><textarea name="st_template_bump" cols="90%" rows="2"><?php echo get_option('st_template_bump'); ?></textarea></td>
      </tr>
      <tr>
        <td valign="top"><a href="http://delicious.com/">Delicious</a></td>
        <td><textarea name="st_template_del" cols="90%" rows="2"><?php echo get_option('st_template_del'); ?></textarea></td>
      </tr>
      <tr>
        <td valign="top"><a href="http://digg.com/">Digg</a></td>
        <td><textarea name="st_template_digg" cols="90%" rows="2"><?php echo get_option('st_template_digg'); ?></textarea></td>
      </tr>
      <tr>
        <td valign="top"><a href="http://www.facebook.com/">Facebook</a></td>
        <td><textarea name="st_template_facebook" cols="90%" rows="2"><?php echo get_option('st_template_facebook'); ?></textarea></td>
      </tr>
      <tr>
        <td valign="top"><a href="http://www.designfloat.com/">Design Float</a></td>
        <td><textarea name="st_template_float" cols="90%" rows="2"><?php echo get_option('st_template_float'); ?></textarea></td>
      </tr>
      <tr>
        <td valign="top"><a href="http://www.mixx.com/">Mixx</a></td>
        <td><textarea name="st_template_mixx" cols="90%" rows="2"><?php echo get_option('st_template_mixx'); ?></textarea></td>
      </tr>
      <tr>
        <td valign="top"><a href="http://www.reddit.com/">Reddit</a></td>
        <td><textarea name="st_template_reddit" cols="90%" rows="2"><?php echo get_option('st_template_reddit'); ?></textarea></td>
      </tr>
      <tr>
        <td valign="top"><a href="<?php echo bloginfo_rss('rss2_url'); ?>">RSS Feed</a></td>
        <td><textarea name="st_template_rss" cols="90%" rows="2"><?php echo get_option('st_template_rss'); ?></textarea></td>
      </tr>
      <tr>
        <td valign="top"><a href="http://www.stumbleupon.com/">StumbleUpon</a></td>
        <td><textarea name="st_template_stumble" cols="90%" rows="2"><?php echo get_option('st_template_stumble'); ?></textarea></td>
      </tr>
      <tr>
        <td valign="top"><a href="http://technorati.com/">Technorati</a></td>
        <td><textarea name="st_template_tech" cols="90%" rows="2"><?php echo get_option('st_template_tech'); ?></textarea></td>
      </tr>
      <tr>
        <td valign="top"><a href="http://www.google.com/bookmarks/">Google</a></td>
        <td><textarea name="st_template_google" cols="90%" rows="2"><?php echo get_option('st_template_google'); ?></textarea></td>
      </tr>
      <tr>
        <td valign="top"><a href="http://www.linkedin.com/shareArticle">LinkedIn</a></td>
        <td><textarea name="st_template_linkedin" cols="90%" rows="2"><?php echo get_option('st_template_linkedin'); ?></textarea></td>
      </tr>
      <tr>
        <td valign="top"><a href="http://twitter.com/">Twitter</a></td>
        <td><textarea name="st_template_twitter" cols="90%" rows="2"><?php echo get_option('st_template_twitter'); ?></textarea></td>
      </tr>
      <tr>
        <td valign="top"><?php _e('Extra bit of HTML', 'st_plugin'); ?></td>
        <td><textarea name="st_template_html" cols="90%" rows="2"><?php echo get_option('st_template_html'); ?></textarea></td>
      </tr>
      <tr>
        <td valign="top"><a href="http://twitter.com/">Twitter<br />
          <?php _e('Post Notification Status Update', 'st_plugin'); ?>
          </a><br />
          <em>
          <?php _e('Note: URL encoding will not take place for variables.', 'st_plugin'); ?>
          </em></td>
        <td><textarea name="st_template_twitter_noti" cols="90%" rows="2"><?php echo get_option('st_template_twitter_noti'); ?></textarea></td>
      </tr>
    </table>
    <p class="submit">
      <input type="submit" name="Submit" class="button-primary" value="Save Templates" />
    </p>
    <h4>
      <?php _e('Variable Legend', 'st_plugin'); ?>
    </h4>
    <table border="0"  class="widefat post fixed">
    <thead>
    <tr>
    <th width="30%" class="manage-column"><?php _e('Tag', 'st_plugin'); ?></th>
    <th width="70%" class="manage-column"><?php _e('Description', 'st_plugin'); ?></th>
    </tr>
    </thead>
      <tr>
        <td width="30%">%permalink%</td>
        <td width="70%"><?php _e('The permalink to the post (URL Encoded)', 'st_plugin'); ?></td>
      </tr>
      <tr>
        <td>%title%</td>
        <td><?php _e('The title of the post (URL Encoded)', 'st_plugin'); ?></td>
      </tr>
      <tr>
        <td>%descirption%</td>
        <td><?php _e('The Excerpt of the post. If the Except does not exist, the title will double up as the descirption. (URL Encoded)', 'st_plugin'); ?></td>
      </tr>
      <tr>
        <td>%widget_image%</td>
        <td><?php _e('The HTML for the image for the widget.', 'st_plugin'); ?></td>
      </tr>
      <tr>
        <td>%widget_url%</td>
        <td><?php _e('The image url for the widget.', 'st_plugin'); ?></td>
      </tr>
      <tr>
        <td>%widget_alt%</td>
        <td><?php _e('The alternative text for the widget.', 'st_plugin'); ?></td>
      </tr>
      <tr>
        <td>%short_url%</td>
        <td><?php _e('The shortened URL of your permalink. Generated on Publish, if short url is not present permalink will be used. (URL Encoded)', 'st_plugin'); ?></td>
      </tr>
      <tr>
        <td>%rss_url%</td>
        <td><?php _e('Your RSS URL', 'st_plugin'); ?></td>
      </tr>
    </table>
  </form>
        <?php
	}
	function st_adm_credits(){
		global $st_imgs, $st_url;
		?>
        <h3>
    <?php _e('Credits', 'st_plugin'); ?>
  </h3>
  <p>
    <?php _e('Coder', 'st_plugin'); ?>
    : <a href="http://www.fullondesign.co.uk/" target="_blank">Mike Rogers (Full On Design)</a> (
    <?php _e('For Support Visit', 'st_plugin');?>
    <a href="http://www.fullondesign.co.uk/socialize-this" target="_blank">Socialize This</a>)<br />
    <?php _e('Testing Server', 'st_plugin'); ?>
    : <a href="http://www.wampserver.com/" target="_blank">WAMP</a><br />
    <?php _e('Default Icons/Widgets', 'st_plugin'); ?>
    : <a href="http://alteredadvice.com/alteredicons-40-social-networking-icon-set/" target="_blank">Alteredicons 1.0 icon Set</a> &amp; <a href="http://www.iconspedia.com/pack/social-me-1467/" target="_blank">Social me icons</a></p>
    <?php echo $this->st_adm_donate(); ?>
        <?php
	}
	
	function st_adm_advanced_functions(){
		global $st_imgs, $st_url;
		if(isset($_POST['save_advanced_settings'])){
			if($_POST['st_hide_donate'] == 'yes'){update_option('st_hide_donate', TRUE);} else {update_option('st_hide_donate', FALSE);}
			if($_POST['st_extend_php_limits'] == 'yes'){update_option('st_extend_php_limits', TRUE);} else {update_option('st_extend_php_limits', FALSE);}
			$note .= 'Settings Updated';
		}
		if(isset($_POST['regenerate_urls'])){
			global $wpdb;
			$query = "SELECT ID from $wpdb->posts WHERE `post_status` = 'publish' AND `post_type` = 'post' ORDER BY `post_date` DESC";
			$posts = $wpdb->get_results($query);
			if(is_array($posts)){
				foreach($posts as $post){
					$permalink = get_permalink($post->ID);
					update_post_meta($post->ID, 'st_tiny_url', $this->shorten_url($permalink), true);
					//sleep(1); // Sleep for a few seconds so we don't overload the url shortening system.
				}
			}
			$note .= 'URL\'s ReGenerated';
		}
		if(isset($_POST['reset'])){
			$this->st_uninstall();
			$this->st_install();
			$note .= 'Settings\'s Reset';
		}
		$this->st_adm_notice($note);
		?>
		<h3>
    <?php _e('Advanced Functions', 'st_plugin'); ?>
  </h3>
  <form action="" method="post" enctype="application/x-www-form-urlencoded">
    <table width="90%" border="0" class="widefat post fixed">
    <thead>
    <tr>
    <th width="40%" class="manage-column"><?php _e('Advanced Settings', 'st_plugin'); ?></th>
    <th width="60%" class="manage-column">&nbsp;</th>
    </tr>
    </thead>
      <tr>
        <td width="40%"><?php _e('Hide Donation Button', 'st_plugin'); ?>
          <br>
          <em>(
          <?php _e('Tick for yes', 'st_plugin'); ?>
          )</em></td>
        <td width="60%"><label>
            <input type="checkbox" name="st_hide_donate" id="checkbox" value="yes" <?php if(get_option('st_hide_donate') == TRUE){?> checked="checked" <?php } ?>>
          </label></td>
      </tr>
      <tr>
        <td width="40%"><?php _e('Attempt to extend PHP max_execution_time and memory_limit', 'st_plugin'); ?><!-- This was added because I sometimes have problems upgrading plugins. This should help. -->
          <br>
          <em>(
          <?php _e('Tick for yes', 'st_plugin'); ?>
          )</em></td>
        <td width="60%"><label>
            <input type="checkbox" name="st_extend_php_limits" id="checkbox" value="yes" <?php if(get_option('st_extend_php_limits') == TRUE){?> checked="checked" <?php } ?>>
          </label></td>
      </tr>
    </table>
    <p class="submit">
      <input type="submit" name="save_advanced_settings" class="button-primary" value="Save Advanced Settings" />
    </p>
  </form>
  <form action="" method="post" enctype="application/x-www-form-urlencoded">
  	<p><input type="submit" name="regenerate_urls" class="button-primary" value="ReGenerate Shortened URL's" /> - <?php _e('May take a while or timeout.', 'st_plugin'); ?></p>
  </form>
  <form action="" method="post" enctype="application/x-www-form-urlencoded">
  	<p><input type="submit" name="reset" class="button-primary" value="Reset Socialize This" /> - <?php _e('Resets Socialize This\'s Settings.', 'st_plugin'); ?></p>
  </form>
  <?php
	}
	
	// socialize_this_settings() - The admin settings page
	function socialize_this_settings(){
		global $st_imgs, $st_url;
		echo '<div class="wrap">';
		$this->st_adm_header();
		if(isset($_GET['module'])){
			switch($_GET['module']){
				case 'social_widgets': $this->st_adm_social_widgets(); break;
				case 'templates': $this->st_adm_templates(); break;
				case 'credits': $this->st_adm_credits(); break;
				case 'advanced_functions': $this->st_adm_advanced_functions(); break;
				default: $this->st_adm_general();
			}
		} else {
			$this->st_adm_general();
		}
		$this->st_adm_footer();
		echo '</div>';
	}
	
	// admin_submit_post()) - Sets up bits of the post, which are useful.
	function admin_submit_post($postID){
		global $st_imgs, $st_url;
		$post = get_post($postID);
		if (is_object($post) && get_post_meta($post->ID, 'st_tiny_url', TRUE) == ''){ // If the tiny url already exists, we will assume it's already been twittered.
			$permalink = get_permalink($post->ID);
			// Add the TinyURL.com URL.
			add_post_meta($post->ID, 'st_tiny_url', $this->shorten_url($permalink), true);
			if(get_option('st_noti_twitter') === 'yes'){
				$vars['%permalink%'] = $permalink;
				$vars['%title%'] = $post->post_title;
				if($descirption != ''){
					$vars['%descirption%'] = $post->post_excerpt;
				} else {
					$vars['%descirption%'] = $post->post_title;
				}
				$vars['%short_url%'] = get_post_meta($post->ID, 'st_tiny_url', TRUE);
				if($vars['%short_url%'] == ''){
					$vars['%short_url%'] = $vars['%permalink%'];
				}
				$vars['%rss_url%'] = get_bloginfo('rss2_url');

				$this->twitter_update_status($this->sprintf4(get_option('st_template_twitter_noti'), $vars));
			}
		}
		
	}
	
	function shorten_url($url){
		global $st_imgs, $st_url;
		if(get_option('st_url_shortening_service') == 'tinyurl'){return $this->get_link('http://tinyurl.com/api-create.php?url='.urlencode($url));}
		elseif(get_option('st_url_shortening_service') == 'urly'){return $this->get_link('http://ur.ly/new.txt?href='.urlencode($url));}
		elseif(get_option('st_url_shortening_service') == 'isgd'){return $this->get_link('http://is.gd/api.php?longurl='.urlencode($url));}
		elseif(get_option('st_url_shortening_service') == 'klam'){return $this->get_link('http://kl.am/api/shorten/?format=text&url='.urlencode($url));}
		elseif(get_option('st_url_shortening_service') == 'unu'){return $this->get_link('http://u.nu/unu-api-simple?url='.urlencode($url));}
		else{return $this->get_link('http://api.tr.im/v1/trim_simple?url='.urlencode($url));}
		return $url;
	}
	
	function get_link($url){
		global $st_imgs, $st_url;
		if(function_exists('curl_init')){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			//curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			$result = curl_exec($ch);	
			$resultArray = curl_getinfo($ch);
			if ($resultArray['http_code'] == 200){
				return $result;
			} else {
				return FALSE;	
			}
			curl_close($ch);
		}elseif(ini_get('allow_url_fopen') == 1){
			return fopen($url, 'r');
		}
		return FALSE;
	}
	
	// show_social_widgets() - Shows a list of the widgets
	function show_social_widgets($icons=NULL){
		global $post, $st_imgs, $st_url;
		$output = '';
		if (is_object($post)){
			$permalink = get_permalink($post->ID);
			asort($st_imgs);
			$count = 0;
			$st_max_widgets_until_br_count = 0;
			
			$st_max_widgets_until_br = get_option('st_max_widgets_until_br');
			
			// Allows for different amounts of widgets per each line. each line sperated by a comma.
			$st_max_widgets_until_br_multi = explode(',', $st_max_widgets_until_br);
			if($st_max_widgets_until_br_multi[1] != ''){
				$st_max_widgets_until_br = $st_max_widgets_until_br_multi[0];
			}
			
			$widget_order = unserialize(get_option('st_widget_order')); // Pull the widget order.
			foreach($widget_order as $key){ // Loop the widgets
			
				$image = $st_imgs[$key];
				if($icons == NULL || !is_array($icons)){
					if(get_option('st_'.$key) === 'use'){ // Check if the admin wants the widget shown
						$count++;
						$output .= $this->render_social_links($permalink, $image, $post->post_title, $post->post_excerpt, $post->ID); // Show the widget
						
						// Decide weather or not to show a <br />
						if($st_max_widgets_until_br == $count){$output .=  '<br />'; $count = 0; $st_max_widgets_until_br_count++;
							if($st_max_widgets_until_br_multi[$st_max_widgets_until_br_count] != ''){
								$st_max_widgets_until_br = $st_max_widgets_until_br_multi[$st_max_widgets_until_br_count];
							}
						}
					}
				}elseif($icons != NULL && is_array($icons)){
					if(in_array($key, $icons)){
						$output .= $this->render_social_links($permalink, $image, $post->post_title, $post->post_excerpt, $post->ID); // Show the widget#
					}
				}
			}
		}
		echo '<div id="socialize-this">'.$output.'</div>';
	}
	
	function render_social_links($permalink, $image, $title=NULL, $descirption=NULL, $post_id=NULL){
		global $st_imgs, $st_url;
		// Pull the templates.
		$vars['%permalink%'] = urlencode($permalink);
		$vars['%title%'] = urlencode($title);
		if($descirption != ''){
			$vars['%descirption%'] = urlencode($descirption);
		} else {
			$vars['%descirption%'] = urlencode($title);
		}
		$vars['%widget_url%'] = $st_url.'/img/'.$image['file'];
		$vars['%widget_alt%'] = $image['title'];
		$vars['%short_url%'] = urlencode(get_post_meta($post_id, 'st_tiny_url', TRUE));
		$vars['%widget_image%'] ='<img src="'.$vars['%widget_url%'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.$vars['%widget_alt%'].'" title="'.$vars['%widget_alt%'].'" />';
		if($vars['%short_url%'] == ''){
			$vars['%short_url%'] = $vars['%permalink%'];
			}
		$vars['%rss_url%'] = get_bloginfo('rss2_url');
		
		return $this->sprintf4(get_option('st_template_'.$image['template']), $vars);
		
	}
	
	// function taken from PHP.net's  str_replace documentation.
	function sprintf4($str, $vars) {

    	return str_replace(array_keys($vars), array_values($vars), $str);
	}
	
	function twitter_update_status($message){
		global $st_imgs, $st_url;
		// Based on a script from - http://woork.blogspot.com/2007/10/twitter-send-message-from-php-page.html
		if(function_exists('curl_init')){
			$twitter['user'] = get_option('st_noti_twitter_user');
			$twitter['pass'] = get_option('st_noti_twitter_pass');
			$twitter['ch'] = curl_init();
			
			curl_setopt($twitter['ch'], CURLOPT_VERBOSE, 1);
			curl_setopt($twitter['ch'], CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($twitter['ch'], CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	
			// Send the username/password
			curl_setopt($twitter['ch'], CURLOPT_USERPWD, $twitter['user'].':'.$twitter['pass']);
			curl_setopt($twitter['ch'], CURLOPT_POSTFIELDS, 'status='.urlencode($message));
			curl_setopt($twitter['ch'], CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($twitter['ch'], CURLOPT_POST, true);
	
			// the URL it's going to AKA the twitter status updater.
			curl_setopt($twitter['ch'], CURLOPT_URL, 'http://twitter.com/statuses/update.xml');
	
			$result = curl_exec($twitter['ch']);	
			$resultArray = curl_getinfo($twitter['ch']);
	
			if ($resultArray['http_code'] == 200){
				return TRUE;
			} else {
				return FALSE;	
			}
			curl_close($twitter['ch']);
			}
		return FALSE;
	}
	
	function st_install($upgrade=NULL){
		
		// General Settings
		if($upgrade == NULL){
			// Add the templates etc.
			
			$templates_list['bump'] = '<a href="http://www.designbump.com/">Design Bump</a>';
			add_option('st_template_bump', '<a href="http://www.designbump.com/submit?url=%permalink%&title=%title%" target="_blank">%widget_image%</a>');
			$templates_list['del'] = '<a href="http://delicious.com/">Delicious</a>';
			add_option('st_template_del', '<a href="http://del.icio.us/submit?url=%permalink%&title=%title%" target="_blank">%widget_image%</a>');
			$templates_list['digg'] = '<a href="http://digg.com/">Digg</a>';
			add_option('st_template_digg', '<a href="http://digg.com/submit?phase=2&url=%permalink%" target="_blank">%widget_image%</a>');
			$templates_list['facebook'] = '<a href="http://www.facebook.com/">Facebook</a>';
			add_option('st_template_facebook', '<a href="http://www.facebook.com/sharer.php?u=%permalink%&t=%title%" target="_blank">%widget_image%</a>');
			$templates_list['float'] = '<a href="http://www.designfloat.com/">Design Float</a>';
			add_option('st_template_float', '<a href="http://www.designfloat.com/submit.php?url=%permalink%&title=%title%" target="_blank">%widget_image%</a>');
			$templates_list['mixx'] = '<a href="http://www.mixx.com/">Mixx</a>';
			add_option('st_template_mixx', '<a href="http://www.mixx.com/submit?page_url=%permalink%" target="_blank">%widget_image%</a>');
			$templates_list['reddit'] = '<a href="http://www.reddit.com/">Reddit</a>';
			add_option('st_template_reddit', '<a href="http://www.reddit.com/submit?url=%permalink%&title=%title%" target="_blank">%widget_image%</a>');
			$templates_list['rss'] = 'RSS Feed';
			add_option('st_template_rss', '<a href="%rss_url%" target="_blank">%widget_image%</a>');
			$templates_list['stumble'] = '<a href="http://www.stumbleupon.com/">StumbleUpon</a>';
			add_option('st_template_stumble', '<a href="http://www.stumbleupon.com/submit?url=%permalink%&title=%title%" target="_blank">%widget_image%</a>');
			$templates_list['google'] = '<a href="http://www.google.com/bookmarks/">Google</a>';
			add_option('st_template_google', '<a href="http://www.google.com/bookmarks/mark?op=add&bkmk=%permalink%&title=%title%&annotation=" target="_blank">%widget_image%</a>');
			$templates_list['linkedin'] = '<a href="http://www.linkedin.com/shareArticle">LinkedIn</a>';
			add_option('st_template_linkedin', '<a href="http://www.linkedin.com/shareArticle?mini=true&url=%permalink%&title=%title%&summary=%descirption%&source=Blog">%widget_image%</a>');
			$templates_list['tech'] = '<a href="http://technorati.com/">Technorati</a>';
			add_option('st_template_tech', '<a href="http://www.technorati.com/faves?add=%permalink%" target="_blank">%widget_image%</a>');
			$templates_list['twitter'] = '<a href="http://twitter.com/">Twitter</a>';
			add_option('st_template_twitter', '<a href="http://twitter.com/home?status=Currently reading %short_url%"  target="_blank">%widget_image%</a>');
			$templates_list['html'] = __('Extra bit of HTML', 'st_plugin');
			add_option('st_template_html', '<a href="%short_url%">Share This Link</a>');
			$templates_list['twitter_noti'] = '<a href="http://twitter.com/">Twitter<br />'.__('Post Notification Status Update', 'st_plugin').'</a><br />
			<em>'.__('Note: URL encoding will not take place for variables.', 'st_plugin').'</em>';
			add_option('st_template_twitter_noti', '%title% | %short_url%');
			
			add_option('st_templates', serialize($templates_list));
			
			add_option('st_include_in_posts', 'yes'); // activated by default.
			add_option('st_max_widgets_until_br', '3,-1');
			
			// Twitter 
			add_option('st_noti_twitter', 'no');
			add_option('st_noti_twitter_user', '');
			add_option('st_noti_twitter_pass', '');
			
			// bit.ly
			add_option('st_bitly_user', '');
			add_option('st_bitly_apikey', '');
			
			add_option('st_url_shortening_service', 'isgd');
			add_option('st_hide_donate', TRUE);
			add_option('st_extend_php_limits', NULL);
			add_option('st_show_template_delete_buttons', NULL);
			add_option('st_show_template_keys', NULL);
		
		
			// The widgets
			add_option('st_del', 'use');
			add_option('st_digg', 'use');
			add_option('st_facebook', 'use');
			add_option('st_rss', 'use');
			add_option('st_stumble', 'use');
			add_option('st_twitter', 'use');
		
		
			// widget Order
			$widget_order = explode(',', 'del,bump,facebook,digg,float,mixx,reddit,rss,stumble,tech,twitter,google,linkedin,html');
			add_option('st_widget_order', serialize($widget_order));
			
			// widget Deatils
			$st_imgs['bump']['name'] = 'bump';
			$st_imgs['bump']['title'] = 'Design Bump';
			$st_imgs['bump']['file'] = 'bump.png';
			$st_imgs['bump']['template'] = 'bump';
			$st_imgs['bump']['width'] = '48px';
			$st_imgs['bump']['height'] = '48px';
			
			$st_imgs['del']['name'] = 'del';
			$st_imgs['del']['title'] = 'Delicious';
			$st_imgs['del']['file'] = 'del.png';
			$st_imgs['del']['template'] = 'del';
			$st_imgs['del']['width'] = '48px';
			$st_imgs['del']['height'] = '48px';
			
			$st_imgs['digg']['name'] = 'digg';
			$st_imgs['digg']['title'] = 'Digg';
			$st_imgs['digg']['file'] = 'digg.png';
			$st_imgs['digg']['template'] = 'digg';
			$st_imgs['digg']['width'] = '48px';
			$st_imgs['digg']['height'] = '48px';
			
			$st_imgs['facebook']['name'] = 'facebook';
			$st_imgs['facebook']['title'] = 'Facebook';
			$st_imgs['facebook']['file'] = 'facebook.png';
			$st_imgs['facebook']['template'] = 'facebook';
			$st_imgs['facebook']['width'] = '48px';
			$st_imgs['facebook']['height'] = '48px';
			
			$st_imgs['float']['name'] = 'float';
			$st_imgs['float']['title'] = 'Design Float';
			$st_imgs['float']['file'] = 'float.png';
			$st_imgs['float']['template'] = 'float';
			$st_imgs['float']['width'] = '48px';
			$st_imgs['float']['height'] = '48px';
			
			$st_imgs['mixx']['name'] = 'mixx';
			$st_imgs['mixx']['title'] = 'Mixx';
			$st_imgs['mixx']['file'] = 'mixx.png';
			$st_imgs['mixx']['template'] = 'mixx';
			$st_imgs['mixx']['width'] = '48px';
			$st_imgs['mixx']['height'] = '48px';
			
			$st_imgs['reddit']['name'] = 'reddit';
			$st_imgs['reddit']['title'] = 'Reddit';
			$st_imgs['reddit']['file'] = 'reddit.png';
			$st_imgs['reddit']['template'] = 'reddit';
			$st_imgs['reddit']['width'] = '48px';
			$st_imgs['reddit']['height'] = '48px';
			
			$st_imgs['rss']['name'] = 'rss';
			$st_imgs['rss']['title'] = 'RSS Feed';
			$st_imgs['rss']['file'] = 'rss.png';
			$st_imgs['rss']['template'] = 'rss';
			$st_imgs['rss']['width'] = '48px';
			$st_imgs['rss']['height'] = '48px';
			
			$st_imgs['stumble']['name'] = 'stumble';
			$st_imgs['stumble']['title'] = 'StumbleUpon';
			$st_imgs['stumble']['file'] = 'stumble.png';
			$st_imgs['stumble']['template'] = 'stumble';
			$st_imgs['stumble']['width'] = '48px';
			$st_imgs['stumble']['height'] = '48px';
			
			$st_imgs['tech']['name'] = 'tech';
			$st_imgs['tech']['title'] = 'Technorati';
			$st_imgs['tech']['file'] = 'tech.png';
			$st_imgs['tech']['template'] = 'tech';
			$st_imgs['tech']['width'] = '48px';
			$st_imgs['tech']['height'] = '48px';
			
			$st_imgs['twitter']['name'] = 'twitter';
			$st_imgs['twitter']['title'] = 'Twitter';
			$st_imgs['twitter']['file'] = 'twitter.png';
			$st_imgs['twitter']['template'] = 'twitter';
			$st_imgs['twitter']['width'] = '48px';
			$st_imgs['twitter']['height'] = '48px';
			
			$st_imgs['google']['name'] = 'google';
			$st_imgs['google']['title'] = 'Google';
			$st_imgs['google']['file'] = 'google.png';
			$st_imgs['google']['template'] = 'google';
			$st_imgs['google']['width'] = '48px';
			$st_imgs['google']['height'] = '48px';
			
			$st_imgs['linkedin']['name'] = 'linkedin';
			$st_imgs['linkedin']['title'] = 'LinkedIn';
			$st_imgs['linkedin']['file'] = 'linkedin.png';
			$st_imgs['linkedin']['template'] = 'linkedin';
			$st_imgs['linkedin']['width'] = '48px';
			$st_imgs['linkedin']['height'] = '48px';
			
			$st_imgs['html']['name'] = 'html';
			$st_imgs['html']['title'] = 'HTML';
			$st_imgs['html']['file'] = 'html.png';
			$st_imgs['html']['template'] = 'html';
			$st_imgs['html']['width'] = '48px';
			$st_imgs['html']['height'] = '48px';
			
			add_option('st_imgs', serialize($st_imgs));
		}
		
		add_option('st_current_version', '1.6.1');
	}
	
	function st_uninstall($upgrade=NULL){
		// Do some house keeping	
		if($upgrade == NULL){
			// The templates etc.
			delete_option('st_template_bump');
			delete_option('st_template_del');
			delete_option('st_template_digg');
			delete_option('st_template_facebook');
			delete_option('st_template_float');
			delete_option('st_template_mixx');
			delete_option('st_template_reddit');
			delete_option('st_template_rss');
			delete_option('st_template_stumble');
			delete_option('st_template_google');
			delete_option('st_template_linkedin');
			delete_option('st_template_tech');
			delete_option('st_template_twitter');
			delete_option('st_template_html');
			delete_option('st_template_twitter_noti');
			
			delete_option('st_templates');
			
			// General Settings
			delete_option('st_include_in_posts');
			delete_option('st_max_widgets_until_br');
			delete_option('st_noti_twitter');
			delete_option('st_noti_twitter_user');
			delete_option('st_noti_twitter_pass');
			// bit.ly
			delete_option('st_bitly_user');
			delete_option('st_bitly_apikey');
			delete_option('st_url_shortening_service');
			delete_option('st_hide_donate');
			delete_option('st_extend_php_limits');
			delete_option('st_show_template_delete_buttons');
			delete_option('st_show_template_keys');
			
			// The widgets
			delete_option('st_del');
			delete_option('st_digg');
			delete_option('st_facebook');
			delete_option('st_rss');
			delete_option('st_stumble');
			delete_option('st_twitter');
			
			// widget Order
			delete_option('st_widget_order');
		
		
			// Uninstall all the widget sets.
			$results = $this->st_get_widget_sets();
			if(is_array($results)){
				foreach($results as $file){
					delete_option('st_installed_'.$file);
				}
			}
			
			// widget Deatils
			delete_option('st_imgs');
		}
		
		
		
		delete_option('st_current_version');
	}
}
if(!function_exists('show_social_widgets') && !function_exists('show_social_icons')){ // stops it screwing with other plugins.
	function show_social_widgets($icons=NULL){
		global $ql;
		$ql->show_social_widgets($icons);
	}
	function show_social_icons($icons=NULL){
		global $ql;
		$ql->show_social_widgets($icons);
	}
}
?>
