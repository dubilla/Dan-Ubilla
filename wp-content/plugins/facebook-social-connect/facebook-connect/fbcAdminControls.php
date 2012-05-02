<?php


Class FbcAdminControls {

	function __construct() {}

	public function fbc_general_options() {
		if ( isset($_POST['fbc_submit']) ) {
			check_admin_referer('fbc-config-settings');
			$errors = FbcAdminControls::validate_fbc_configration();
			if( empty($errors) ) {
					// Options for facebook core configration
					update_option( 'fbc_api_key', $_POST['fbc_api_key'] );
					update_option( 'fbc_api_secret', $_POST['fbc_api_secret'] );
					update_option( 'fbc_api_id', $_POST['fbc_api_id'] );

					// Options for facebook social plugin
					update_option( 'fbc_enable_fshare', isset($_POST['add_fshare']) ? true : false );
					update_option( 'fbc_enable_flike', isset($_POST['add_flike']) ? true : false );
					update_option( 'fbc_enable_frecommend', isset($_POST['add_frecommend']) ? true : false );
					update_option('fbc_enable_factivity', isset($_POST['add_factivity']) ? true : false );
					update_option('fbc_enable_fcomments', isset($_POST['add_fcomments']) ? true : false );
			}
	}
	?>
		<style type="text/css">
		.form-table td {
			padding:11px;
			vertical-align:top;

			}
			.form-table td p{
				margin:0px;
				padding:0px 0px 0px 2px;
				color:#ff0000;
			}
		</style>
		<div class="fbc_configs">
				<h2><?php _e('Facebook Connect Options', 'facebook-connect') ?></h2>

				<form method="post" action="" name="Fbc-configration">
					<h3><?php _e('Facebook Application Configuration', 'facebook-connect') ?></h3>
     				<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
						<tr valign="top">
							<th style="width: 33%"><?php _e('Facebook App. Config.', 'Facebook-connect') ?></th>
							<td>
							<a href="http://www.facebook.com/developers/" target="_blank"><?php _e('Go to Facebook Developer App', 'Facebook-connect') ?></a>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%"><label for="fbc_api_key"><?php _e('Facebook API Key:', 'Facebook-connect') ?></label></th>
							<td>
							<input type="text" name="fbc_api_key" id="fbc_api_key" size="50" value="<?php echo get_option('fbc_api_key');?>"/>
							<br />
							<p><?php isset($errors['fbc_api_key']) ? print $errors['fbc_api_key'] : ''; ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%"><label for="fbc_api_secret"><?php _e('Facebook API Secret:', 'Facebook-connect') ?></label></th>
							<td>
							<input type="text" name="fbc_api_secret" size="50" id="fbc_api_secret" value="<?php echo get_option('fbc_api_secret');?>"/>
							<br />
							<p><?php isset($errors['fbc_api_secret']) ? print $errors['fbc_api_secret'] : ''; ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%"><label for="fbc_api_id"><?php _e('Facebook API ID:', 'Facebook-connect') ?></label></th>
							<td>
							<input type="text" name="fbc_api_id" size="50" id="fbc_api_id" value="<?php echo get_option('fbc_api_id');?>"/>
							<br />
							<p><?php isset($errors['fbc_api_id']) ? print $errors['fbc_api_id'] : ''; ?></p>
							</td>
						</tr>
						</table>
						<h3><?php _e('Facebook Social Plugins Configration', 'Facebook-connect') ?></h3>
						<table class="form-table" cellspacing="0" cellpadding="0" width="100%">
						<tr valign="top">
							<th style="width: 33%"><label for="add_fshare"><?php _e('Add fshare button to posts:', 'Facebook-connect') ?></label></th>
							<td>
								<input type="checkbox" name="add_fshare" id="add_fshare" <?php
									echo get_option('fbc_enable_fshare') ? 'checked="checked"' : ''; ?> class="socialcheckbox"/>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%"><label for="add_flike"><?php _e('Add Like button to posts:', 'Facebook-connect') ?></label></th>
							<td><input type="checkbox" name="add_flike" id="add_flike" <?php
									echo get_option('fbc_enable_flike') ? 'checked="checked"' : ''; ?> />
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%"><label for="add_frecommend"><?php _e('Enable Recommendation widget:', 'Facebook-connect') ?></label></th>
							<td><input type="checkbox" name="add_frecommend" id="add_frecommend" <?php
									echo get_option('fbc_enable_frecommend') ? 'checked="checked"' : ''; ?> />
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%"><label for="add_factivity"><?php _e('Enable facebook activity widget:', 'Facebook-connect') ?></label></th>
							<td><input type="checkbox" name="add_factivity" id="add_factivity" <?php
									echo get_option('fbc_enable_factivity') ? 'checked="checked"' : ''; ?> />
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%"><label for="add_fcomments"><?php _e('Enable facebook Comment widget:', 'Facebook-connect') ?></label></th>
							<td><input type="checkbox" name="add_fcomments" id="add_fcomments" <?php
									echo get_option('fbc_enable_fcomments') ? 'checked="checked"' : ''; ?> />
							</td>
						</tr>

     				</table>


					<?php wp_nonce_field('fbc-config-settings'); ?>

     				<p class="submit"><input class="button-primary" type="submit" name="fbc_submit" value="<?php _e('Update Configuration', 'Facebook-connect') ?> &raquo;" /></p>
     			</form>

			</div>
		<?php
	}

	public function validate_fbc_configration() {
		$error = array();
		if($_POST['fbc_api_key'] == '')
			$error['fbc_api_key'] = 'Facebook application key is required.';
		if($_POST['fbc_api_secret'] == '')
			$error['fbc_api_secret'] = 'Facebook application secret key is required.';
		if($_POST['fbc_api_id'] == '')
			$error['fbc_api_id'] = 'Facebook application ID is required.';

		return $error;
	}

}

?>