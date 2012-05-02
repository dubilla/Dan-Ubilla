<?php
/**
 * @author Anuj Chauhan<anuj@arcgate.com>
 *
 *
 */
class FbmlTags {
	private $app_id;
	private $href;
	private $event_id;

	function __construct() {
		$this->app_id = FB_APPID;
		$this->href = FB_SITE_HREF;
		$this->event_id = FB_EVENT_ID;
	}

/**
 * Facebook Like button
 *
 */
	public function fbc_get_like_button($href) {
		if($href)
			$like_button = '<fb:like href="'. $href .'" width="280px" height="30px"></fb:like>';
		else
			$like_button = '<fb:like width="280px"  height="30px"></fb:like>';
		return $like_button;
	}

/**
 * Facebook Login button
 *
 */
	public function fbc_get_login_button() {
		global $current_user;
		if($current_user->ID == 0) {
			//$login_button .= $current_user->first_name . " " .$current_user->last_name;
			$login_button .= '<fb:login-button autologoutlink="true" perms="read_stream,offline_access,publish_stream,email,status_update" size="large" background="light" >fconnect</fb:login-button>';
		} else {
			$login_button .= '<a href="' . esc_url( wp_logout_url($redirect) ) . '" onClick="fbc_js_logout()">' . __('Log out') . '</a>';
		}


		return $login_button;
	}

/**
 * Facebook Recommendation block
 *
 */
	public function fbc_get_recommendation_block() {
		$rec_block = '<fb:recommendations site="'. get_option('site_url') .'" width="280" border_color="#FFFFFF" header="false"></fb:recommendations>';
		return $rec_block;
	}

/**
 * Facebook Share button
 *
 */
	public function fbc_get_share_button($url) {
		$share_button = '<fb:share-button href="'. $url .'" type="button_count" ></fb:share-button>';
		return $share_button;
	}

/**
 * Facebook Activity block
 *
 */
	public function fbc_get_activity_block(){
		$activity_block = '<fb:activity site="'. get_option('site_url') .'"  width="280"></fb:activity>';
		return $activity_block;
	}

/**
 *  Facebook Comments block
 *
 */
	public function fbc_get_comments_block() {
		$comments_block = '<fb:comments></fb:comments>';
		return $comments_block;
	}

/**
 * Facebook LiveStream functionality
 *
 */
	public function fbc_get_live_stream_block() {
		$live_stream = '<fb:live-stream event_app_id="'. $this->event_id .'" width="280"></fb:live-stream>';
		return $live_stream;
	}

/**
 * Facebook javascript code
 * adds in wp footer
 */
	public function fbc_fbml_js() {
		$facebook_js = "<div id='fb-root'></div>
											<script>
												window.fbAsyncInit = function() {
													FB.init({appId: ". FB_APPID .", status: true, cookie: true,
																	 xfbml: true});
													FB.Event.subscribe('auth.login',function(response) { window.location.href='".get_bloginfo("url")."?fb_login=yes';});
													FB.Event.subscribe('auth.logout',function(response) { window.location.href='".get_bloginfo("url")."?fb_logout=yes';});

												};
												(function() {
													var e = document.createElement('script'); e.async = true;
													e.src = document.location.protocol +
														'//connect.facebook.net/en_US/all.js';
													document.getElementById('fb-root').appendChild(e);
												}());
												function fbc_js_logout() {
													FB.logout(function() { alert('I am out'); });
												}
										</script>
										";
		echo $facebook_js;
	}

/**
 * function to render like button in content posts
 * @param $content string post content
 * @return string return content
 */
	public function fbc_the_fshare_content($content) {
		global $post;
		if(!is_page() ) {
			$url = get_permalink($post->ID);
			$content .=  '<div class="fbconnect_head_share">';
			if( get_option( 'fbc_enable_flike' ) )
				$content .=  FbmlTags::fbc_get_like_button($url) ;
			if( get_option( 'fbc_enable_fshare' ) )
				$content .=  FbmlTags::fbc_get_share_button($url);

			$content .=  '</div>';
		}
		return $content;
	}

/**
 * function to render XFBML tag in head part
 * @param $html string doctype
 * @return string return doctype
 */
	public function fbc_set_xfbml_tag($html){
		return "xmlns:fb=\"http://www.facebook.com/2008/fbml\" ".$html;
	}

/**
 * function to render Recommendation block in sidebar
 * @param $args Array sidebar widget arguments
 * @return string return sidebar block
 */
	public function fbc_recommendation_widget($args) {
		extract($args);
		//$rec_widget = $before_widget;
		//$rec_widget .= $before_title. '<h4>RECOMMENDED FOR YOU:</h4>				<div class="rightBlocBox">' ;
		//$rec_widget .= $after_title;
		$rec_widget .= '<h4>RECOMMENDED FOR YOU:</h4>
				<div class="rightBlocBox"> '.FbmlTags::fbc_get_recommendation_block();
		$rec_widget .= '</div>'.$after_widget;
		echo $rec_widget;
	}

/**
 * function to render Activity block in sidebar
 * @param $args Array sidebar widget arguments
 * @return string return sidebar block
 */
	public function fbc_activity_widget($args) {
		extract($args);
		$act_widget = $before_widget;
		$act_widget .= $before_title;
		$act_widget .= "Facebook Activity";
		$act_widget .= $after_title;
		$act_widget .= FbmlTags::fbc_get_activity_block();
		$act_widget .= $after_widget;
		echo $act_widget;
	}

/**
 * function to render Login block in sidebar
 * @param $args Array sidebar widget arguments
 * @return string return sidebar block
 */
	public function fbc_login_widget($args) {
		extract($args);
		$act_widget = $before_widget;
		$act_widget .= $before_title ."Facebook Login";
		$act_widget .= $after_title;
		$act_widget .= FbmlTags::fbc_get_login_button();
		$act_widget .= $after_widget;
		echo $act_widget;
	}

	function fbc_logout() {
		if ( ! is_user_logged_in() )
		$link = '<a href="' . esc_url( wp_login_url($redirect) ) . '">' . __('Log in') . '</a>';
	else
		$link = '<a href="' . esc_url( wp_logout_url($redirect) ) . '" onClick="fbc_js_logout()">' . __('Log out') . '</a>';
		echo $link;
	}

}

global $fbml_tags;
$fbml_tags = new FbmlTags();
?>