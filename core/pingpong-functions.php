<?php
/**
 * Helper functions.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Returns the main plugin class.
 *
 * @since {{VERSION}}
 *
 * @return PingPong
 */
function PONG() {

	return PingPong::getInstance();
}

/**
 * Gets players of a team.
 *
 * @since {{VERSION}}
 *
 * @param int $team_ID Team to retrieve players from.
 *
 * @return array Players.
 */
function pingpong_get_team_players( $team_ID ) {

	$players = rbm_get_field( 'players', $team_ID );

	/**
	 * Team players.
	 *
	 * @since {{VERSION}}
	 */
	$players = apply_filters( 'pingpong_team_players', $players );

	return $players;
}