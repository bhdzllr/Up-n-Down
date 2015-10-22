<?php
/**
 * Plugin Name: Up-n-Down
 * Plugin URI:  http://github.com/bhdzllr
 * Description: Upload plugin.
 * Version:     1.0.0
 * Author:      bhdzllr
 * Author URI:  http://github.com/bhdzllr
 */

// error_reporting(E_ALL);

// Abort if file is called directly.
if ( ! defined( 'WPINC' ) ) exit;

/**
 * Upload class.
 */
class UpNDown_Main {

	/** @var UpNDown_Main|null Class instance. */
	private static $instance;

	/**
	 * Constructor.
	 */
	private function __construct() {
		register_activation_hook( __FILE__, array ( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array ( $this, 'deactivate' ) );

		if ( is_admin() ) {
			// Back-End
			require_once 'up-n-down-admin.php';

			$upndown_admin = new UpNDown_Admin;
		} else {
			// Front-End
			require_once 'up-n-down-public.php';

			$upndown_public = new UpNDown_Public;
		}
	}

	/**
	 * Return instance if exist, else create one.
	 *
	 * @return UpNDown_Main Upload class.
	 */
	public static function getInstance() {
		if ( ! isset( self::$instance ) )
			self::$instance = new self;

		return self::$instance;
	}

	/**
	 * Forbid clone from outside via `__clone()`.
	 */
	private function __clone() {}
	
	/**
	 * Forbid deserialization from outside via `__wakeup()`.
	 */
	private function __wakeup() {}

	/**
	 * Plugin activation.
	 */
	public function activate() {}

	/**
	 * Plugin deactivation.
	 */
	public function deactivate() {}

}

$upndown_upload = UpNDown_Main::getInstance(); // Run plugin.
