<?php if (function_exists('wp_list_comments')) :?>
<?php
// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<p class="nocomments">This post is password protected. Enter the password to view comments.</p>
	<?php
		return;
	}
?>

<!-- You can start editing here. -->

<?php if ( have_comments() ) : ?>
	<h3 id="comments"><?php comments_number('No Responses', 'One Response', '% Responses' );?> to &#8220;<?php the_title(); ?>&#8221;</h3>

	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>
<div class="clear"></div>
	<ol class="commentlist">
	<?php wp_list_comments(); ?>
	</ol>

	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>
	<div class="clear"></div>
 <?php else : // this is displayed if there are no comments so far ?>

	<?php if ('open' == $post->comment_status) : ?>
		<!-- If comments are open, but there are no comments. -->

	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments">Comments are closed.</p>

	<?php endif; ?>
<?php endif; ?>


<?php if ('open' == $post->comment_status) : ?>

<div id="respond">

<h3><?php comment_form_title( 'Leave a Reply', 'Leave a Reply to %s' ); ?></h3>

<div class="cancel-comment-reply">
	<small><?php cancel_comment_reply_link(); ?></small>
</div>

<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">logged in</a> to post a comment.</p>
<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

<?php if ( $user_ID ) : ?>

<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account">Log out &raquo;</a></p>

<?php else : ?>

<p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> />
<label for="author"><small>Name <?php if ($req) echo "(required)"; ?></small></label></p>

<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />
<label for="email"><small>Mail (will not be published) <?php if ($req) echo "(required)"; ?></small></label></p>

<p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
<label for="url"><small>Website</small></label></p>

<?php endif; ?>

<!--<p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->

<p><textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea></p>

<p><input name="submit" type="submit" id="submit" tabindex="5" value="Submit" />
<?php comment_id_fields(); ?>
</p>
<?php do_action('comment_form', $post->ID); ?>

</form>

<?php endif; // If registration required and not logged in ?>
</div>

<?php endif; // if you delete this the sky will fall on your head ?>
<?php else : ?>


<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
			?>

			<p class="nocomments">This post is password protected. Enter the password to view comments.</p>

			<?php
			return;
		}
	}

	/* This variable is for alternating comment background */
	$oddcomment = 'alt';
?>

<!-- You can start editing here. -->

				<div id="comments" class="post-1">
<?php if ($comments) : ?>
			<?php /* Count the totals */
				$numPingBacks = 0;
				$numComments  = 0;
				foreach ($comments as $comment) {
					if (get_comment_type() != "comment") {
						$numPingBacks++;
					} else {
						$numComments++;
					}
				}
			?>
			<?php if ($numComments != 0) : ?>
					<h4><?php if ($numComments == 1) { echo 'One comment'; } else { echo $numComments; echo ' comments'; } ?></h4>

	<?php $commentnumber = 1?>
	<?php foreach ($comments as $comment) : ?>
				<?php if (get_comment_type() == 'comment'){ ?>
					<div id="comment-<?php comment_ID() ?>" class="<?php if ($comment->comment_author_email == get_the_author_email()) echo 'author'; else echo $oddcomment; ?> message-content">
							<div class="comleft">
							<div class="message-count"><span><b class="count-l">&nbsp;</b><a href="#comment-<?php comment_ID() ?>"><?php echo $commentnumber; $commentnumber++;?></a><b class="count-r">&nbsp;</b></span></div>
							
							<div class="avatar-place"><?php echo get_avatar( $comment, 40 ); ?></div>
							<div class="message-by"><?php comment_author_link() ?>:</div>

							
							
							
							</div>
							
							<div class="message-entry font-resize">
								<?php if ($comment->comment_approved == '0') : ?>
								<em>Your comment is awaiting moderation.</em>
								<?php endif; ?>

								<?php comment_text() ?>
								
								<div class="message-time"><span><?php comment_date('F jS, Y') ?> at <?php comment_time() ?></span></div>
								
							</div>
							<div class="clear"></div>
						</div>
					
	<?php /* Changes every other comment to a different class */	
		if ('alt' == $oddcomment) $oddcomment = '';
		else $oddcomment = 'alt';
	?>
				<?php } /* End of is_comment statement */ ?>

	<?php endforeach; /* end for each comment */ ?>
			<?php endif; ?>
		
			<?php if ($numPingBacks != 0) : ?>
					<h4 id="trackbacks"><?php if ($numPingBacks == 1) { echo 'One Trackback/Ping'; } else { echo $numPingBacks; echo ' Trackbacks/Pings'; } ?></h4>
					<ol class="commentlist">
				<?php foreach ($comments as $comment) : ?>
					<?php if (get_comment_type() != 'comment'){ ?>
						<li class="<?php echo $oddcomment; ?>ping" id="comment-<?php comment_ID() ?>">
							<cite>
								<span class="author-ping b"><?php comment_author_link() ?>&nbsp;&nbsp;&nbsp;</span>
								<span class="date-ping"><?php comment_date('M d Y') ?> / <?php comment_date('ga') ?></span>
							</cite>
						</li>
						<?php /* Changes every other comment to a different class */
						if ('alt' == $oddcomment) $oddcomment = '';
						else $oddcomment = 'alt';
						?>
					<?php } ?>
				<?php endforeach; /* end for each comment */ ?>
					</ol>
			<?php endif; ?>

 <?php else : ?>

  <?php if ('open' == $post-> comment_status) : ?> 
		<!-- If comments are open, but there are no comments. -->
		
	 <?php else : /* comments are closed */ ?>
		<!-- If comments are closed. -->
					<p class="nocomments">Comments are closed at this time.</p>
				</div>
	<?php endif; ?>
<?php endif; ?>
<div class="backtotop"><a class="scroll" href="#menu">Back to top</a></div>

<?php if ('open' == $post->comment_status) : ?>
					<div id="response">
						<h4 id="respond">Leave a reply</h4>
						<div class="form">
<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">logged in</a> to post a comment.</p>
<?php else : ?>
							<form  action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
								<div class="inputs">
<?php if ( $user_ID ) : ?>

<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Log out of this account">Logout »</a></p>

<?php else : ?>

									<div class="input">Name <?php if ($req) _e('(<b>*</b>)'); ?></div>
									<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
									<div class="input">Mail (will not be published) <?php if ($req) _e('(<b>*</b>)'); ?></div>
									<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
									<div class="input">URI</div>
									<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
<?php endif; ?>
								</div>
								<div class="message">

									<div class="input">Comment</div>
									<textarea name="comment" id="comment" cols="40" rows="10" tabindex="4"></textarea>
								</div>
								<div class="clear"></div>
								<div class="submit"><input type="submit" name="submit" id="submit" tabindex="5" value="Submit" /><input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" /></div>
								<?php do_action('comment_form', $post->ID); ?>
							</form>
							
<?php endif;  ?>
						</div>	
					</div>
				</div>
				
<?php endif; ?>




<?php endif; // if you delete this the sky will fall on your head ?>