Plugin Name: Netflix
Plugin URI: http://www.albertbanks.com/wordpress-netflix-plugin/
Description: Displays info from your Netflix account.  This includes text and images via RSS feed.
Version: 3.1
License: GPL
Author: Albert Banks
Author URI: http://albertbanks.com


Changelog
-----------------------------------
3.1
Fixed image referencing issue.

3.0:
Implemented movie class.
Added netflix_movies function to retrieve movie objects based on feed.

2.2:
Fixed problem with image references.

2.1:
Added 'Movies At Home' feed option.
Set default feed to 'Movies At Home'.
Fixed problem with raw titles and images.

2.0.2: 
Fixed problem with differing movieid length.

2.0.1: 
Fixed problem for Wordpress 1.5.x users when not including netflix_id in netflix() call.

2.0: 
Integrated configuration into WordPress plugin section.
Added before/after option to add html before and after display.

1.0: 
First version.
			

Instructions
-----------------------------------
 1. Download plugin and unzip. 
 2. Upload the folder (netflix/) to your WordPress plugins directory inside of wp-content.
 3. Activate it from the plugins menu inside of WordPress.
 4. Configure your settings via the panel in plugins.

NOTE: Your netflix id comes from your Netflix RSS feed. Login to your Netflix account and go to your queue. At the bottom of the page click the RSS link. Copy the id variable in one of the RSS links and paste it in the configuration panel.


Usage
-----------------------------------


FUNCTION: netflix($number_movies, $feed, $display_type, $image_size, $before, $after, $netflix_id)

Example usage(s):

<? netflix(); ?>

Or setup advanced options:

<ul><? netflix(3, "home", "image", "small", "<li>", "</li>", "P11111111111111111111111111111111"); ?></ul>



FUNCTION: netflix_movies($number_movies, $feed, $netflix_id)

Example usage:

<? $movies = netflix_movies(5, "queue"); ?>
<table>
  <tr>
<? foreach ($movies as $movie) { ?>
    <td><?=$movie->get_cover_image()?></td>
<? } ?>
  </tr>
</table>

Live Examples: http://www.albertbanks.com/movies/


== Variables ==

# $number_movies - number of feed elements displayed (default is 10)

# $feed - personalized feed to use
   ''home'' - displays movies you have at home (default)
   ''queue'' - displays your movie queue in order
   ''recommendations'' - displays your recommended movies
   ''recent'' - display recent activity

# $display_type - what to display. 
    ''title'' - stips text to just movie title (default)
    ''raw'' - displays raw text from feed
    ''image'' - display recent activity

# $image_size - if $type is set to ''image'' this defines the image size
    ''small'' - small image (64px X 90px) (default)
    ''large'' - large image (110px X 154px) 

# $before - html appearing before each entity

# $after - html appearing after each entity

# $netflix_id - specify your netflix id
