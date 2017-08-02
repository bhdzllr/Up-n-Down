<div class="wrap upndown-wrap">
	<h1>Up-n-Down</h1>

	<p>Append form to post or use shortcode: <code>[Up-n-Down]</code></p>

	<form id="upndown-options" method="post" action="<?php echo admin_url( 'options.php' ); ?>">
		<fieldset>
			<legend>Options</legend>
			<?php 

			settings_fields( 'upndown_options' ); 
			do_settings_sections( 'up-n-down.php' ); 

			?>

			<div>
				<label for="upndown-post-id">Target post id:</label>
				<select name="upndown_options[target_post_id]" id="upndown-target-post-id">
					<option value="0">&mdash; Auswählen &mdash;</option>
				<?php foreach ( $this->posts as $post ) : ?>
					<?php if ( $post['ID'] == $this->options['target_post_id']) : ?>
					<option value="<?php echo $post['ID']; ?>" selected="selected"><?php echo $post['title']; ?></option>
					<?php else: ?>
					<option value="<?php echo $post['ID']; ?>"><?php echo $post['title']; ?></option>
					<?php endif; ?>
				<?php endforeach; ?>
				</select>
			</div>

			<div>
				<label for="upndown-target-dir">Folder name:</label>
				<input type="text" name="upndown_options[target_dir]" id="upndown-target-dir" value="<?php if ( isset( $this->options['target_dir'] ) ) echo $this->options['target_dir']; ?>" placeholder="Uploads folder name ..." />
				<p class="form-help">Stored in WP uploads folder. Do not include leading or trailing slash.</p>
			</div>

			<div>
				<?php if ( isset( $this->options['hide_upload'] ) ) : ?>
				<input type="checkbox" name="upndown_options[hide_upload]" id="upndown-hide-upload" checked="checked" />
				<?php else : ?>
				<input type="checkbox" name="upndown_options[hide_upload]" id="upndown-hide-upload" />
				<?php endif; ?>
				<label for="upndown-hide-upload">Hide upload for public.</label>
			</div>

			<div>
				<?php if ( isset( $this->options['hide_login'] ) ) : ?>
				<input type="checkbox" name="upndown_options[hide_login]" id="upndown-hide-login" checked="checked" />
				<?php else : ?>
				<input type="checkbox" name="upndown_options[hide_login]" id="upndown-hide-login" />
				<?php endif; ?>
				<label for="upndown-hide-login">Hide login form.</label>
			</div>

			<div>
				<?php if ( isset( $this->options['show_files'] ) ) : ?>
				<input type="checkbox" name="upndown_options[show_files]" id="upndown-show-files" checked="checked" />
				<?php else : ?>
				<input type="checkbox" name="upndown_options[show_files]" id="upndown-show-files" />
				<?php endif; ?>
				<label for="upndown-show-files">Show files for public.</label>
			</div>

			<div>
				<?php if ( isset( $this->options['show_admin_bar'] ) ) : ?>
				<input type="checkbox" name="upndown_options[show_admin_bar]" id="upndown-show-admin-bar" checked="checked" />
				<?php else : ?>
				<input type="checkbox" name="upndown_options[show_admin_bar]" id="upndown-show-admin-bar" />
				<?php endif; ?>
				<label for="upndown-show-admin-bar">Show admin bar for subscribers.</label>
			</div>

			<div>
				<?php if ( isset( $this->options['allow_subscribers_delete'] ) ) : ?>
				<input type="checkbox" name="upndown_options[allow_subscribers_delete]" id="upndown-allow-subscribers-delete" checked="checked" />
				<?php else : ?>
				<input type="checkbox" name="upndown_options[allow_subscribers_delete]" id="upndown-allow-subscribers-delete" />
				<?php endif; ?>
				<label for="upndown-allow-subscribers-delete">Allow subscribers to delete files.</label>
			</div>

			<div>
				<p><label for="upndown-mime-types">MIME-Types:</label></p>
				<textarea 
					name="upndown_options[mime_types]"
					id="upndown-mime-types" 
					cols="30"
					rows="3"
					placeholder="Allowed MIME-Types ..."><?php if ( isset( $this->options['mime_types'] ) ) echo implode( ',', $this->options['mime_types'] ); ?></textarea>
				<p class="form-help">Allowed MIME-Types, separated with comma.</p>
			</div>

			<?php submit_button(); ?>
		</fieldset>
	</form>
</div>