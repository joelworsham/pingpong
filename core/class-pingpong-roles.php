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
}