<?php
/**
 * Database functions.
 *
 * @since {{VERSION}}
 *
 * @package PingPong
 * @subpackage PingPong/core
 */

defined( 'ABSPATH' ) || die;

/**
 * Class PingPong_DB
 *
 * Database functions.
 *
 * @since {{VERSION}}
 */
class PingPong_DB {

	/**
	 * Gets a player's rankings.
	 *
	 * @since {{VERSION}}
	 *
	 * @param string $player_ID
	 *
	 * @return array|null|object|void
	 */
	public static function get_rankings( $player_ID ) {

		global $wpdb;

		$results = $wpdb->get_row(
			"
			SELECT *
			FROM {$wpdb->prefix}pingpong_rankings
			WHERE player_ID = '$player_ID'
			",
			ARRAY_A );

		$rankings = $results ? $results : pingpong_rankings_schema();

		/**
		 * Filters the get_rankings results.
		 *
		 * @since {{VERSION}}
		 */
		$rankings = apply_filters(
			'pingpong_db_get_player_rankings',
			$rankings,
			$player_ID
		);

		return $rankings;
	}

	/**
	 * Gets all players' rankings.
	 *
	 * @since {{VERSION}}
	 *
	 * @param array $args
	 *
	 * @return array|null|object|void
	 */
	public static function get_all_rankings( $args ) {

		global $wpdb;

		$args = wp_parse_args( $args, array(
			'orderby'  => 'plusminus',
			'order'    => 'DESC',
			'offset'   => 0,
			'per_page' => 10,
		) );

		$results = $wpdb->get_results(
			"
			SELECT *
			FROM {$wpdb->prefix}pingpong_rankings
			ORDER BY $args[orderby] $args[order]
			LIMIT $args[per_page] OFFSET $args[offset]
			",
			ARRAY_A );

		/**
		 * Filters the get_all_rankings results.
		 *
		 * @since {{VERSION}}
		 */
		$results = apply_filters(
			'pingpong_db_get_players_rankings',
			$results
		);

		return $results;
	}

	/**
	 * Updates or adds a player's rankings.
	 *
	 * @since {{VERSION}}
	 *
	 * @param int $player_ID
	 * @param array $rankings
	 *
	 * @return array|null|object|void
	 */
	public static function update_player_rankings( $player_ID, $rankings ) {

		global $wpdb;

		/**
		 * Filters the rankings to update/add.
		 *
		 * @since {{VERSION}}
		 */
		$rankings = apply_filters( 'pingpong_db_update_player_rankings', $rankings, $player_ID );

		// Update if exists
		if ( self::get_rankings( $player_ID ) ) {

			$result = $wpdb->update(
				"{$wpdb->prefix}pingpong_rankings",
				$rankings,
				array(
					'player_ID' => $player_ID,
				),
				array_fill( 0, count( $rankings ), '%d' ),
				array(
					'%d',
				)
			);

		} else {

			$rankings['player_ID'] = $player_ID;

			$result = $wpdb->insert(
				"{$wpdb->prefix}pingpong_rankings",
				$rankings,
				array_fill( 0, count( $rankings ) + 1, '%d' )
			);
		}

		if ( $result === 1 ) {

			return $wpdb->insert_id;

		} else {

			return $result;
		}
	}
}

/**
 * Quick access to database class.
 *
 * @since {{VERSION}}
 */
function PingPongDB() {

	return PONG()->db;
}