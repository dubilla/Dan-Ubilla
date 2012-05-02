<?php 
/*
Plugin Name: GoodShelf
Plugin URI: htt://phalkunz.com/
Version: 1.0
Description: Wordpress plugin for displaying books from GoodReads 
Author: Saophalkun Ponlu
Author URI: http://phalkunz.com
*/

/* 
 * register widget
 */
function goodshelf_widget_init()
{
	// Check for the required API functions
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return;
	
	// Tell Dynamic Sidebar about our new widget and its control
	register_sidebar_widget('GoodShelf', 'goodshelf');
	register_widget_control("GoodShelf", "goodshelf_control");
}
function goodshelf() 
{
	display_goodshelf();
}

function goodshelf_control()
{
	$title = get_option('goodshelf-title');
	if ($_POST['goodshelf-title']) {
		$title = strip_tags(stripslashes($_POST['goodshelf-title']));
		update_option('goodshelf-title', $title);
	}
?>
	
	<p>
	
		<label for="goodshelf-title">Title</lable>
		<input name="goodshelf-title" value="<?php echo $title ?>" />
	</p>
<?php
}

/*
 * goodshelf settings
 */
// Delay plugin execution to ensure Dynamic Sidebar has a chance to load first
add_action('init', 'goodshelf_widget_init');

// plugin installation hook
register_activation_hook( __FILE__, 'goodshelf_activate' );

// admin menu hook
add_action('admin_menu','goodshelf_init');

function goodshelf_activate() {
	update_option('goodshelf-title', "GoodReads");
	update_option('book_num', '3');
	update_option('sort_by', 'random');
	update_option('cover_size', 'small');
	update_option('title_visible', '');
	update_option('title_length', '30');
	update_option('author_visible', '');
	update_option('author_label','Author:');
	update_option('isbn_visible', '');
	update_option('isbn_label','ISBN:');
	update_option('shelf', 'read');
	update_option('user_rating_visible', '');
	update_option('user_rating_label','User Rating:');
	update_option('avg_rating_visible', '');
	update_option('avg_rating_label','Avg Rating:');
}

function goodshelf_init() {
	// add GoodShelf as a Design's submenu
	// ref: add_theme_page( page_title, menu_title, access_level/capability, file, [function]);
	add_theme_page( "GoodShelf", "GoodShelf", 7, __FILE__, 'goodshelf_settings');
}

function goodshelf_settings() {
	process_form();
	print_settings_page();
}

// process form and update related wp options
function process_form() {
	if ($_POST['submit']) {	
		// get POST variables
		$uid = $_POST['uid'];
		$book_num = $_POST['book_num'];
		$sort_by = $_POST['sort_by'];
		$cover_size = $_POST['cover_size'];
		$title_visible = $_POST['title_visible'];
		$title_length = $_POST['title_length'];
		$author_visible = $_POST['author_visible'];
		$author_label = $_POST['author_label'];
		$isbn_visible = $_POST['isbn_visible'];
		$isbn_label = $_POST['isbn_label'];
		$shelf = $_POST['shelf'];
		$user_rating_visible = $_POST['user_rating_visible'];
		$user_rating_label = $_POST['user_rating_label'];
		$avg_rating_visible = $_POST['avg_rating_visible'];
		$avg_rating_label = $_POST['avg_rating_label'];
		
		// update wp options accordingly
		update_option('uid', $uid);
		if (is_numeric($book_num)) {
			update_option('book_num', $book_num);
		}
		else {
			update_option('book_num', 'invalid');
		}
		update_option('sort_by', $sort_by);
		update_option('cover_size', $cover_size);
		update_option('title_visible', $title_visible);
		update_option('title_length', $title_length);
		update_option('author_visible', $author_visible);
		update_option('author_label', $author_label);
		update_option('isbn_visible', $isbn_visible);
		update_option('isbn_label', $isbn_label);
		update_option('shelf', $shelf);
		update_option('user_rating_visible', $user_rating_visible);
		update_option('user_rating_label', $user_rating_label);
		update_option('avg_rating_visible', $avg_rating_visible);
		update_option('avg_rating_label', $avg_rating_label);
	}

}

