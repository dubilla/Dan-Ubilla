<?php

/*
Plugin Name: Feed Statistics
Plugin URI: http://www.chrisfinke.com/wordpress/plugins/feed-statistics/
Description: Compiles statistics about who is reading your blog via an RSS feed and what they're reading.
Version: 1.4.3
Author: Christopher Finke
Author URI: http://www.chrisfinke.com/
*/

if (preg_match("/feed\-statistics\.php$/", $_SERVER["PHP_SELF"])) {
	if (!defined('DB_NAME')) {
		$root = __FILE__;
		$i = 1;
		
		while ($root = dirname($root)) {
			if (file_exists($root . "/wp-load.php")) {
				require_once($root . "/wp-load.php");
				break;
			}
			else if (file_exists($root . "/wp-config.php")) {
				require_once($root . "/wp-config.php");
				break;
			}
			
			if ($root == '/') {
				if (isset($_GET["url"])){
					$url = base64_decode($_GET["url"]);
					header("Location: ".$url);
					return;	
				}
				else if (isset($_GET["view"])) {
					header("Content-Type: image/gif");
					echo base64_decode("R0lGODlhAQABAIAAANvf7wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==");
					return;
				}
				
				die;
			}
		}
	}
	
	if (isset($_GET["view"])){
		if (!empty($_GET["post_id"]) && get_option("feed_statistics_track_postviews")){
			global $table_prefix;
			global $wpdb;
			
			$sql = "INSERT INTO ".$table_prefix."feed_postviews
				SET 
					post_id=".intval($_GET["post_id"]).",
					time=NOW()";
			$wpdb->query($sql);
		}
	
		header("Content-Type: image/gif");
		echo base64_decode("R0lGODlhAQABAIAAANvf7wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==");
		return;
	}
	
	if (isset($_GET["url"])){
		$url = base64_decode($_GET["url"]);
		
		if (get_option("feed_statistics_track_clickthroughs")){
			if (trim($url) == '') die;

			global $table_prefix;
			global $wpdb;
			$link_id = 0;
		
			$wpdb->hide_errors();
			$sql = "SELECT id FROM ".$table_prefix."feed_links WHERE url='".mysql_real_escape_string($url)."'";
			$result = $wpdb->query($sql);
		
			if ($result) {
				$link_id = $wpdb->last_result[0]->id;
			}
			else {
				$sql = "INSERT INTO ".$table_prefix."feed_links SET	url='".mysql_real_escape_string($url)."'";
		
				if ($wpdb->query($sql)) {
					$link_id = $wpdb->insert_id;
				}
			}
		
			if ($link_id) {
				$sql = "INSERT INTO ".$table_prefix."feed_clickthroughs SET
					link_id=$link_id,
					time=NOW()";
				$wpdb->query($sql);
			}
		}
	
		$wpdb->show_errors();
	
		header("Location: ".$url);
		return;
	}
}

class FEED_STATS {
	function init(){
		global $table_prefix;
		global $wpdb;
		
		$version = get_option("feed_statistics_version");
		
		if (isset($_GET["reset_feed_statistics"])) $version = '';
		
		switch ($version) {
			case '1.0':
			case '1.0.1':
			case '1.0.2':
			case '1.0.3':
			case '1.0.4':
				$sql = "ALTER TABLE ".$table_prefix."feed_subscribers ADD user_agent VARCHAR(255) NOT NULL DEFAULT ''";
				$wpdb->query($sql);
				
				$sql = "CREATE TABLE IF NOT EXISTS ".$table_prefix."feed_clickthroughs (
					id INT(11) NOT NULL auto_increment,
					link_id INT(11) NOT NULL DEFAULT '0',
					referrer_id INT(11) NOT NULL DEFAULT '0',
					time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
					PRIMARY KEY (id)
				)";
				$wpdb->query($sql);
				
				$sql = "CREATE TABLE IF NOT EXISTS ".$table_prefix."feed_links (
					id INT(11) NOT NULL auto_increment,
					url VARCHAR(255) NOT NULL DEFAULT '',
					PRIMARY KEY (id),
					UNIQUE KEY `url` (`url`)
				)";
				$wpdb->query($sql);
				
