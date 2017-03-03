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
		 * Database functions.
		 *
		 * @since {{VERSION}}
		 *
		 * @var PingPong_DB
		 */
		public $db;

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

		/**
		 * Creates the "League" custom post type.
		 *
		 * @since {{VERSION}}
		 *
		 * @var PingPong_CPT_League
		 */
		public $cpt_league;

		/**
		 * Creates the Rankings.
		 *
		 * @since {{VERSION}}
		 *
		 * @var PingPong_Rankings
		 */
		public $rankings;

		/**
		 * Creates the shortcodes.
		 *
		 * @since {{VERSION}}
		 *
		 * @var PingPong_Shortcodes
		 */
		public $shortcodes;

		/**
		 * Creates the profiles.
		 *
		 * @since {{VERSION}}
		 *
		 * @var PingPong_Profiles
		 */
		public $profiles;

		/**
		 * Admin functionality.
		 *
		 * @since {{VERSION}}
		 *
		 * @var PingPong_Admin
		 */
		public $admin;

		private function __clone() {
		}

		private function __wakeup() {
		}

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
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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
			require_once PINGPONG_DIR . 'core/class-pingpong-cpt-league.php';
			require_once PINGPONG_DIR . 'core/class-pingpong-shortcodes.php';
			require_once PINGPONG_DIR . 'core/includes/class-pingpong-shortcode.php';
			require_once PINGPONG_DIR . 'core/class-pingpong-db.php';
			require_once PINGPONG_DIR . 'core/class-pingpong-profiles.php';
			require_once PINGPONG_DIR . 'core/class-pingpong-rankings.php';
			require_once PINGPONG_DIR . 'admin/class-pingpong-admin.php';

			$this->roles      = new PingPong_Roles();
			$this->db         = new PingPong_DB();
			$this->cpt_match  = new PingPong_CPT_Match();
			$this->cpt_team   = new PingPong_CPT_Team();
			$this->cpt_league = new PingPong_CPT_League();
			$this->shortcodes = new PingPong_Shortcodes();
			$this->profiles   = new PingPong_Profiles();
			$this->rankings   = new PingPong_Rankings();
			$this->admin   = new PingPong_Admin();
		}

		/**
		 * Registers all plugin scripts.
		 *
		 * @since {{VERSION}}
		 * @access private
		 */
		function register_scripts() {

			wp_register_style(
				'pingpong',
				PINGPONG_URI . '/assets/dist/css/pingpong.min.css',
				array(),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PINGPONG_VERSION
			);

			wp_register_style(
				'pingpong-admin',
				PINGPONG_URI . '/assets/dist/css/pingpong-admin.min.css',
				array(),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PINGPONG_VERSION
			);

			wp_register_script(
				'pingpong',
				PINGPONG_URI . '/assets/dist/js/pingpong.min.js',
				array( 'select2', 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PINGPONG_VERSION
			);

			wp_register_script(
				'pingpong-admin',
				PINGPONG_URI . '/assets/dist/js/pingpong-admin.min.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PINGPONG_VERSION
			);

			wp_register_style(
				'pingpong-select2',
				PINGPONG_URI . '/vendor/select2/select2.min.css',
				array(),
				'4.0.3'
			);

			wp_register_script(
				'pingpong-select2',
				PINGPONG_URI . '/vendor/select2/select2.full.min.js',
				array( 'jquery' ),
				'4.0.3'
			);

			/**
			 * Filters the data for localization to JS.
			 *
			 * @since {{VERSION}}
			 */
			$data = apply_filters( 'pingpong_admin_script_data', array(
				'l10n' => array(
					'cant_load_scores'            => __( 'Could not load previous scores.', 'pingpong' ),
					'game'                        => __( 'Game', 'pingpong' ),
					'totals'                      => __( 'Totals', 'pingpong' ),
					'error_too_many_team_players' => __( 'Error: Each team can only have 2 players for a Team Match.', 'pingpong' ),
					'could_not_save_match'        => __( 'Could not save match scores.', 'pingpong' ),
					'confirm_submit_match'        => __( 'Are you sure you want to submit this match? You cannot edit this later.', 'pingpopn' ),
					'view_matches'                => __( 'View Matches', 'pingpong' ),
					'hide_matches'                => __( 'Hide Matches', 'pingpong' ),
				),
			) );

			wp_localize_script( 'pingpong-admin', 'PingPong_Admin', $data );
		}

		/**
		 * Enqueue scripts.
		 *
		 * @since {{VERSION}}
		 * @access private
		 */
		function enqueue_scripts() {

			wp_enqueue_style( 'pingpong' );
			wp_enqueue_script( 'pingpong' );
			wp_enqueue_style( 'pingpong-select2' );
			wp_enqueue_script( 'pingpong-select2' );
		}

		/**
		 * Enqueue admin scripts.
		 *
		 * @since {{VERSION}}
		 * @access private
		 */
		function admin_enqueue_scripts() {

			wp_enqueue_style( 'pingpong-admin' );
			wp_enqueue_script( 'pingpong-admin' );
		}
	}

	register_uninstall_hook( __FILE__, array( 'PingPong_Roles', 'remove_roles' ) );

	// Installation
	require_once PINGPONG_DIR . 'core/class-pingpong-install.php';
	register_activation_hook( __FILE__, array( 'PingPong_Install', 'install' ) );

	require_once PINGPONG_DIR . 'core/pingpong-functions.php';
	require_once PINGPONG_DIR . 'pingpong-bootstrapper.php';

	new PingPong_Bootstrapper();
}