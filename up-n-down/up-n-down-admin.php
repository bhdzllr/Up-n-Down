<?php

/**
 * Admin class.
 * 
 * Back-End stuff.
 */
class UpNDown_Admin {

	/** @var array $posts   Array for posts ('post', 'page'). */
	private $posts = array();

	/** @var array $options Array for plugin options. */ 
	private $options = array();

	/**
	 * Construct class for hooks.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'stylesnscripts' ) );
	}

	/**
	 * Initialize admin area.
	 */
	public function admin_init() {
		$query = new WP_Query( array( 
			'post_type'      => array( 'post', 'page' ),
			'posts_per_page' => -1,
			'order'          => 'ASC'
		) );

		if ( $query->have_posts() ) { while ( $query->have_posts() ) {
			$query->the_post();
			$this->posts[] = array(
				'ID'    => get_the_ID(),
				'title' => get_the_title()
			);
		} }

		register_setting( 'upndown_options', 'upndown_options', array( $this,'validate_options' ) );

		$this->options = get_option( 'upndown_options', true );
	}

	/**
	 * Add settings page.
	 */
	public function register_settings() {
		add_options_page( 'Up-n-Down', 'Up-n-Down', 'manage_options', 'up-n-down.php', array ( $this, 'render_settings_page' ) );
	}

	/**
	 * Enqueue styles and scripts for backend.
	 */
	public function stylesnscripts() {
		wp_enqueue_style( 'up-n-down-admin', plugin_dir_url( __FILE__ ) . 'css/up-n-down-admin.css' );
	}

	/**
	 * Validate input.
	 */
	public function validate_options( $input ) {
		if ( empty( trim( $input['target_post_id'] ) ) ) {
			$input['target_post_id'] = false;
		} else {
			$input['target_post_id'] = $input['target_post_id'];
		}

		if ( empty( trim( $input['target_dir'] ) ) ) {
			$input['target_dir'] = false;
		} else {
			$input['target_dir'] = trim( $input['target_dir'] );
		}

		return $input;
	}

	/**
	 * Render settings page.
	 */
	public function render_settings_page() {
		include_once 'views/options.php';
	}

}
