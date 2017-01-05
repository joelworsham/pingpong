<?php
/**
 * Plugin Name: Ping Pong
 * Description: Creates all of the required Ping Pong functionality.
 * Version: 0.1.0
 * Author: Joel Worsham
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'PingPong' ) ) {

	define( 'PINGPONG_VERSION', '0.1.0' );
	define( 'PINGPONG_DIR', plugin_dir_path( __FILE__ ) );
	define( 'PINGPONG_URI', plugins_url( '', __FILE__ ) );

	/**
	 * Class PingPong
	 *
	 * The main plugin class.
	 *
	 * @since {{VERSION}}
	 */
	final class PingPong {

		/**
		 * Handles custom roles.
		 *
		 * @since {{VERSION}}
		 *
		 * @var PingPong_Roles
		 */
		public $roles;

		/**
		 * Creates the "Match" custom post type.
		 *
		 * @since {{VERSION}}
		 *
		 * @var PingPong_CPT_Match
		 */
		public $cpt_match;

		/**
		 * Creates the "Team" custom post type.
		 *
		 * @since {{VERSION}}
		 *
		 * @var PingPong_CPT_Team
		 */
		public $cpt_team;

		private function __clone() { }

		private function __wakeup() { }

		/**
		 * Returns the *Singleton* instance of this class.
		 *
		 * @since {{VERSION}}
		 *
		 * @staticvar Singleton $instance The *Singleton* instances of this class.
		 *
		 * @return PingPong The *Singleton* instance.
		 */
		public static function getInstance() {

			static $instance = null;

			if ( null === $instance ) {

				$instance = new static();
			}

			return $instance;
		}

		/**
		 * PingPong_CPT constructor.
		 *
		 * @since {{VERSION}}
		 */
		function __construct() {

			$this->init();

			add_action( 'init', array( $this, 'register_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_filter( 'rbm_load_select2', '__return_true' );
		}

		/**
		 * Initializes all plugin files and modules.
		 *
		 * @since {{VERSION}}
		 * @access private
		 */
		private function init() {

			require_once PINGPONG_DIR . 'core/class-pingpong-roles.php';
			require_once PINGPONG_DIR . 'core/class-pingpong-cpt-match.php';
			require_once PINGPONG_DIR . 'core/class-pingpong-cpt-team.php';

			$this->roles = new PingPong_Roles();
			$this->cpt_match = new PingPong_CPT_Match();
			$this->cpt_team = new PingPong_CPT_Team();
		}

		/**
		 * Registers all plugin scripts.
		 *
		 * @since {{VERSION}}
		 * @access private
		 */
		function register_scripts() {

			wp_register_script(
				'pingpong-admin',
				PINGPONG_URI . '/assets/dist/js/ld-gb-admin.min.js',
				array(),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PINGPONG_VERSION
			);
		}

		/**
		 * Enqueue admin scripts.
		 *
		 * @since {{VERSION}}
		 * @access private
		 */
		function admin_enqueue_scripts() {

			wp_enqueue_script( 'pingpong-admin' );
		}
	}

	register_uninstall_hook( __FILE__, array( 'PingPong_Roles', 'remove_roles' ) );

	require_once PINGPONG_DIR . 'core/pingpong-functions.php';
	require_once PINGPONG_DIR . 'pingpong-bootstrapper.php';

	new PingPong_Bootstrapper();
}