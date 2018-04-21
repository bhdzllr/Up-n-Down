<?php

/**
 * Public class.
 * 
 * Front-End stuff.
 */
class UpNDown_Public {

	/**
	 * @var int Post id of current post.
	 * @var string Full URL to uploaded file (the permalink).
	 */
	private $post_id;
	private $permanlink;

	/**
	 * @var string $site_url_up   WordPress relative site URL.
	 * @var string $site_url_down WordPress absolute site URL.
	 */
	private $site_url_up;
	private $site_url_down;

	/** @var arry Array for plugin options */ 
	private $options = array();

	/**
	 * @var int Post ID to append the upload.
	 * @var string Name of upload directory.
	 * @var string Path to upload directory.
	 **/
	private $target_post_id;
	private $target_dir;
	private $target_dir_path;

	/** @var array Array for already uploaded files in target directory. */
	public $files = array();

	/** @var array Array for user messages. */
	public $messages = array();

	/**
	 * Construct class for hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'the_post', array( $this, 'get_post_data' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'stylesnscripts' ) );

		add_filter( 'the_content', array( $this, 'append_upload' ) );

		add_shortcode( 'Up-n-Down', array( $this, 'upndown_shortcode' ) );
		add_shortcode( 'up-n-down', array( $this, 'upndown_shortcode' ) );
	}

	/**
	 * Initialize
	 */
	public function init() {
		$this->options = get_option( 'upndown_options', true );
		$upload_dir = wp_upload_dir();

		$this->site_url_up = $upload_dir['basedir'];
		$this->site_url_down = $upload_dir['baseurl'];

		$this->target_post_id = ( empty( $this->options['target_post_id'] ) ? 0           : $this->options['target_post_id'] );
		$this->target_dir     = ( empty( $this->options['target_dir'] )     ? 'up-n-down' : $this->options['target_dir']     );

		$this->target_dir_path = $this->site_url_up . '/' . $this->target_dir;

		$this->user_login();

		if ( isset( $_GET['upndown-delete-file'] ) && current_user_can( 'delete_pages' ) )
			$this->delete_file( $_GET['upndown-delete-file'] );
	}

	/**
	 * User login.
	 */
	public function user_login() {
		if ( isset( $_POST['upndown-submit-login'] ) ) {
			$credentials = array(
				'user_login'    => $_POST['upndown-username'],
				'user_password' => $_POST['upndown-password'],
				'remember'      => true
			);
			
			$user = wp_signon( $credentials, true );

			if ( ! is_wp_error( $user ) ) {
				wp_set_current_user( $user->ID );
			} else {
				$this->messages[] = array(
					'type' => 'error',
					'text' => $user->get_error_message()
				);
			}
		} else if ( isset( $_POST['upndown-submit-logout'] ) ) {
			wp_logout();
			wp_set_current_user( 0 );
		}

		if ( ! isset( $this->options['show_admin_bar'] ) && ! current_user_can( 'edit_posts' ) )
			show_admin_bar( false );
	}

	/**
	 * Get data form current post and store id in member variable.
	 *
	 * @param  WP_Post $post Post object.
	 * @return boolean       True.
	 */
	public function get_post_data( $post ) {
		$this->post_id = $post->ID;
		$this->permalink = get_permalink( $this->post_id );

		return true;
	}

	/**
	 * Enqueue styles and scripts for backend.
	 */
	public function stylesnscripts() {
		wp_enqueue_style( 'up-n-down-public', plugin_dir_url( __FILE__ ) . 'css/up-n-down-public.css' );
	}

	/**
	 * Append upload form to the content.
	 *
	 * @param  string $content Content of the current post.
	 * @return string          Content of the current post.
	 */
	public function append_upload( $content ) {
		if ( $this->post_id != $this->target_post_id || $this->target_post_id == 0 )
			return $content;

		return $this->prepare_upload( $content );
	}

