<?php
/*
Plugin Name: Netflix
Plugin URI: http://www.albertbanks.com/wordpress-netflix-plugin/
Description: Displays info from your Netflix account.  This includes text and images via RSS feed.
Version: 3.1
License: GPL
Author: Albert Banks
Author URI: http://albertbanks.com
*/

// display netflix info
function netflix($number_movies = 10, $feed= 'home', $display_type = 'raw', $image_size = 'small', $before = '<li>', $after = '</li>', $netflix_id='') {

	// use configuration if args not set
	for($i = 0 ; $i < func_num_args(); $i++) {
		$args[] = func_get_arg($i);
	}
	if (!isset($args[0])) $number_movies = get_option('netflix_number_movies'); 
	if (!isset($args[1])) $feed = get_option('netflix_feed');
	if (!isset($args[2])) $display_type = trim(get_option('netflix_display_type'));
	if (!isset($args[3])) $image_size = get_option('netflix_image_size');
	if (!isset($args[4])) $before = stripslashes(get_option('netflix_before'));
	if (!isset($args[5])) $after = stripslashes(get_option('netflix_after'));
	if (!isset($args[6])) $netflix_id = stripslashes(get_option('netflix_netflix_id'));
	
	// rss functions
	require_once(ABSPATH.'wp-includes/rss-functions.php');

	// feed
	switch($feed) {
		case 'recent':
			$url = "http://rss.netflix.com/TrackingRSS?id=".$netflix_id;
			break;
		case 'recommendations':
			$url = "http://rss.netflix.com/RecommendationsRSS?id=".$netflix_id;
			break;
		case 'queue':
			$url = "http://rss.netflix.com/QueueRSS?id=".$netflix_id;
			break;
		default: 
			$url = "http://rss.netflix.com/AtHomeRSS?id=".$netflix_id;
	}
	
	// url setup properly
	if ($url) {
		$rss = fetch_rss($url);
		foreach ($rss->items as $item) {

			// no limit, exit
			if ($number_movies == 0) break;

			// vars
			$raw_title = $item['title'];
			$title_start_position = strpos($raw_title, " ") + 1;
			$title = substr($raw_title, $title_start_position);
			$link = $item['link'];
			if (preg_match("#/(\d+)#", $link, $matches)) $movie_id = $matches[1]; 
			

			// diplay type
			switch($display_type) {
				case 'title':
					if ($feed == 'home' || $feed == 'recommendations') $display = $raw_title;
					else $display = $title;
					break;
				case 'image':
					if ($feed == 'home' || $feed == 'recommendations') $title = $raw_title;
					$display = '<img src="http://cdn.nflximg.com/us/boxshots/'.$image_size.'/'.$movie_id.'.jpg" alt="'.$title.'" title="'.$title.'" class="dvdcover" />';
					break;
				case 'both':
					if ($feed == 'home' || $feed == 'recommendations') $title = $raw_title;
					$display = '<img src="http://cdn.nflximg.com/us/boxshots/'.$image_size.'/'.$movie_id.'.jpg" alt="'.$title.'" title="'.$title.'" class="dvdcover" /><br />'.$title.'';
					break;
				// raw
				default: 
					$display = $raw_title;
			}
		
			// display link
			$netflix .= wptexturize($before.'<a href="'.$link.'">'.$display.'</a>'.$after);
			$number_movies--;
		}		
	}
	
	echo '<ol id="netflix">'.$netflix.'</ol>';

}

function netflix_movies($number_movies = 10, $feed= 'home', $netflix_id='') {

	// includes
	require_once('movie.class.php');
	require_once(ABSPATH.'wp-includes/rss-functions.php');
	
	$movies = array();
	
	// use configuration if args not set
	for($i = 0 ; $i < func_num_args(); $i++) {
		$args[] = func_get_arg($i);
	}
	if (!isset($args[0])) $number_movies = get_option('netflix_number_movies'); 
	if (!isset($args[1])) $feed = get_option('netflix_feed');
	if (!isset($args[6])) $netflix_id = stripslashes(get_option('netflix_netflix_id'));
	
	// loop through feed
	$url = generate_feed_url($netflix_id, $feed);
	$rss = fetch_rss($url);
	foreach ($rss->items as $item) {

		// limit reached, exit
		if ($number_movies == 0) break;

		// vars
		$title = generate_title($item['title'], $feed);
		$link = $item['link'];
		if (preg_match("#/(\d+)#", $link, $matches)) $movie_id = $matches[1];
		$description = $item['description']; 
		
		// save movie
		$results[] = new Movie($movie_id, $title, $link, $description);
		$number_movies--;
	}
	return $results;
}

// generate movie title
function generate_title($title, $feed) {

	// feed
	if ($feed == 'recent' || $feed == 'queue') {
		$title_start_position = strpos($title, ' ');
		return substr($title, $title_start_position);
	}
	return $title;
}

// generate feed url
function generate_feed_url($netflix_id, $feed) {

	// feed
	switch($feed) {
		case 'recent':
			return "http://rss.netflix.com/TrackingRSS?id=".$netflix_id;
		case 'recommendations':
			return "http://rss.netflix.com/RecommendationsRSS?id=".$netflix_id;
		case 'queue':
			return "http://rss.netflix.com/QueueRSS?id=".$netflix_id;
			break;
		default: 
			return "http://rss.netflix.com/AtHomeRSS?id=".$netflix_id;
	}
}

function netflix_stylesheet() {
	?>
	<style type="text/css">
		#netflix    { padding: 0px; margin: 0 0 0 15px; }
  	#netflix li { list-style-type: none; margin: 0px; padding: 0px; display: inline; }
  	img.dvdcover { margin: 0 20px 5px 0; }
	</style>
	<?php
}

