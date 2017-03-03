<?php
/**
 * Admin functionality.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PingPong_Admin
 *
 * Admin functionality.
 *
 * @since {{VERSION}}
 */
class PingPong_Admin {

	/**
	 * PingPong_Admin constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		if ( isset( $_GET['create_page'] ) ) {

			add_action( 'admin_init', array( $this, 'create_page' ) );
		}
	}

	function admin_menu() {

		add_options_page(
			__( 'Ping Pong', 'pingpong' ),
			__( 'Ping Pong', 'pingpong' ),
			'manage_options',
			'pingpong',
			array( $this, 'settings_page' )
		);
	}

	function register_settings() {

		register_setting( 'pingpong', 'pingpong_pages' );
	}

	function create_page() {

		$page = $_GET['create_page'];

		$ID = wp_insert_post( array(
			'post_type'   => 'page',
			'post_title'  => pingpong_get_page_title( $page ),
			'post_status' => 'publish',
		) );

		$pages = get_option( 'pingpong_pages', pingpong_get_pages_list() );

		$pages[ $page ] = $ID;

		update_option( 'pingpong_pages', $pages );

		wp_redirect( admin_url( 'options-general.php?page=pingpong' ) );
		exit();
	}

	function settings_page() {

		flush_rewrite_rules();

		$pages = get_option( 'pingpong_pages' );

		if ( ! $pages ) {

			$pages = pingpong_get_pages_list();
		}

		foreach ( $pages as $page => $ID ) {

			if ( get_post_status( $ID ) != 'publish' ) {

				$pages[ $page ] = null;
			}
		}

		include_once PINGPONG_DIR . 'admin/views/settings-page.php';
	}
}