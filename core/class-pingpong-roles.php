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

		if ( $overall_points = get_user_meta( $user->ID, 'player_overall_points', true ) ) {

			$overall_points = esc_attr( $overall_points );

		} else {

			$overall_points = __( 'No points yet', 'pingpong' );
		}

		?>
		<h3>
			<?php _e( 'Player Points', 'pingpong' ); ?>
		</h3>

		<table class="form-table">

			<tr>
				<th>
					<?php _e( 'Overall Points', 'pingpong' ); ?>
				</th>

				<td>
					<?php echo $overall_points; ?>
				</td>
			</tr>

		</table>
		<?php
	}
}