<?php
/**
 * Creates the Ping Pong roles.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PingPong_Roles
 *
 * Creates the Ping Pong roles.
 *
 * @since {{VERSION}}
 */
class PingPong_Roles {

	/**
	 * PingPong_Roles constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_action( 'init', array( $this, 'add_roles' ) );
		add_action( 'show_user_profile', array( $this, 'add_user_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'add_user_fields' ) );

	}

	/**
	 * Returns the custom roles.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	private static function get_roles() {

		$subscriber = get_role( 'subscriber' );

		/**
		 * All ping pong custom roles.
		 *
		 * @since {{VERSION}}
		 */
		$roles = apply_filters( 'pingpong_roles', array(
			'player' => array(
				'name'         => __( 'Player', 'pingpong' ),
				'capabilities' => $subscriber->capabilities,
			),
		) );

		return $roles;
	}

	/**
	 * Adds the custom roles.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function add_roles() {

		$roles = self::get_roles();

		foreach ( (array) $roles as $role_ID => $role ) {

			if ( ! get_role( $role_ID ) ) {

				add_role( $role_ID, $role['name'], $role['capabilities'] );
			}
		}
	}

	/**
	 * Removes the custom roles.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	static function remove_roles() {

		$roles = self::get_roles();

		foreach ( (array) $roles as $role_ID => $role ) {

			if ( get_role( $role_ID ) ) {

				remove_role( $role_ID );
			}
		}
	}

	/**
	 * Show users fields.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param WP_User $user
	 */
	function add_user_fields( $user ) {

		if ( ! in_array( 'player', $user->roles ) ) {

			return;
		}

		$player_scores = (array) get_user_meta( $user->ID, 'pingpong_scores', true );

		$games = array();
		foreach ( $player_scores as $match_ID => $game_score ) {

			if ( ! ( $match_title = get_the_title( $match_ID ) ) ) {

				continue;
			}

			$games[ $match_title ] = $game_score;
		}

		$games_total = array_sum( $games );

		?>
		<h3>
			<?php _e( 'Player Points', 'pingpong' ); ?>
		</h3>

		<table class="form-table">

			<tr>
				<th>
					<?php _e( 'Games Won', 'pingpong' ); ?>
				</th>

				<td>
					<span class="pingpong-won-games-total">
						<?php echo $games_total; ?>
					</span>

					<a href="#" data-expand-games>
						<?php _e( 'View Matches', 'pingpong' ); ?>
					</a>

					<table id="pingpong-won-games" style="display: none;">
						<thead>
						<tr>
							<th class="pingpong-won-games-title">
								<?php _e( 'Match Name', 'pingpong' ); ?>
							</th>

							<th class="pingpong-won-games-score">
								<?php _e( 'Score', 'pingpong' ); ?>
							</th>
						</tr>
						</thead>

						<tbody>
						<?php foreach ( $games as $game_title => $game_score ) : ?>
							<tr>
								<td class="pingpong-won-games-title">
									<?php echo $game_title; ?>
								</td>

								<td class="pingpong-won-games-score">
									<?php echo $game_score; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</td>
			</tr>

		</table>
		<?php
	}
}