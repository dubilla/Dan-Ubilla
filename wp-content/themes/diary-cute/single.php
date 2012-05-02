<?php get_header(); ?>

	<div id="content">
	<div id="maincontent">
		<div class="topcorner"></div>
		<div class="contentpadding">
	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<small>Posted on <?php the_time('l, F jS, Y') ?> at <?php the_time() ?></small>

				<div class="entry">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>

				<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit'); ?></p>
			</div>

						<div class="ping-track clear">


						<?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
							/* Both Comments and Pings are open*/ ?>
							You can <a href="#respond">leave a response</a>, or <a href="<?php trackback_url(); ?>" rel="trackback">trackback</a> from your own site.

						<?php } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
							/* Only Pings are Open*/ ?>
							Responses are currently closed, but you can <a href="<?php trackback_url(); ?> " rel="trackback">trackback</a> from your own site.

						<?php } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
							/* Comments are open, Pings are not*/ ?>
							You can skip to the end and leave a response. Pinging is currently not allowed.

						<?php } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
							/* Neither Comments, nor Pings are open */ ?>
							Both comments and pings are currently closed.
						<?php } ?>
					</div>	
			<div class="postnav">
			<div class="alignleft"><?php previous_post_link('%link&laquo;') ?></div>
			<div class="alignright"><?php next_post_link('&raquo;%link') ?></div>
			<div class="clear"></div>
			</div>		
		<?php comments_template(); ?>
		

		<?php endwhile; ?>


	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>
			</div>
	<div class="bottomcorner"></div>
	</div>
<?php get_sidebar(); ?>
<div class="clear"></div>
	</div>
<?php get_footer(); ?>
