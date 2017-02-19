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

/**
 * Returns the rankings schema.
 *
 * @since {{VERSION}}
 *
 * @return array
 */
function pingpong_rankings_schema() {

	return array(
		'points_won'     => 0,
		'points_lost'    => 0,
		'games_won'      => 0,
		'games_lost'     => 0,
		'matches_won'    => 0,
		'matches_lost'   => 0,
		'matches_played' => 0,
	);
}

/**
 * Saves the scores to rankings.
 *
 * @since {{VERSION}}
 *
 * @param array $player1
 * @param array $player2
 */
function pingpong_save_match( $player1, $player2 ) {

	$player1_rankings = wp_parse_args( (array) PingPongDB()->get_rankings( $player1['ID'] ), pingpong_rankings_schema() );
	$player2_rankings = wp_parse_args( (array) PingPongDB()->get_rankings( $player2['ID'] ), pingpong_rankings_schema() );

	$player1_rankings['matches_played'] ++;
	$player2_rankings['matches_played'] ++;

	$winner = $player1['scores']['games'] > $player2['scores']['games'] ? 'player1' : 'player2';

	if ( $winner == 'player1' ) {

		$player1_rankings['matches_won'] ++;
		$player2_rankings['matches_lost'] ++;

	} else {

		$player2_rankings['matches_won'] ++;
		$player1_rankings['matches_lost'] ++;
	}

	$player1_rankings['points_won']  = $player1_rankings['points_won'] + $player1['scores']['points'];
	$player1_rankings['points_lost'] = $player1_rankings['points_lost'] + $player2['scores']['points'];

	$player2_rankings['points_won']  = $player2_rankings['points_won'] + $player2['scores']['points'];
	$player2_rankings['points_lost'] = $player2_rankings['points_lost'] + $player1['scores']['points'];

	$player1_rankings['games_won']  = $player1_rankings['games_won'] + $player1['scores']['games'];
	$player1_rankings['games_lost'] = $player1_rankings['games_lost'] + $player2['scores']['games'];

	$player2_rankings['games_won']  = $player2_rankings['games_won'] + $player2['scores']['games'];
	$player2_rankings['games_lost'] = $player2_rankings['games_lost'] + $player1['scores']['games'];

	PingPongDB()->update_player_rankings( $player1['ID'], $player1_rankings );
	PingPongDB()->update_player_rankings( $player2['ID'], $player2_rankings );
}