				$sql = "CREATE TABLE IF NOT EXISTS ".$table_prefix."feed_referrers (
					id INT(11) NOT NULL auto_increment,
					url VARCHAR(255) NOT NULL DEFAULT '',
					PRIMARY KEY (id),
					UNIQUE KEY `url` (`url`)
				)";
				$wpdb->query($sql);
				
				$sql = "CREATE TABLE IF NOT EXISTS ".$table_prefix."feed_postviews (
					id INT(11) NOT NULL auto_increment,
					post_id INT(11) NOT NULL DEFAULT '0',
					time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
					PRIMARY KEY (id)
				)";
				$wpdb->query($sql);
				
				update_option("feed_statistics_track_clickthroughs", "0");
				update_option("feed_statistics_track_postviews", "1");
			case '1.1':
			case '1.1.1':
			case '1.1.2':
				$sql = "ALTER TABLE ".$table_prefix."feed_subscribers ADD `feed` VARCHAR( 120 ) NOT NULL AFTER `identifier`";
				$wpdb->query($sql);

				$sql = "ALTER TABLE ".$table_prefix."feed_subscribers DROP PRIMARY KEY, ADD PRIMARY KEY (identifier, feed)";
				$wpdb->query($sql);
			case '1.2':
			case '1.3':
				$sql = "DROP TABLE ".$table_prefix."feed_referrers";
				$wpdb->query($sql);
				
				$sql = "ALTER TABLE ".$table_prefix."feed_clickthroughs DROP referrer_id";
				$wpdb->query($sql);
			case '1.3.1':
				$sql = "ALTER TABLE ".$table_prefix."feed_subscribers CHANGE feed feed VARCHAR(120) NOT NULL";
				$wpdb->query($sql);
				
				update_option("feed_statistics_version","1.4.2");
			case '1.3.2':
				
				break;
			default:
				$sql = "CREATE TABLE IF NOT EXISTS ".$table_prefix."feed_subscribers (
					subscribers INT(11) NOT NULL DEFAULT 0,
					identifier VARCHAR(200) NOT NULL DEFAULT '',
					feed VARCHAR( 120 ) NOT NULL,
					date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					user_agent VARCHAR(255) NOT NULL DEFAULT '',
					PRIMARY KEY (identifier, feed)
				)";
				$wpdb->query($sql);
				
				$sql = "CREATE TABLE IF NOT EXISTS ".$table_prefix."feed_clickthroughs (
					id INT(11) NOT NULL auto_increment,
					link_id INT(11) NOT NULL DEFAULT '0',
					time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
					PRIMARY KEY (id)
				)";
				$wpdb->query($sql);
				
				$sql = "CREATE TABLE IF NOT EXISTS ".$table_prefix."feed_links (
					id INT(11) NOT NULL auto_increment,
					url VARCHAR(255) NOT NULL DEFAULT '',
					PRIMARY KEY (id),
					UNIQUE KEY `url` (`url`)
				)";
				$wpdb->query($sql);
				
				$sql = "CREATE TABLE IF NOT EXISTS ".$table_prefix."feed_postviews (
					id INT(11) NOT NULL auto_increment,
					post_id INT(11) NOT NULL DEFAULT '0',
					time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
					PRIMARY KEY (id)
				)";
				$wpdb->query($sql);
				
				update_option("feed_statistics_expiration_days","3");
				update_option("feed_statistics_version","1.4.2");
				update_option("feed_statistics_track_clickthroughs", "0");
				update_option("feed_statistics_track_postviews", "1");
				break;
		}
		
		if (FEED_STATS::is_feed_url()){
			$user_agent = $_SERVER["HTTP_USER_AGENT"];
			
			if (!preg_match("/(Mozilla|Opera|subscriber|user|feed)/Uis", $user_agent)){
				if (strlen($user_agent) > 3){
					return;
				}
			}
			
			if (!preg_match("/(readers|subscriber|user|feed)/Uis", $user_agent)){
				if (preg_match("/(slurp|bot|spider)/Uis", $user_agent)){
					return;
				}
			}
	
			$m = array();
			
			if (preg_match("/([0-9]+) subscriber/Uis", $user_agent, $m)) {
				// Not a typo below - should have been replacing $m[1], but screwed it up the first time around, so it's here to stay
				$identifier = str_replace($m[0], "###", $user_agent);
				$subscribers = $m[1];
			}
			else if (preg_match("/users ([0-9]+);/Uis", $user_agent, $m)) {
				// For Yahoo!'s bot
				$identifier = str_replace($m[1], "###", $user_agent);
				$subscribers = $m[1];
			}
			else if (preg_match("/ ([0-9]+) readers/Uis", $user_agent, $m)) {
				// For LiveJournal's bot
				$identifier = str_replace($m[1], "###", $user_agent);
				$subscribers = $m[1];
			}
			else {
				$identifier = $_SERVER["REMOTE_ADDR"];
				$subscribers = 1;
			}
			
			$feed = $_SERVER["REQUEST_URI"];
			
			if (!preg_match("/(\/|\.php|\?.*)$/Uis", $feed)){
				$feed .= "/";
			}
			
			$q = "SELECT * FROM ".$table_prefix."feed_subscribers
				WHERE identifier='".mysql_real_escape_string($identifier)."'
				AND feed=''";
			$results = $wpdb->get_results($q);
		
			if (!empty($results)) {
				$q = "UPDATE ".$table_prefix."feed_subscribers 
					SET 
						subscribers=".intval($subscribers).", 
						identifier='".mysql_real_escape_string($identifier)."', 
						user_agent='".mysql_real_escape_string($user_agent)."',
						feed='".mysql_real_escape_string($feed)."',
						date=NOW() 
					WHERE
						identifier='".mysql_real_escape_string($identifier)."'
						AND feed=''";
				$wpdb->query($q);
			}
			else {
				$q = "SELECT * FROM ".$table_prefix."feed_subscribers WHERE identifier='".mysql_real_escape_string($identifier)."' AND feed='".mysql_real_escape_string($feed)."'";
				$result = $wpdb->query($q);
				
				if ($result == 0) {
					$q = "INSERT INTO ".$table_prefix."feed_subscribers 
						SET 
							subscribers=".intval($subscribers).", 
							identifier='".mysql_real_escape_string($identifier)."', 
							user_agent='".mysql_real_escape_string($user_agent)."',
							feed='".mysql_real_escape_string($feed)."',
							date=NOW()";
					$wpdb->query($q);
				}
				else {
					$row = $wpdb->last_result[0];
					
					if ($user_agent != $row->user_agent || $subscribers != $row->subscribers){
						$q = "UPDATE ".$table_prefix."feed_subscribers
							SET
							date=NOW(), 
							user_agent='".mysql_real_escape_string($user_agent)."',
							subscribers=".intval($subscribers)."
							WHERE identifier='".mysql_real_escape_string($identifier)."' AND feed='".mysql_real_escape_string($feed)."'";
						$wpdb->query($q);
					}
				}
			}
		}
	}
	
	function is_feed_url() {
		switch (basename($_SERVER['PHP_SELF'])) {
			case 'wp-rdf.php':
			case 'wp-rss.php':
			case 'wp-rss2.php':
			case 'wp-atom.php':
			case 'feed':
			case 'rss2':
			case 'atom':
				return true;
				break;
		}
		
		if (isset($_GET["feed"])) {
			return true;
		}

		if (preg_match("/^\/(feed|rss2?|atom|rdf)/Uis", $_SERVER["REQUEST_URI"])) {
			return true;
		}
		
		if (preg_match("/\/(feed|rss2?|atom|rdf)\/?$/Uis", $_SERVER["REQUEST_URI"])){
			return true;
		}
		
		return false;
	}
	
	function how_many_subscribers() {
		global $table_prefix;
		global $wpdb;
		
		$q = "SELECT
				subscribers,
				CASE WHEN subscribers = 1 THEN identifier ELSE CONCAT(identifier, feed) END AS ident
			FROM ".$table_prefix."feed_subscribers 
			WHERE 
				(
					(date > '".date("Y-m-d H:i:s", time() - (60 * 60 * 24 * get_option("feed_statistics_expiration_days")))."') 
					OR 
					(
						LOCATE('###',identifier) != 0 AND 
						date > '".date("Y-m-d H:i:s", time() - (60 * 60 * 24 * get_option("feed_statistics_expiration_days") * 3))."'
					)
				)
				ORDER BY ident ASC, date DESC";
		$results = $wpdb->get_results($q);
		
		$s = 0;
		$current_ident = '';
		
		if (!empty($results)) {
			foreach ($results as $row){
				if ($row->ident != $current_ident){
					$s += $row->subscribers;
					$current_ident = $row->ident;
				}
			}
		}
		
		return intval($s);
	}
	
	function add_options_menu() {
		add_menu_page('Feed Options', 'Feed', 8, basename(__FILE__), 'feed_statistics_feed_page');
		add_submenu_page(basename(__FILE__), 'Top Feeds', 'Top Feeds', 8, 'feedstats-topfeeds', 'feed_statistics_topfeeds_page');
		add_submenu_page(basename(__FILE__), 'Feed Readers', 'Feed Readers', 8, 'feedstats-feedreaders', 'feed_statistics_feedreaders_page');
		add_submenu_page(basename(__FILE__), 'Post Views', 'Post Views', 8, 'feedstats-postviews', 'feed_statistics_postviews_page');
		add_submenu_page(basename(__FILE__), 'Clickthroughs', 'Clickthroughs', 8, 'feedstats-clickthroughs', 'feed_statistics_clickthroughs_page');
	}
	
	function clickthroughs_page(){
		global $table_prefix;
		global $wpdb;
		?>
			<div class="wrap">
				<p>You currently have clickthrough tracking turned <b>
				<?php
			
				echo (get_option("feed_statistics_track_clickthroughs")) ? "on" : "off";
			
				?></b>.
			</p>
			<br />

			<h2>Most popular links in your feed (last 30 days)</h2>
			<table style="width: 100%;">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th style="width: 45%;">Outgoing Link</th>
						<th>Clicks</th>
						<th style="width: 35%;">&nbsp;</th>
						</tr></thead>
				<tbody>
		<?php		
		
		$sql = "SELECT 
				COUNT(*) AS clicks,
				l.url AS link
			FROM ".$table_prefix."feed_clickthroughs AS c
			LEFT JOIN ".$table_prefix."feed_links AS l ON c.link_id=l.id
			WHERE time > '".date("Y-m-d H:i:s", time() - (60 * 60 * 24 * 30))."'
			GROUP BY c.link_id
			ORDER BY clicks DESC";
		$results = $wpdb->get_results($sql);
		
		$i = 1;
		
		if (!empty($results)) {
			$max = $results[0]->clicks;
		
			foreach ($results as $row){
				$percentage = ceil($row->clicks / $max * 100);
			
				echo '<tr><td>'.$i++.'.</td><td><a href="'.$row->link.'">'.$row->link.'</a></td><td>'.$row->clicks.'</td>
					<td>
						<div class="graph" style="width: '.$percentage.'%;">&nbsp;</div>
					</td>
					</tr>';
			}
		}
					
		?>			
				</tbody>
			</table>
		</div>
		<?php
	}
	
	function topfeeds_page(){
		global $table_prefix;
		global $wpdb;
		?>
		<div class="wrap">
			<h2>Your most popular feeds</h2>
			<table style="width: 100%;">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th style="width: 50%;">Feed URL</th>
						<th>Subscribers</th>
						<th style="width: 35%;">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
		<?php		
		
		$q = "SELECT
			feed,
			SUM(subscribers) subscribers
			FROM ".$table_prefix."feed_subscribers
			WHERE 
				feed != '' 
				AND 
				(
					(date > '".date("Y-m-d H:i:s", time() - (60 * 60 * 24 * get_option("feed_statistics_expiration_days")))."') 
					OR 
					(
						LOCATE('###',identifier) != 0 AND 
						date > '".date("Y-m-d H:i:s", time() - (60 * 60 * 24 * get_option("feed_statistics_expiration_days") * 3))."'
					)
				)
			GROUP BY feed
			ORDER BY subscribers DESC";
		$results = $wpdb->get_results($q);
		
		$feeds = array();
		
		$i = 1;
		
		if (!empty($results)){
			foreach ($results as $feed) {
				if (!isset($max)) $max = $feed->subscribers;
				
				$percentage = ceil($feed->subscribers / $max * 100);
			
				echo '<tr><td>'.$i++.'.</td><td style="width: 40%;"><a href="'.$feed->feed.'">'.$feed->feed.'</a></td><td style="width: 15%;">'.$feed->subscribers.'</td><td style="width: 40%;"><div class="graph" style="width: '.$percentage.'%;">&nbsp;</div></td></tr>';
			}
		}
		
		echo "</tbody></table>";
	}
	
	function postviews_page(){
		global $table_prefix;
		global $wpdb;
		?>
		<div class="wrap">
			<p>You currently have post view tracking turned <b>
			<?php
			
			echo (get_option("feed_statistics_track_postviews")) ? "on" : "off";
			
			?></b>.</p>
			<br />
			<h2>Your most popular posts (last 30 days)</h2>
			<table style="width: 100%;">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th style="width: 50%;">Post Title</th>
						<th>Views</th>
						<th style="width: 35%;">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
		<?php		
		
		$sql = "SELECT 
				COUNT(*) AS views,
				v.post_id,
				p.post_title title,
				p.guid permalink
			FROM ".$table_prefix."feed_postviews AS v
			LEFT JOIN ".$table_prefix."posts AS p ON v.post_id=p.ID
			WHERE time > '".date("Y-m-d H:i:s", time() - (60 * 60 * 24 * 30))."'
			GROUP BY v.post_id
			ORDER BY views DESC
			LIMIT 20";
		$results = $wpdb->get_results($sql);
		
		if (!empty($results)) {
			$i = 1;
			$max = $results[0]->views;
			
			foreach ($results as $row) {
				$percentage = ceil($row->views / $max * 100);
				echo '
					<tr>
						<td>'.$i++.'.</td>
						<td><a href="'.$row->permalink.'">'.$row->title.'</a></td>
						<td>'.$row->views.'</td>
						<td>
							<div class="graph" style="width: '.$percentage.'%;">&nbsp;</div>
						</td>
					</tr>';
			}
		}
					
		?>			
				</tbody>
			</table>
		</div>
		<?php
	}
	
	function feedreaders_page(){
		?>
		<div class="wrap">
		<h2>Top Feed Readers</h2>
		<?php 
		
		echo FEED_STATS::reader_stats();
		
		?>
		</div>
		<?php
	}
	
	function feed_page() {
		if (isset($_POST["feed_statistics_update"])){
			update_option("feed_statistics_expiration_days",intval($_POST["feed_statistics_expiration_days"]));
			update_option("feed_statistics_track_clickthroughs",intval(isset($_POST["feed_statistics_track_clickthroughs"])));
			update_option("feed_statistics_track_postviews",intval(isset($_POST["feed_statistics_track_postviews"])));
		} 
		?>
		<div class="wrap">
			<h2>Feed Options</h2>
			<form method="post" style="width: 100%;">
				<fieldset>
					<input type="hidden" name="feed_statistics_update" value="1"/>
					<p>Count users who have requested a feed within the last <input type="text" size="2" name="feed_statistics_expiration_days" value="<?php echo get_option("feed_statistics_expiration_days"); ?>" /> days as subscribers. You currently have <b><?php feed_subscribers(); ?></b>. </p>
					<p>
						<input type="checkbox" name="feed_statistics_track_clickthroughs" value="1" <?php if (get_option("feed_statistics_track_clickthroughs")) { ?>checked="checked"<?php } ?>>
						Track which links your subscribers click<br />
						This requires Wordpress to route all links in your posts back through your site so that clicks can be recorded.  The user shouldn't notice a difference.
					</p>
					<p>
						<input type="checkbox" name="feed_statistics_track_postviews" value="1" <?php if (get_option("feed_statistics_track_postviews")) { ?>checked="checked"<?php } ?>>
						Track individual post views<br />
						This is done via an invisible tracking image and will track views of posts by users that use feed readers that load images from your site.
					</p>
					<input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" />
				</fieldset>	
			</form>
		</div>
		<?php
	}
	
	function reader_stats() {
		global $wpdb, $table_prefix;
		
		$q = "SELECT
				CASE 
					WHEN 
						LOCATE('###',identifier) != 0 THEN SUBSTRING(identifier, 1, LOCATE(' ',identifier))
					ELSE
						user_agent
				END AS reader,
			SUM(subscribers) readers
			FROM ".$table_prefix."feed_subscribers
			WHERE date > '".date("Y-m-d H:i:s", time() - (60 * 60 * 24 * get_option("feed_statistics_expiration_days")))."'
			GROUP BY reader
			ORDER BY readers DESC";
		$results = $wpdb->get_results($q);
		
		$readers = array();
		
		if (!empty($results)){
			foreach ($results as $row){
			$reader = $row->reader;
			
			$version = array();
			
			if ($reader == '') {
				$reader = "Unknown (Pending)";
			} 
			else if (preg_match("/Navigator\/([0-9abpre\.]+)/is", $reader, $version)){
				$reader = "Netscape Navigator ".$version[1];
			}
			else if (preg_match("/Opera\/([0-9abpre\.]+)/is", $reader, $version)){
				$reader = "Opera ".$version[1];
			}
			else if (preg_match("/Flock\/([0-9abpre\.]+)/is", $reader, $version)){
				$reader = "Flock ".$version[1];
			}
			else if (preg_match("/(Firefox|BonEcho|GranParadiso)\/([0-9abpre\.]+)/is", $reader, $version)) {
				$reader = "Mozilla ".$version[1]." ".$version[2];
			}
			else if (preg_match("/MSIE ([0-9abpre\.]+)/is", $reader, $version)){
				$reader = "Internet Explorer ".$version[1];
			}
			else if (preg_match("/Gecko/Uis", $reader)) {
				$reader = "Other Mozilla browser";
			}
			else if (!preg_match("/Mozilla/Uis", $reader)){
				$reader = preg_replace("/[\/;].*$/Uis", "", $reader);
			}
			else {
				continue;
			}
			
			foreach ($readers as $key => $d) {
				if ($d["reader"] == $reader){
					$readers[$key]["readers"] += $row->readers;
					continue 2;
				}
			}
			
			$readers[] = array("reader" => $reader, "readers" => $row->readers);
			}
		}
		
		function sort_reader_array($a, $b) {
			return $b["readers"] - $a["readers"];
		}
		
		usort($readers, 'sort_reader_array');
		
		$max = $readers[0]["readers"];
		$rv = '<table style="width: 100%;">';
		$rv .= '<thead><tr><th>&nbsp;</th><th>Reader</th><th>Subscribers</th><th>&nbsp;</th></tr></thead><tbody>';
		
		$i = 1;
		
		foreach ($readers as $reader) {
			$percentage = ceil($reader["readers"] / $max * 100);
			
			$rv .= '<tr><td>'.$i++.'.</td><td style="width: 40%;">'.$reader["reader"].'</td><td style="width: 15%;">'.$reader["readers"].'</td><td style="width: 40%;"><div class="graph" style="width: '.$percentage.'%;">&nbsp;</div></td></tr>';
		}
		
		$rv .= "</tbody></table>";
		
		return $rv;
	}
	
	function widget_register() {
		if (function_exists('register_sidebar_widget')) {
			register_sidebar_widget('Feed Statistics', 'feed_statistics_widget');
		}
	}
	
	function widget($args) {
		extract($args);
		
		echo $before_widget;
		echo '<span class="subscriber_count">';
		feed_subscribers();
		echo '</span>';
		echo $after_widget;
	}
	
	function clickthrough_replace($content) {
		if (is_feed()) {
			$this_file = __FILE__;
			
			$redirect_url = feed_statistics_get_plugin_url() ."?url=";
		
			$content = preg_replace("/(<a[^>]+href=)(['\"])([^'\"]+)(['\"])([^>]*>)/e", "'$1\"$redirect_url'.base64_encode('\\3').'\"$5'", $content);
		}	
		
		return $content;
	}
	
	function postview_tracker($content) {
		global $id;
		
		if (is_feed()) {
			$content .= ' <img src="'.feed_statistics_get_plugin_url().'?view=1&post_id='.$id.'" width="1" height="1" style="display: none;" />';
		}
		
		return $content;
	}
	
	function admin_head() {
		?>
		<style type="text/css">
			div.graph {
				border: 1px solid rgb(13, 50, 79);
				background-color: rgb(131, 180, 216);
			}
		</style>		
		<?php
	}
}

