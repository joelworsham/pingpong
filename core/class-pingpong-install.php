<?php
/**
 * Installs the plugin.
 *
 * @since {{VERSION}}
 *
 * @package PingPong
 * @subpackage PingPong/core
 */

defined( 'ABSPATH' ) || die;

/**
 * Class PingPong_Install
 *
 * Installs the plugin.
 *
 * @since {{VERSION}}
 */
class PingPong_Install {

	/**
	 * Loads the install functions.
	 *
	 * @since {{VERSION}}
	 */
	static function install() {

		add_option( 'pingpong_db_version', '1.0.0' );

		self::setup_tables();
	}

	/**
	 * Sets up the tables.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @global wpdb $wpdb
	 */
	private static function setup_tables() {

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$wpdb->prefix}pingpong_rankings (
		  player_id mediumint(9) NOT NULL,
		  points_won int,
		  points_lost int,
		  games_won int,
		  games_lost int,
		  matches_won int,
		  matches_lost int,
		  matches_played int,
 		  PRIMARY KEY  (player_id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}