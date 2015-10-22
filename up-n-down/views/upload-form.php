<?php foreach ( $this->messages as $message ) : ?>
<p <?php echo $this->msg_class(); ?>><?php echo $message['text']; ?></p>
<?php endforeach; ?>

<?php if ( is_user_logged_in() || ! isset( $this->options['hide_upload'] ) ) : ?>
<form id="upndown-upload-form" method="post" action="<?php echo $this->permalink; ?>" enctype="multipart/form-data">
	<fieldset>
		<legend>Upload</legend>
	
		<input type="file" name="upndown-file" id="upndown-file" />
		<input type="submit" name="upndown-submit-upload" id="upndown-submit-upload" value="Upload" />
	</fieldset>
</form>
<?php endif; ?>

<?php if ( is_user_logged_in() || isset( $this->options['show_files'] ) ) : ?>
<ul>
<?php foreach ( $this->files as $file ) : ?>
	<li>
		<a href="<?php echo $file['path']; ?>" title="Download <?php echo $file['name']; ?>" download="<?php echo $file['name']; ?>"><?php echo $file['name']; ?></a>
		<?php if ( current_user_can( 'delete_pages' ) ) : ?>
		<a href="<?php echo $this->permalink; ?>?upndown-delete-file=<?php echo $file['name']; ?>" title="Delete <?php echo $file['name']; ?>">Delete</a>
		<?php endif; ?>
	</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if ( ! is_user_logged_in() && ! isset( $this->options['hide_login'] ) ) : ?>
<form id="upndown-login-form" method="post" action="<?php echo $this->permalink; ?>">
	<fieldset>
		<legend>User Action</legend>

		<label for="upndown-username">Username: </label>
		<input type="text" name="upndown-username" id="upndown-username" placeholder="Username ..."/>

		<label for="upndown-password">Password: </label>
		<input type="password" name="upndown-password" id="upndown-password" placeholder="Password ..."/>

		<input type="submit" name="upndown-submit-login" id="upndown-submit-login" value="Login" />
	</fieldset>
</form>
<?php endif; ?>

<?php if ( is_user_logged_in() ) : ?>
<form id="upndown-logout-form" method="post" action="<?php echo $this->permalink; ?>">
	<fieldset>
		<legend>User Action</legend>

		<input type="submit" name="upndown-submit-logout" id="upndown-submit-logout" value="Logout" />
	</fieldset>
</form>
<?php endif; ?>