function feed_statistics_get_plugin_url() {
	if (!defined('WP_CONTENT_URL')) {
		define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
	}
	
	$plugin_url = WP_CONTENT_URL . '/plugins/wordpress-feed-statistics/feed-statistics.php';
	
	return $plugin_url;
}

function feed_subscribers(){
	$s = FEED_STATS::how_many_subscribers();
	echo $s." feed subscriber";
	if ($s != 1) echo "s";
}

function feed_statistics_options() {
	FEED_STATS::options();
}

function feed_statistics_widget($args) {
	FEED_STATS::widget($args);
}

function feed_statistics_feed_page() {
	FEED_STATS::feed_page();
}
function feed_statistics_feedreaders_page() {
	FEED_STATS::feedreaders_page();
}
function feed_statistics_clickthroughs_page() {
	FEED_STATS::clickthroughs_page();
}
function feed_statistics_postviews_page() {
	FEED_STATS::postviews_page();
}
function feed_statistics_topfeeds_page() {
	FEED_STATS::topfeeds_page();
}

if(function_exists('add_action')){
	add_action('init', array('FEED_STATS','init'));
	add_action('init', array('FEED_STATS','widget_register'));
	add_action('admin_menu', array('FEED_STATS','add_options_menu'));
	add_action('admin_head', array('FEED_STATS','admin_head'));
}

if(function_exists('get_option')){
	if (get_option("feed_statistics_track_clickthroughs")) {
		add_filter('the_content', array('FEED_STATS','clickthrough_replace'));
	}

	if (get_option("feed_statistics_track_postviews")) {
		add_filter('the_content', array('FEED_STATS','postview_tracker'));
	}
}

?>
