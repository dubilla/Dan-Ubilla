	<div id="sidebar">
		<ul>
          	
          	<li>
          		<h2>What I'm Up to:</h2>
          		<?php twitter_feed_dan(); ?>
          	</li>
<!--          	<li>
          		<h2>What I'm Listening to:</h2><p />
            	<?php //lastfmrecords_display(); ?> 
            </li> -->
            <li>
            	<h2> What I'm Watching:</h2><p />
            		<ol id="netflix">
            			<?php netflix(); ?>
            		</ol>
            </li>
     	    <?php 	/* Widgetized sidebar, if you have the plugin installed. */
	        if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) :
	        endif;
            ?>

			<!-- Author information is disabled per default. Uncomment and fill in your details if you want to use it.
			<li><h2><?php _e('Author', 'minimalism'); ?></h2>
			<p>A little something about you, the author. Nothing lengthy, just an overview.</p>
			</li>
			-->
<!--
			<?php if ( is_404() || is_category() || is_day() || is_month() ||
						is_year() || is_search() || is_paged() ) {
			?> <li>

			<?php /* If this is a 404 page */ if (is_404()) { ?>
			<?php /* If this is a category archive */ } elseif (is_category()) { ?>
			<p><?php printf(__('You are currently browsing the archives for the %s category.', 'minimalism'), single_cat_title('', false)); ?></p>

			<?php /* If this is a yearly archive */ } elseif (is_day()) { ?>
			<p><?php printf(__('You are currently browsing the <a href="%1$s/">%2$s</a> blog archives for the day %3$s.', 'minimalism'), get_bloginfo('url'), get_bloginfo('name'), get_the_time(__('l, F jS, Y', 'minimalism'))); ?></p>

			<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<p><?php printf(__('You are currently browsing the <a href="%1$s/">%2$s</a> blog archives for %3$s.', 'minimalism'), get_bloginfo('url'), get_bloginfo('name'), get_the_time(__('F, Y', 'minimalism'))); ?></p>

			<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<p><?php printf(__('You are currently browsing the <a href="%1$s/">%2$s</a> blog archives for the year %3$s.', 'minimalism'), get_bloginfo('url'), get_bloginfo('name'), get_the_time('Y')); ?></p>

			<?php /* If this is a monthly archive */ } elseif (is_search()) { ?>
			<p><?php printf(__('You have searched the <a href="%1$s/">%2$s</a> blog archives for <strong>&#8216;%3$s&#8217;</strong>. If you are unable to find anything in these search results, you can try one of these links.', 'minimalism'), get_bloginfo('url'), get_bloginfo('name'), get_search_query()); ?></p>

			<?php /* If this is a monthly archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<p><?php printf(__('You are currently browsing the <a href="%1$s/">%2$s</a> blog archives.', 'minimalism'), get_bloginfo('url'), get_bloginfo('name')); ?></p>

			<?php } ?>

			</li> <?php }?>

			<?php wp_list_pages('title_li=<h2>' . __('Pages', 'minimalism') . '</h2>' ); ?>

			<li><h2><?php _e('Archives', 'minimalism'); ?></h2>
				<ul>
				<?php wp_get_archives('type=monthly'); ?>
				</ul>
			</li>

			<?php wp_list_categories('show_count=1&title_li=<h2>' . __('Categories', 'minimalism') . '</h2>'); ?>

			<?php /* If this is the frontpage */ if ( is_home() || is_page() ) { ?>

				</li>
			<?php } ?>
		-->
		</ul>
	</div>