// subpanel to set netflix options
function netflix_subpanel() {
	// form submitted
	if (isset($_POST['configure_netflix'])) {
		// gather form data
		$form_netflix_id = $_POST['netflix_id'];
		$form_number_movies = $_POST['number_movies'];
		$form_feed = $_POST['feed'];
		$form_display_type = $_POST['display_type'];
		$form_image_size = $_POST['image_size'];
		$form_before = $_POST['before'];
		$form_after = $_POST['after'];

		// update options
		update_option('netflix_netflix_id', $form_netflix_id);
		update_option('netflix_number_movies', $form_number_movies);
		update_option('netflix_feed', $form_feed);
		update_option('netflix_display_type', $form_display_type);
		update_option('netflix_image_size', $form_image_size);
		update_option('netflix_before', $form_before);
		update_option('netflix_after', $form_after);
?>

<div class="updated">
  <p>Options changes saved.</p>
</div>
<?php
	}
?>
<div class="wrap">
  <h2>Netflix Configuration</h2>
  <form method="post">
    <fieldset class="options">
    <table>
      <tr>
        <td><p><strong>
            <label for="flickr_nsid">Netflix ID</label>
            :</strong></p></td>
        <td><input name="netflix_id" type="text" id="netflix_id" value="<?php echo get_option('netflix_netflix_id'); ?>" size="40" />
          View your <a href="http://www.netflix.com/RSSFeeds">Personal Feeds</a> to find your id.
          </p></td>
      </tr>
      <tr>
        <td><p><strong>Feed:</strong></p></td>
        <td><select name="number_movies" id="number_movies">
            <option <?php if(get_option('netflix_number_movies') == '1') { echo "selected"; } ?> value="1">1</option>
            <option <?php if(get_option('netflix_number_movies') == '2') { echo "selected"; } ?> value="2">2</option>
            <option <?php if(get_option('netflix_number_movies') == '3') { echo "selected"; } ?> value="3">3</option>
            <option <?php if(get_option('netflix_number_movies') == '4') { echo "selected"; } ?> value="4">4</option>
            <option <?php if(get_option('netflix_number_movies') == '5') { echo "selected"; } ?> value="5">5</option>
            <option <?php if(get_option('netflix_number_movies') == '6') { echo "selected"; } ?> value="6">6</option>
            <option <?php if(get_option('netflix_number_movies') == '7') { echo "selected"; } ?> value="7">7</option>
            <option <?php if(get_option('netflix_number_movies') == '8') { echo "selected"; } ?> value="8">8</option>
            <option <?php if(get_option('netflix_number_movies') == '9') { echo "selected"; } ?> value="9">9</option>
            <option <?php if(get_option('netflix_number_movies') == '10') { echo "selected"; } ?> value="10">10</option>
          </select>
          movies from your
          <select name="feed" id="feed">
            <option <?php if(get_option('netflix_feed') == 'home') { echo "selected"; } ?> value="home">Movies At Home</option>
            <option <?php if(get_option('netflix_feed') == 'queue') { echo "selected"; } ?> value="queue">Queue</option>
            <option <?php if(get_option('netflix_feed') == 'recent') { echo "selected"; } ?> value="recent">Most Recent Rental Activity</option>
            <option <?php if(get_option('netflix_feed') == 'recommendations') { echo "selected"; } ?> value="recommendations">Recommendations</option>
          </select>
          RSS feed </td>
      </tr>
      <tr>
        <td><p><strong>Display:</strong> </p></td>
        <td><select name="display_type" id="display_type">
            <option <?php if(get_option('netflix_display_type') == 'title') { echo "selected"; } ?> value="title">Movie Title</option>
            <option <?php if(get_option('netflix_display_type') == 'raw') { echo "selected"; } ?> value="raw">Raw Text From Feed </option>
            <option <?php if(get_option('netflix_display_type') == 'image') { echo "selected"; } ?> value="image">Cover Image</option>
            <option <?php if(get_option('netflix_display_type') == 'both') { echo "selected"; } ?> value="both">Image and Title</option>
          </select>
          (if image select size:
          <select name="image_size" id="image_size">
            <option <?php if(get_option('netflix_image_size') == 'small') { echo "selected"; } ?> value="small">Small</option>
            <option <?php if(get_option('netflix_image_size') == 'large') { echo "selected"; } ?> value="large">Large</option>
          </select>
          ) 
      </tr>
      <tr>
        <td><p><strong>
            <label for="before">Before</label>
            /
            <label for="after">After</label>
            :</strong></p></td>
        <td><input name="before" type="text" id="before" value="<?php echo htmlspecialchars(stripslashes(get_option('netflix_before'))); ?>" size="10" />
          /
          <input name="after" type="text" id="afte" value="<?php echo htmlspecialchars(stripslashes(get_option('netflix_after'))); ?>" size="10" />
          <em> e.g. &lt;li&gt;&lt;/li&gt;, &lt;p&gt;&lt;/p&gt;</em>
          </p>
        </td>
      </tr>
    </table>
    </fieldset>
    <p>
    <div class="submit">
      <input type="submit" name="configure_netflix" value="<?php _e('Configure Netflix &raquo;', 'configure_netflix') ?>" />
    </div>
    </p>
  </form>
</div>
<?php 
} 

function netflix_admin_menu() {
	if (function_exists('add_submenu_page')) {
		add_submenu_page('plugins.php', 'Netflix Configuration Page', 'Netflix', 8, basename(__FILE__), 'netflix_subpanel');
	}
}

add_action('admin_menu', 'netflix_admin_menu'); 
add_action('wp_head', 'netflix_stylesheet');

?>
