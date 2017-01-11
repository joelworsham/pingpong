<?php
/**
 * Creates the Team custom post type.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PingPong_CPT_Team
 *
 * Creates the Team custom post type.
 *
 * @since {{VERSION}}
 */
class PingPong_CPT_Team extends RBM_CPT {

	public $post_type = 'team';
	public $icon = 'groups';
	public $post_args;
	public $supports = array( 'title', 'editor', 'thumbnail' );

	private $user_options = array();

	/**
	 * PingPong_CPT_Team constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		$this->label_singular = __( 'Team', 'pingpong' );
		$this->label_plural   = __( 'Teams', 'pingpong' );

		parent::__construct();

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
	}

	/**
	 * Adds the custom post type meta boxes.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function add_meta_boxes() {

		$users = get_users( array(
			'role' => 'player',
		) );

		if ( $users && ! is_wp_error( $users ) ) {

			foreach ( $users as $user ) {

				$this->user_options[ $user->ID ] = $user->data->display_name;
			}
		}

		add_meta_box(
			'team-players',
			__( 'Team Players', 'pingpong' ),
			array( $this, 'mb_team_players' ),
			$this->post_type
		);
	}

	/**
	 * Metabox for team players.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_team_players() {

		rbm_do_field_select( 'players', false, false, array(
			'options'  => $this->user_options,
			'multiple' => true,
			'input_class'   => 'rbm-select2',
			'input_atts'  => array(
				'data-maximum-selection-length' => 2,
			),
		) );
	}
}