	<div id="sidebar">
	<div id="sidebar-top">
	
		 <ul class="tabs">
            <li><a href="#" title="recent-posts" class="tab active texthidden" id="tabnewpost"><span>Recent post</span></a></li>
            <li><a href="#" title="categories" class="tab texthidden" id="tabcomments"><span>Categories</span></a></li>
            <li><a href="#" title="archives" class="tab" id="tabarchives"><span>Archives</span></a></li>
        </ul>
        <div class="tabshow-t"></div>
        <div id="recent-posts" class="contentlist">
					<?php query_posts('showposts=5'); ?>
					<ul>
					<?php while (have_posts()) : the_post(); ?>
						<li>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a>
						</li>
					<?php endwhile; ?>
					</ul>
        </div>
        <div id="categories" class="contentlist">
			<ul>
			<?php wp_list_categories('show_count=0&title_li=0'); ?>
			</ul>
        </div>
        <div id="archives" class="contentlist">
			<ul>
				<?php wp_get_archives('type=monthly'); ?>
				</ul>
        </div>
<div class="tabshow-b"></div>
	
	</div>
	<div id="sidebar-left">

			<?php 	/* Widgetized sidebar, if you have the plugin installed. */
					if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
					
<div class="tabshow1-t"></div>
<div class="contentlist1">
<h2>Meta</h2>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
					<?php wp_meta(); ?>
				</ul>
</div>
<div class="tabshow1-b"></div>
<!--
			<?php /* If this is the frontpage */ if ( is_home() || is_page() ) { ?>
<div class="tabshow-t"><h2>Links</h2></div>
<div class="contentlist">
					<ul>
						<?php get_links('-1', '<li>', '</li>', '', FALSE, 'id', FALSE, FALSE, -1, FALSE); ?>
					</ul>
			
</div>
<div class="tabshow-b"></div>

<?php } ?>

<div class="tabshow1-t"></div>
<div class="contentlist1">
<?php include (TEMPLATEPATH . "/searchform.php"); ?>		
</div>
<div class="tabshow1-b"></div>
-->
			<?php endif; ?>


	</div>

	</div>