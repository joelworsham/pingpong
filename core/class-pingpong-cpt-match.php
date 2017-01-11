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

		add_filter( 'pingpong_admin_script_data', array( $this, 'add_data' ) );
		add_action( 'current_screen', array( $this, 'lock_completed' ) );
		add_filter( 'admin_body_class', array( $this, 'admin_body_classes' ) );
		add_action( 'admin_notices', array( $this, 'match_notices' ) );
		add_action( 'pre_get_posts', array( $this, 'remove_from_all' ) );
		add_action( 'init', array( $this, 'add_post_statuses' ) );
		add_action( 'admin_footer-post.php', array( $this, 'add_post_statuses_to_dropdown' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'add_publish_sections' ) );
		add_action( 'admin_footer', array( $this, 'scores_modal' ) );
		add_action( 'wp_ajax_pingpong_get_team_players', array( $this, 'ajax_get_teams_players' ) );
		add_action( 'wp_ajax_pingpong_submit_scores', array( $this, 'ajax_submit_scores' ) );
		add_action( 'wp_ajax_pingpong_get_match_scores', array( $this, 'ajax_get_match_scores' ) );
	}

	/**
	 * Adds javascript data.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param array $data
	 *
	 * @return mixed
	 */
	function add_data( $data ) {

		$data['match_ID'] = isset( $_GET['post'] ) && get_post_type( $_GET['post'] ) == 'match' ? $_GET['post'] : null;

//		$scores = get_post_meta( $_GET['post'], 'pingpong_scores', true );
//		$data['scores']   = $scores;

		return $data;
	}

	/**
	 * Locks editing completed matches.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param WP_Screen $screen
	 */
	function lock_completed( $screen ) {

		if ( $screen->base != 'post' || $screen->id != 'match' ) {

			return;
		}

		if ( ! isset( $_GET['match_edit_completed'] ) && get_post_status( $_GET['post'] ) == 'completed' ) {

			wp_die( __( 'You cannot edit this match because it has been completed.', 'pingpong' ) );
		}
	}

	/**
	 * Add some classes.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param string $classes
	 *
	 * @return mixed
	 */
	function admin_body_classes( $classes ) {

		if ( isset( $_GET['post_status'] ) && $_GET['post_status'] == 'match_complete' ) {

			$classes .= ' pingpong-matches-complete';
		}

		return $classes;
	}

	/**
	 * Shows match notices.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function match_notices() {

		if ( isset( $_GET['pingpong_match_saved'] ) ) {
			?>
			<div class="notice updated">
				<p>
					<?php _e( 'Successfully saved match scores and set to completed.', 'pingpong' ); ?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Remove from All list table.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param WP_Query $query
	 */
	function remove_from_all( $query ) {

		if ( ! is_admin() || ! $query->is_main_query() ) {

			return;
		}

		$screen = get_current_screen();

		if ( $screen->id != 'edit-match' ||
		     ( isset( $_GET['post_status'] ) && $_GET['post_status'] != 'all' )
		) {

			return;
		}

		$post_statuses = get_post_stati( array(
			'show_in_admin_all_list'    => true,
		) );

		$query->set( 'post_status', $post_statuses );
	}

	/**
	 * Adds custom post statuses.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function add_post_statuses() {

		global $wp_post_statuses;

		register_post_status( 'match_complete', array(
			'label'                     => __( 'Completed', 'pingpong' ),
			'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'pingpong' ),
			'exclude_from_search'       => get_post_type_object( 'match' )->exclude_from_search,
			'public'                    => get_post_type_object( 'match' )->public,
			'publicly_queryable'        => get_post_type_object( 'match' )->publicly_queryable,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => false,
		) );
	}

	/**
	 * Add custom statuses to dropdown (messy, but all that's available now).
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function add_post_statuses_to_dropdown() {

		if ( get_post_type() != 'match' ) {

			return;
		}

		$custom_statuses = array(
			'match_complete' => __( 'Completed', 'pingpong' ),
		);
		?>
		<script type="text/javascript">
			(function ($) {

				$(function () {

					var $status_select = $('#post_status'),
						$status_display = $('#post-status-display');

					<?php foreach ( $custom_statuses as $status => $status_label ) : ?>
					$status_select.append('<option value="<?php echo $status; ?>"><?php echo $status_label; ?></option>');

					<?php if ( $status == get_post_status() ) : ?>
					$status_select.find('option[value="<?php echo $status; ?>"]').prop('selected', true);
					$status_display.html('<?php echo $status_label; ?>');
					<?php endif; ?>
					<?php endforeach; ?>
				});
			})(jQuery);
		</script>
		<?php

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
			'match-league',
			__( 'Match League', 'pingpong' ),
			array( $this, 'mb_match_league' ),
			$this->post_type,
			'side'
		);

		add_meta_box(
			'match-settings-singles',
			__( 'Match Settings Singles', 'pingpong' ),
			array( $this, 'mb_match_settings_singles' ),
			$this->post_type
		);

		add_meta_box(
			'match-settings-doubles',
			__( 'Match Settings Doubles', 'pingpong' ),
			array( $this, 'mb_match_settings_doubles' ),
			$this->post_type
		);

		add_meta_box(
			'match-settings-team',
			__( 'Match Settings Team', 'pingpong' ),
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

		rbm_do_field_select( 'games', __( 'Number of Games', 'pingpong' ), false, array(
			'options' => array(
				3 => 3,
				5 => 5,
				7 => 7,
			),
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
	 * Outputs the metabox for the Match League.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_match_league() {

		$leagues = get_posts( array(
			'post_type'   => 'league',
			'numberposts' => - 1,
		) );

		$league_options = array(
			'' => __( '- No League -', 'pingpong' ),
		);

		if ( $leagues && ! is_wp_error( $leagues ) ) {

			$league_options = array_merge( $league_options, wp_list_pluck( $leagues, 'post_title', 'ID' ) );
		}

		rbm_do_field_select( 'league', false, false, array(
			'options'     => $league_options,
			'input_class' => 'rbm-select2',
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
			'input_atts'  => array(
				'data-maximum-selection-length' => 2,
			),
		) );
	}

	/**
	 * Doubles match settings.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_match_settings_doubles() {

		$teams = get_posts( array(
			'post_type'   => 'team',
			'numberposts' => - 1,
		) );

		$team_options = array();

		if ( $teams && ! is_wp_error( $teams ) ) {

			$team_options = wp_list_pluck( $teams, 'post_title', 'ID' );
		}

		rbm_do_field_select( 'doubles_teams', __( 'Teams', 'pingpong' ), false, array(
			'options'     => $team_options,
			'multiple'    => true,
			'input_class' => 'rbm-select2',
			'input_atts'  => array(
				'data-maximum-selection-length' => 2,
			),
		) );
	}

	/**
	 * Outputs any misc publishing sections.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function add_publish_sections() {

		// Add scores
		?>
		<div class="misc-pub-section pingpong-add-scores">
			<button type="button" class="button" data-scores-open>
				<?php _e( 'Add Scores', 'pingpong' ); ?>
			</button>
		</div>
		<?php
	}

	/**
	 * Outputs the scores modal.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function scores_modal() {

		$screen = get_current_screen();

		if ( $screen->id != 'edit-match' &&
		     ( $screen->id != 'match' && $screen->base != 'post' )
		) {

			return;
		}

		?>
		<div id="pingpong-scores-modal" style="display: none;">
			<div class="pingpong-scores-container">

				<div class="pingpong-scores-content">
					<p class="pingpong-scores-title">
						<?php _e( 'Match Scores', 'pingpong' ); ?>
					</p>

					<div class="pingpong-scores-error notice error inline" style="display: none;">
						<p></p>
					</div>

					<table class="pingpong-scores-table">

						<thead></thead>

						<tbody></tbody>

						<tfoot></tfoot>

					</table>

				</div>

				<div class="pingpong-scores-actions">

					<?php if ( $screen->id == 'match' && $screen->base == 'post' ) : ?>
						<button type="button" class="pingpong-scores-submit button button-primary button-large"
						        data-scores-submit>
							<?php _e( 'Submit Scores and End Match', 'pingpong' ); ?>
						</button>
					<?php endif; ?>

					<a href="#" class="pingpong-scores-close" data-scores-close>
						<span class="dashicons dashicons-no"></span>
					</a>
				</div>

				<div class="pingpong-scores-cover"><span class="spinner is-active"></span></div>

			</div>
		</div>
		<?php
	}

	/**
	 * AJAX callback for getting teams' players.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function ajax_get_teams_players() {

		$team_IDs = $_POST['team_IDs'];

		$players = array();

		foreach ( $team_IDs as $team_ID ) {

			$team = array(
				'name'    => get_the_title( $team_ID ),
				'id'      => $team_ID,
				'players' => array(),
			);

			if ( $team_players = pingpong_get_team_players( $team_ID ) ) {

				foreach ( $team_players as $team_player_ID ) {

					$user = new WP_User( $team_player_ID );

					$team['players'][] = array(
						'name' => $user->display_name,
						'id'   => $team_player_ID,
					);
				}

				$players[] = $team;
			}
		}

		wp_send_json_success( $players );
	}

	/**
	 * AJAX callback for submiting scores.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function ajax_submit_scores() {

		$match_ID = $_POST['match_ID'];
		$scores   = $_POST['scores'];

		unset( $scores['players']['length'] );
		unset( $scores['teams']['length'] );

		foreach ( $scores['players'] as $player_ID => $player_score ) {

			if ( ! ( $player_scores = get_user_meta( $player_ID, 'pingpong_scores', true ) ) ) {

				$player_scores = array();
			}

			$player_scores[ $match_ID ] = $player_score;

			update_user_meta( $player_ID, 'pingpong_scores', $player_scores );
			update_user_meta( $player_ID, 'pingpong_games', $player_score );
		}

		foreach ( $scores['teams'] as $team_ID => $team_score ) {

			if ( ! ( $team_scores = get_post_meta( $team_ID, 'pingpong_scores', true ) ) ) {

				$team_scores = array();
			}

			$team_scores[ $match_ID ] = $team_score;

			update_post_meta( $team_ID, 'pingpong_scores', $team_scores );
		}

		update_post_meta( $match_ID, 'pingpong_scores', $scores );

		// Set to passed
		wp_update_post( array(
			'ID'          => $match_ID,
			'post_status' => 'match_complete',
		) );

		wp_send_json_success( array(
			'redirect' => admin_url( 'edit.php?post_status=match_complete&post_type=match&pingpong_match_saved' ),
		) );
	}

	/**
	 * AJAX callback for getting a match's score.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function ajax_get_match_scores() {

		$match_ID = $_POST['match_id'];

		$scores     = get_post_meta( $match_ID, 'pingpong_scores', true );
		$match_type = get_post_meta( $match_ID, '_rbm_type', true );

		$players = array();
		$teams   = array();

		foreach ( $scores['players'] as $player_ID => $score ) {

			$user = new WP_User( $player_ID );

			$players[] = array(
				'name' => $user->display_name,
				'id'   => $player_ID,
			);
		}

		foreach ( $scores['teams'] as $team_ID => $score ) {

			$team_player_IDs = pingpong_get_team_players( $team_ID );
			$team_players = array();

			foreach ( (array) $team_player_IDs as $player_ID ) {

				$user = new WP_User( $player_ID );

				$team_players[] = array(
					'name' => $user->display_name,
					'id'   => $player_ID,
				);
			}

			$teams[] = array(
				'name'    => get_the_title( $team_ID ),
				'id'      => $team_ID,
				'players' => $team_players,
			);
		}

		$scores['players'] = $players;
		$scores['teams']   = $teams;

		wp_send_json_success( array(
			'scores' => $scores,
			'type'   => $match_type,
		) );
	}
}