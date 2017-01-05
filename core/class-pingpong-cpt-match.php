<?php
/**
 * Creates the Match custom post type.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PingPong_CPT_Match
 *
 * Creates the Match custom post type.
 *
 * @since {{VERSION}}
 */
class PingPong_CPT_Match extends RBM_CPT {

	public $post_type = 'match';
	public $icon = 'flag';
	public $post_args;
	public $supports = array( 'title', 'editor', 'thumbnail' );

	/**
	 * PingPong_CPT_Match constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		$this->label_singular = __( 'Match', 'pingpong' );
		$this->label_plural   = __( 'Matches', 'pingpong' );

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

		add_meta_box(
			'match-game-settings',
			__( 'Game Settings', 'pingpong' ),
			array( $this, 'mb_game_settings' ),
			$this->post_type,
			'side'
		);

		add_meta_box(
			'match-type',
			__( 'Match Type', 'pingpong' ),
			array( $this, 'mb_match_type' ),
			$this->post_type,
			'side'
		);

		add_meta_box(
			'match-settings-singles',
			__( 'Match Settings', 'pingpong' ),
			array( $this, 'mb_match_settings_singles' ),
			$this->post_type
		);

		add_meta_box(
			'match-settings-doubles',
			__( 'Match Settings', 'pingpong' ),
			array( $this, 'mb_match_settings_doubles' ),
			$this->post_type
		);

		add_meta_box(
			'match-settings-team',
			__( 'Match Settings', 'pingpong' ),
			array( $this, 'mb_match_settings_team' ),
			$this->post_type
		);
	}

	/**
	 * Outputs the metabox for Game Settings
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_game_settings() {

		rbm_do_field_number( 'games', __( 'Number of Games', 'pingpong' ), false, array(
			'default' => 1,
			'min'     => 0,
		) );
	}

	/**
	 * Outputs the metabox for Match Type
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_match_type() {

		rbm_do_field_radio( 'type', false, false, array(
			'default' => 'singles',
			'options' => array(
				'singles' => __( 'Singles Match', 'pingpong' ),
				'team'    => __( 'Team Match', 'pingpong' ),
				'doubles' => __( 'Doubles Match', 'pingpong' ),
			),
		) );
	}

	/**
	 * Singles match settings.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_match_settings_singles() {

		$users = get_users( array(
			'role' => 'player',
		) );

		$user_options = array();

		if ( $users && ! is_wp_error( $users ) ) {

			foreach ( $users as $user ) {

				$user_options[ $user->ID ] = $user->data->display_name;
			}
		}

		rbm_do_field_select( 'singles_players', __( 'Players', 'pingpong' ), false, array(
			'options'       => $user_options,
			'wrapper_class' => 'player-select',
			'multiple'      => true,
			'input_class'   => 'rbm-select2',
		) );
	}

	/**
	 * Two person team match settings.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_match_settings_team() {

		$teams = get_posts( array(
			'post_type'   => 'team',
			'numberposts' => - 1,
		) );

		$team_options = array();

		if ( $teams && ! is_wp_error( $teams ) ) {

			$team_options = wp_list_pluck( $teams, 'post_title', 'ID' );
		}

		rbm_do_field_select( 'teams', __( 'Teams', 'pingpong' ), false, array(
			'options'     => $team_options,
			'multiple'    => true,
			'input_class' => 'rbm-select2',
		) );
	}

	/**
	 * Doubles match settings.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_match_settings_doubles() {

		echo 'Doubles';
	}
}