// Output GoodShelf settings page
function print_settings_page() {
	$sort_options = array('date_published', 'rating', 'shelves', 'date_created', 'comments', 'votes', 'review', 'title', 'author', 'notes', 'random', 'num_ratings', 'date_read', 'date_added', 'position', 'avg_rating');
?>	
	<div class="wrap">
		<h2><em>GoodShelf</em> Configuration</h2>
		<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<table class="form-table">
				<tr>
					<th>GoodReads UserID</th>
					<td>
						<input type="text" name="uid" value="<?php echo get_option('uid'); ?>" />
						<br/><span>GoodReads user ID can be obtained from GoodReads <strong>URL</strong>. For example, when you viewing your library the URL looks like http://www.goodreads.com/review/list/<strong>1177502</strong>. In this case, the user id is <strong>1177502</strong>.</span>
					</td>
				</tr>
				<tr>
					<th>Shelf</th>
					<td>
						<input type="text" name="shelf" value="<?php echo get_option('shelf'); ?>"/>
					</td>
				</tr>
				<tr>
					<th>Number of Books</th>
					<td>
						<?php 
							if(!is_numeric(get_option('book_num'))) 
							{
								echo "<span style=\"color:#ff0000;\">The value you entered in this field is NOT valid so the default value is taken. It has to be a number</span><br/>"; 
								update_option('book_num', '3');
							}
							
						?>
						<input type="text" name="book_num" value="<?php echo get_option('book_num'); ?>"/> It is a number ranging from 1 to 200.
					</td>
				</tr>
				<tr>
					<th>Sort By</th>
					

					<td>
						<select name="sort_by">
							<?php
								foreach ($sort_options as $key=>$value) {
									if (get_option('sort_by')==$value) {
										echo "<option value=\"$value\" selected>$value</option>";
									}
									else {
										echo "<option value=\"$value\">$value</option>";
									}
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th>Show Cover</th>
					<td>
						<select name="cover_size">
							<option value="none" <?php if(get_option('cover_size')=='none') { echo 'selected'; } ?>>none</option>
							<option value="small" <?php if(get_option('cover_size')=='small' || get_option('cover_size')=='') { echo 'selected'; } ?>>small</option>
							<option value="medium" <?php if(get_option('cover_size')=='medium') { echo 'selected'; } ?>>medium</option>
							<option value="large" <?php if(get_option('cover_size')=='large') { echo 'selected'; } ?>>large</option>
						</select>
						
					</td>
				</tr>
				<tr>
					<th>Show Title & Length</th>
					<td>
						<input type="checkbox" name="title_visible" <?php if(get_option('title_visible')) { echo 'checked'; } ?> /> <input type="text" name="title_length" value="<?php if(is_numeric(get_option('title_length'))) echo get_option('title_length'); else echo "30"; ?>" /> <?php if(!is_numeric(get_option('title_length'))) echo "<span style=\"color:#ff0000;\">The lenght of the title must be a number.</span>" ?>
					</td>
				</tr>
				<tr>
					<th>Show Author & Label</th>
					<td>
						<input type="checkbox" name="author_visible" <?php if(get_option('author_visible')) { echo 'checked'; } ?> /> <input type="text" name="author_label" value="<?php echo get_option('author_label') ?>" />
					</td>
				</tr>
				<tr>
					<th>Show ISBN & Label</th>
					<td>
						<input type="checkbox" name="isbn_visible" <?php if(get_option('isbn_visible')) { echo 'checked'; } ?> /> <input type="text" name="isbn_label" value="<?php echo get_option('isbn_label') ?>" />
					</td>
				</tr>
				<tr>
					<th>Show User Rating & Label</th>
					<td>
						<input type="checkbox" name="user_rating_visible" <?php if(get_option('user_rating_visible')) { echo 'checked'; } ?> /> <input type="text" name="user_rating_label" value="<?php echo get_option('user_rating_label') ?>" />
					</td>
				</tr>
				<tr>
					<th>Show Average Rating & Label</th>
					<td>
						<input type="checkbox" name="avg_rating_visible" <?php if(get_option('avg_rating_visible')) { echo 'checked'; } ?> /> <input type="text" name="avg_rating_label" value="<?php echo get_option('avg_rating_label') ?>" />
					</td>
				</tr>
			</table>
			<p class="submit"><input type="submit" value="Save Changes" name="submit" /></p>
		</form>
	</div>
	
<?php
} // end of print_settings_page()

//------------------------------------------------------------
// display a list books
//------------------------------------------------------------
function display_goodshelf() {
	include_once(ABSPATH . WPINC . '/rss.php');
	$base_uri = "http://www.goodreads.com/review/list_rss/";
	$url = "";
	
	$uid = get_option('uid');
	$book_num = get_option('book_num');
	$sort_by = get_option('sort_by');
	
	$cover_size = get_option('cover_size');
	$title_visible = get_option('title_visible');
	$author_visible = get_option('author_visible');
	$isbn_visible = get_option('isbn_visible');
	$shelf = get_option('shelf');
	$user_rating_visible = get_option('user_rating_visible');
	$avg_rating_visible = get_option('avg_rating_visible');
	
	if(trim($uid)=='') {
		echo "no uid yet!";
		
	} else {
		// construct url
		$url = $base_uri . trim($uid) . "?shelf=$shelf&per_page=$book_num&sort=$sort_by";
		$rss = fetch_rss($url);
		if (empty($rss->items)) 
		{
			echo '<li>No items</li>';
		}
		else {
			echo "<ol id=\"goodreads\">";
			foreach($rss->items as $item) {
				echo "<li>";
				// check visibility of cover
				if($cover_size!='none') {
					$cover_url = "";	
					if($cover_size=='small') {
						$cover_url = $item['book_small_image_url'];
					}
					elseif ($cover_size=='medium') {
						$cover_url = $item['book_medium_image_url'];
					}
					elseif ($cover_size='large') {
						echo $cover_url = $item['book_large_image_url'];
					}
					echo "<a title=\"".trim($item['title'])."\" href=\"".trim($item['link'])."\"><img class=\"bookcover\" valign=\"top\" src=\"".trim($cover_url)."\" /></a>";
				}
				
				echo '<span class="goodshelf_text">';
				// check title visibility
				if($title_visible) {
					$item['title'] = trim($item['title']);
					if (strlen($item['title']) > get_option('title_length')) {
						$item['title'] = trim(substr($item['title'], 0, get_option('title_length'))). '...';
					}
					echo "<a title=\"".trim($item['title'])."\" href=\"".trim($item['link'])."\">".$item['title']."</a><br/>";
				}
				if($author_visible) {
					$item['author_name'] = trim($item['author_name']); 
					echo "<span class=\"goodshelf_label\">".get_option('author_label')."</span> ".$item['author_name']."<br/>";
				}
				if($isbn_visible) {
					$item['isbn'] = trim($item['isbn']);
					echo "<span class=\"goodshelf_label\">".get_option('isbn_label')."</span> ".$item['isbn']."<br/>";
				}
				if($user_rating_visible) {
					echo "<span class=\"goodshelf_label\">".get_option('user_rating_label')."</span> ".$item['user_rating']."<br/>";
				}
				if($avg_rating_visible) {
					echo "<span class=\"goodshelf_label\">".get_option('avg_rating_label')."</span> ".$item['average_rating'];
				}
				
				echo '</span>';
				echo "</li>";
			}
			echo "</ol>";
		}
		
	}
} // end of display_goodshelf()

function goodreads_stylesheet() {
	?>
	<style type="text/css">
		#goodreads    { padding: 0px; margin-left: 15px; }
  	#goodreads li { list-style-type: none; margin: 0px; padding: 0px; display: inline; }
  	img.bookcover { margin: 0 20px 5px 0; }
	</style>
	<?php
}

add_action('wp_head', 'goodreads_stylesheet');

?>