	/**
	 * Prepare upload.
	 *
	 * @param  string $content Content of the current post (optional).
	 * @return string          Content with appended form.
	 */
	private function prepare_upload( $content = null ) {
		if ( ! is_dir( $this->target_dir_path ) )
			mkdir( $this->target_dir_path, 0755, true);

		if ( isset( $_POST['upndown-submit-upload'] ) )
			$this->upload();

		$this->load_files();

		return $this->render( $content );
	}

	/**
	 * Upload files.
	 *
	 * @link http://php.net/manual/en/features.file-upload.php Documentation and source of code.
	 */
	private function upload() {
		try {
			if ( ! isset( $_FILES['upndown-file']['error'] )
				|| is_array( $_FILES['upndown-file']['error'] )
			) {
				throw new RuntimeException( 'Invalid parameters.' );
			}

			switch ( $_FILES['upndown-file']['error'] ) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					throw new RuntimeException( 'No file sent.' );
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new RuntimeException( 'Exceeded filesize limit.' );
				default:
					throw new RuntimeException( 'Unknown errors.' );
			}

			$finfo = new finfo(FILEINFO_MIME_TYPE);		
			if ( false === $ext = array_search(
				$finfo->file( $_FILES['upndown-file']['tmp_name'] ),
				$this->options['mime_types'],
				true
			) ) {
				throw new RuntimeException( 'Invalid file format.' );
			}

			if ( file_exists( $this->target_dir_path . '/' . $_FILES['upndown-file']['name'] ) 
				|| is_uploaded_file( $this->target_dir_path . '/' . $_FILES['upndown-file']['name'] )
			) {
				throw new RuntimeException( 'File with same name already exists.' );
			}

			if ( ! move_uploaded_file(
				$_FILES['upndown-file']['tmp_name'], 
				$this->target_dir_path . '/' . $_FILES['upndown-file']['name'] // New file name
			) ) {
				throw new RuntimeException( 'Failed to move uploaded file.' );
			}

			$this->messages[] = array(
				'type' => 'success',
				'text' => 'File uploaded successfully.'
			);
		} catch ( RuntimeException $e ) {
			$this->messages[] = array(
				'type' => 'error',
				'text' => $e->getMessage()
			);
		}
	}

	/**
	 * Load files from target folder.
	 *
	 * @return void
	 */
	private function load_files() {
		if ( is_dir( $this->target_dir_path ) ) {
			if ( $handler = opendir( $this->target_dir_path ) ) {
				while ( ( $file = readdir( $handler ) ) !== false ) {
					if( $file != '.' && $file != '..' ) {
						$filename = $file;
	
						if ( empty( $this->target_dir ) ) {
							$filepath = $this->site_url_down . '/' . $file;
						} else {
							$filepath = $this->site_url_down . '/' . $this->target_dir . '/' . $file;
						}
	
						$this->files[] = array(
							'name' => $filename,
							'path' => $filepath
						);
					}
				}

				closedir( $handler );
			}
		}

		return;
	}

	/**
	 * Delete file.
	 *
	 * @return boolean True for success, false for error.
	 */
	private function delete_file( $file ) {
		if ( file_exists( $this->target_dir_path . '/' . $file ) )
			return unlink( $this->target_dir_path . '/' . $file );

		return false;
	}

	/**
	 * Shortcode.
	 *
	 * @return string Page content with upload form.
	 */
	public function upndown_shortcode() {
		return $this->prepare_upload();
	}

	/**
	 * Render the content.
	 *
	 * @return string Content with appended form.
	 */
	private function render( $content ) {
		ob_start();
		
		include_once 'templates/upload-form.php';
		$output = ob_get_clean();
		$content .= $output;

		return $content;
	}

	/**
	 * Create message classes.
	 *
	 * @return string HTML attribute.
	 */
	private function msg_class() {
		$classes = implode(' ', array_map( function( $msg ) {
			return 'upndown-' . $msg['type'];
		}, $this->messages));

		return 'class="' . $classes . '"';
	}

}
