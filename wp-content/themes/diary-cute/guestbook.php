<?php 
/*
Template Name: Guestbook
*/
?>
<?php get_header(); ?>

	<div id="content">
	<div id="maincontent">
		<div class="topcorner"></div>
		<div class="contentpadding">
	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				

				<div class="entry">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>

				
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
