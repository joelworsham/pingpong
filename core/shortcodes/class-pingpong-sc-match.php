<?php
/**
 * Shortcode: Match
 *
 * @since {{VERSION}}
 *
 * @package PingPong
 * @subpackage PingPong/core/shortcodes
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PingPong_SC_Match
 *
 * Interface for plays to log a match.
 *
 * @since {{VERSION}}
 *
 * @package PingPong
 * @subpackage PingPong/core/shortcodes
 */
class PingPong_SC_Match extends PingPong_Shortcode {

	/**
	 * Whether or not this shortcode was used.
	 *
	 * @since {{VERSION}}
	 *
	 * @var bool
	 */
	private $used = false;

	/**
	 * PingPong_SC_Match constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		parent::__construct( 'pingpong_match' );

		session_start();
	}

	/**
	 * Shortcode output.
	 *
	 * @since {{VERSION}}
	 */
	public function shortcode() {

		if ( ! is_user_logged_in() ) {

			echo 'Must be logged in to start a match.';
		}

		$page = isset( $_POST['page'] ) ? $_POST['page'] : 'intro';

		// Only allow approved pages
		if ( ! in_array( $page, array(
			'intro',
			'setup',
			'play',
			'end',
		) )
		) {

			return;
		}

		// Save settings to session
		if ( isset( $_POST['match_total_games'] ) ) {

			$player1_user = wp_get_current_user();
			$player2_user = new WP_User( $_POST['match_opponent_player'] );

			$player1 = array(
				'name' => $player1_user->display_name,
				'ID'   => $player1_user->ID,
			);

			$player2 = array(
				'name' => $player2_user->display_name,
				'ID'   => $player2_user->ID,
			);

			$_SESSION['match_settings'] = array(
				'total_games'     => $_POST['match_total_games'],
				'points'          => $_POST['match_points'],
				'opponent_player' => $_POST['match_opponent_player'],
				'opponent_team'   => $_POST['match_opponent_team'],
				'player1'         => $player1,
				'player2'         => $player2,
			);
		}

		if ( isset( $_SESSION['match_settings'] ) ) {

			$settings = $_SESSION['match_settings'];
		}

		if ( isset( $_POST['match_player_1_score'] ) ) {

			$_SESSION['match_games'][] = array(
				'player1' => $_POST['match_player_1_score'],
				'player2' => $_POST['match_player_2_score'],
			);
		}

		switch ( $page ) {

			case 'intro':

				$_SESSION['match_settings'] = array();
				$_SESSION['match_games']    = array();

				break;

			case 'setup':

				$players = get_users( array(
					'role' => 'player',
				) );

				$player_options = array();

				if ( $players && ! is_wp_error( $players ) ) {

					foreach ( $players as $player ) {

						$player_options[ $player->ID ] = $player->data->display_name;
					}
				}

				$teams = get_posts( array(
					'post_type'   => 'team',
					'numberposts' => - 1,
				) );

				$team_options = array();

				if ( $teams && ! is_wp_error( $teams ) ) {

					$team_options = wp_list_pluck( $teams, 'post_title', 'ID' );
				}

				break;

			case 'play':

				$game = (int) $_POST['game'];

				break;

			case 'end':

				$games = $_SESSION['match_games'];

				$scores = array(
					'player1' => array(
						'points' => 0,
						'games'  => 0,
					),
					'player2' => array(
						'points' => 0,
						'games'  => 0,
					),
				);

				foreach ( $games as $game ) {

					$scores['player1']['points'] = $scores['player1']['points'] + $game['player1'];
					$scores['player2']['points'] = $scores['player2']['points'] + $game['player2'];

					$scores[ $game['player1'] > $game['player2'] ? 'player1' : 'player2' ]['games'] ++;
				}

				$winner = $scores['player1']['games'] > $scores['player2']['games'] ? 'player1' : 'player2';

				pingpong_save_match(
					array(
						'ID'     => $settings['player1']['ID'],
						'scores' => $scores['player1'],
					),
					array(
						'ID'     => $settings['player2']['ID'],
						'scores' => $scores['player2'],
					)
				);

				unset( $_SESSION['match_games'] );
				unset( $_SESSION['match_settings'] );

				$_POST = array();

				break;
		}

		if ( file_exists( PINGPONG_DIR . "core/views/sc-match/match-{$page}.php" ) ) {
			?>
			<div id="pingpong-match" class="match-page-<?php echo esc_attr( $page ); ?>">
				<form class="pingpong-match-form" method="post">
					<?php include_once PINGPONG_DIR . "core/views/sc-match/match-{$page}.php"; ?>
				</form>
			</div>
			<?php
		}
	}
}